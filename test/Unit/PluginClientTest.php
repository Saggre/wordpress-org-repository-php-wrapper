<?php

namespace Saggre\WordPress\Repository\Test\Unit;

use Sabre\DAV\Client;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Exception\TagNotFoundException;
use Saggre\WordPress\Repository\Model\LogEntry;
use Saggre\WordPress\Repository\Model\LogPath;
use Saggre\WordPress\Repository\Model\LogPathAction;
use Saggre\WordPress\Repository\PluginClient;
use Saggre\WordPress\Repository\Test\Unit\Stub\DavClientStub;

class PluginClientTest extends UnitTestCase
{
    /**
     * Build a client that talks to a stub instead of the live repository.
     *
     * @param DavClientStub $dav
     * @param string $slug
     * @return PluginClient
     */
    protected function createPluginClient(DavClientStub $dav, string $slug = 'demo-plugin'): PluginClient
    {
        return new class ($dav, new PluginClientConfig($slug)) extends PluginClient {
            public function __construct(protected DavClientStub $dav, PluginClientConfig $config)
            {
                parent::__construct($config);
            }

            protected function createClient(): Client
            {
                return $this->dav;
            }
        };
    }

    /**
     * Build a client replaying the tag history, then the diff of a version range.
     *
     * @return array{PluginClient, DavClientStub}
     */
    protected function createDiffClient(): array
    {
        $dav = (new DavClientStub())
            ->willRespondWith($this->getFixture('tag_revisions.xml'))
            ->willRespondWith($this->getFixture('version_diff.xml'));

        return [$this->createPluginClient($dav), $dav];
    }

    public function testGetChangedPathsRequestsTheRevisionRange()
    {
        $dav = (new DavClientStub())->willRespondWith($this->getFixture('version_diff.xml'));

        $log = $this->createPluginClient($dav)->getChangedPaths(120, 115, 'trunk/includes', 10);
        $body = $dav->getRequestBody(0);

        self::assertCount(1, $dav->requests);
        self::assertSame('REPORT', $dav->requests[0]->getMethod());
        self::assertSame('https://plugins.svn.wordpress.org/demo-plugin', $dav->requests[0]->getUrl());
        self::assertStringContainsString('<S:start-revision>120</S:start-revision>', $body);
        self::assertStringContainsString('<S:end-revision>115</S:end-revision>', $body);
        self::assertStringContainsString('<S:limit>10</S:limit>', $body);
        self::assertStringContainsString('<S:path>trunk/includes</S:path>', $body);
        self::assertSame([120, 115], array_column($log, 'revision'));
    }

    public function testGetChangedPathsRejectsAnInvertedRangeBeforeSending()
    {
        $dav = new DavClientStub();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The start revision 115 is older than the end revision 120.');

        try {
            $this->createPluginClient($dav)->getChangedPaths(115, 120);
        } finally {
            self::assertSame([], $dav->requests, 'The request reached the network.');
        }
    }

    public function testGetChangedPathsReportsFailedRequests()
    {
        $dav = (new DavClientStub())->willRespondWith('Not Found', 404);

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unable to read the commit log of "/demo-plugin".');
        $this->expectExceptionCode(404);

        $this->createPluginClient($dav)->getChangedPaths(120, 115);
    }

    public function testGetTagRevisionsMapsVersionsToTheirRevisions()
    {
        $dav = (new DavClientStub())->willRespondWith($this->getFixture('tag_revisions.xml'));

        $tags = $this->createPluginClient($dav)->getTagRevisions();

        self::assertContainsOnlyInstancesOf(LogEntry::class, $tags);
        self::assertSame(['1.2.0', '1.9.0', '1.10.4', '1.1.9'], array_keys($tags));
        self::assertSame(120, $tags['1.10.4']->revision);
        self::assertStringContainsString('<S:path>tags</S:path>', $dav->getRequestBody(0));
    }

    public function testGetTagRevisionsOrdersAVersionSeriesByRevision()
    {
        $dav = (new DavClientStub())->willRespondWith($this->getFixture('tag_revisions.xml'));

        $revisions = array_keys($this->createPluginClient($dav)->getTagRevisions());

        self::assertGreaterThan(
            array_search('1.9.0', $revisions, true),
            array_search('1.10.4', $revisions, true),
            'Versions are sorted as text, so 1.10.4 lands before 1.9.0.'
        );
    }

    public function testGetTagRevisionsResolvesARecreatedTagToItsLatestCopy()
    {
        $dav = (new DavClientStub())->willRespondWith($this->getFixture('tag_revisions.xml'));

        $tag = $this->createPluginClient($dav)->getTagRevisions()['1.1.9'];

        self::assertSame(130, $tag->revision);
        self::assertSame(129, $tag->paths[0]->copyFromRevision);
        self::assertSame('/demo-plugin/trunk', $tag->paths[0]->copyFromPath);
    }

    public function testDiffVersionsReadsTheRangeBetweenTwoTags()
    {
        [$client, $dav] = $this->createDiffClient();

        $client->diffVersions('1.9.0', '1.10.4');

        self::assertCount(2, $dav->requests, 'The diff took more than one report beyond the tags.');
        self::assertStringContainsString('<S:start-revision>120</S:start-revision>', $dav->getRequestBody(1));
        self::assertStringContainsString('<S:end-revision>111</S:end-revision>', $dav->getRequestBody(1));
    }

    public function testDiffVersionsDeduplicatesTrunkAndTagPaths()
    {
        [$client] = $this->createDiffClient();

        $paths = $client->diffVersions('1.9.0', '1.10.4');

        self::assertContainsOnlyInstancesOf(LogPath::class, $paths);
        self::assertSame([
            'admin/settings.php',
            'includes/new-feature.php',
            'includes/old.php',
            'readme.txt',
            'style.css',
        ], array_keys($paths));
        self::assertSame('readme.txt', $paths['readme.txt']->path);
        self::assertSame(LogPathAction::Deleted, $paths['includes/old.php']->action);
    }

    public function testDiffVersionsKeepsTheContentChangeOfADuplicatedPath()
    {
        [$client] = $this->createDiffClient();

        $paths = $client->diffVersions('1.9.0', '1.10.4');

        self::assertTrue($paths['style.css']->textMods, 'The property only tag copy hid the trunk edit.');
        self::assertFalse($paths['includes/old.php']->textMods);
    }

    public function testDiffVersionsThrowsOnAnUntaggedVersion()
    {
        [$client] = $this->createDiffClient();

        $this->expectException(TagNotFoundException::class);
        $this->expectExceptionMessage('Version "1.9.1" of "demo-plugin" has no tag in the repository.');

        $client->diffVersions('1.9.1', '1.10.4');
    }
}
