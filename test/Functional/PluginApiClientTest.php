<?php

namespace Saggre\WordPress\Repository\Test\Functional;

use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Model\PluginBrowse;
use Saggre\WordPress\Repository\Model\PluginInfo;
use Saggre\WordPress\Repository\Model\PluginQuery;
use Saggre\WordPress\Repository\PluginApiClient;

class PluginApiClientTest extends FunctionalTestCase
{
    protected PluginApiClient $client;

    protected function setUp(): void
    {
        $this->client = new PluginApiClient();
    }

    public function testQueryPluginsEnumeratesRecentlyUpdatedPlugins()
    {
        $result = $this->client->queryPlugins(new PluginQuery(
            browse: PluginBrowse::Updated,
            page: 1,
            perPage: 3,
        ));

        self::assertSame(1, $result->page);
        self::assertGreaterThan(0, $result->pages);
        self::assertGreaterThan(0, $result->results);
        self::assertCount(3, $result->plugins);
        self::assertContainsOnlyInstancesOf(PluginInfo::class, $result->plugins);

        $timestamps = [];

        foreach ($result->plugins as $plugin) {
            self::assertNotNull($plugin->lastUpdated, "{$plugin->slug} has no parsed last update.");
            $timestamps[] = $plugin->lastUpdated->getTimestamp();
        }

        // The head of this list is eventually consistent, so entries settle into place over the
        // following minutes and their order is not asserted. The newest entry still dates the page
        // and separates this browse mode from the others, whose newest release is days old.
        self::assertGreaterThan(time() - 86400, max($timestamps), 'The page is not of recent updates.');
    }

    public function testQueryPluginsTrimsAndEnrichesThePayload()
    {
        $result = $this->client->queryPlugins(new PluginQuery(
            browse: PluginBrowse::Updated,
            perPage: 1,
            fields: [
                'sections' => false,
                'description' => false,
                'screenshots' => false,
                'icons' => false,
                'contributors' => true,
            ],
        ));

        $plugin = $result->plugins[0];

        self::assertSame([], $plugin->sections);
        self::assertArrayNotHasKey('description', $plugin->raw);
        self::assertArrayNotHasKey('screenshots', $plugin->raw);
        self::assertArrayNotHasKey('icons', $plugin->raw);
        self::assertNotEmpty($plugin->contributors);
        self::assertNotNull($plugin->authorProfile);
        self::assertNotNull($plugin->shortDescription);
    }

    public function testGetPluginInformationReadsTheVersionsMap()
    {
        $info = $this->client->getPluginInformation('hello-dolly');

        self::assertSame('hello-dolly', $info->slug);
        self::assertSame('Hello Dolly', $info->name);
        self::assertSame('2008-07-06', $info->added->format('Y-m-d'));
        self::assertArrayHasKey('matt', $info->contributors);
        self::assertArrayHasKey('1.5', $info->versions);
        self::assertArrayHasKey('1.7.2', $info->versions);
        self::assertSame(
            'https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip',
            $info->versions['1.7.2']
        );
    }

    public function testGetPluginInformationTrimsThePayload()
    {
        $info = $this->client->getPluginInformation('hello-dolly', ['sections' => false]);

        self::assertSame([], $info->sections);
        self::assertNotEmpty($info->versions);
    }

    public function testGetPluginInformationThrowsOnUnknownPlugin()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->client->getPluginInformation('saggre-wordpress-org-repository-php-wrapper-unknown');
    }

    public function testGetPluginStatusReadsAnOpenPlugin()
    {
        $status = $this->client->getPluginStatus('hello-dolly');

        self::assertSame('hello-dolly', $status->slug);
        self::assertFalse($status->closed);
        self::assertNull($status->closedDate);
    }

    public function testGetPluginStatusReadsAClosedPlugin()
    {
        $status = $this->client->getPluginStatus('hana-flv-player');

        self::assertTrue($status->closed);
        self::assertSame('Hana Flv Player', $status->name);
        self::assertSame('2021-06-21', $status->closedDate->format('Y-m-d'));
        self::assertSame('security-issue', $status->reason);
        self::assertSame('Security Issue', $status->reasonText);
    }

    public function testGetPluginStatusThrowsOnUnknownPlugin()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->client->getPluginStatus('saggre-wordpress-org-repository-php-wrapper-unknown');
    }
}
