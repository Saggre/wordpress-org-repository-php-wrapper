<?php

namespace Saggre\WordPress\Repository\Test\Unit\Util;

use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Model\LogEntry;
use Saggre\WordPress\Repository\Model\LogPathAction;
use Saggre\WordPress\Repository\Test\Unit\UnitTestCase;
use Saggre\WordPress\Repository\Util\LogReport;

class LogReportTest extends UnitTestCase
{
    public function testCreateRequestBodyWithoutStartRevision()
    {
        $body = (new LogReport())->createRequestBody(10);

        self::assertStringContainsString('<S:log-report xmlns:S="svn:" xmlns:D="DAV:">', $body);
        self::assertStringNotContainsString('start-revision', $body);
        self::assertStringContainsString('<S:end-revision>0</S:end-revision>', $body);
        self::assertStringContainsString('<S:limit>10</S:limit>', $body);
        self::assertStringContainsString('<S:discover-changed-paths/>', $body);
    }

    public function testCreateRequestBodyWithRevisionRange()
    {
        $body = (new LogReport())->createRequestBody(5, 3383710, 3289318);

        self::assertStringContainsString('<S:start-revision>3383710</S:start-revision>', $body);
        self::assertStringContainsString('<S:end-revision>3289318</S:end-revision>', $body);
    }

    public function testCreateRequestBodyIsValidXml()
    {
        $document = simplexml_load_string((new LogReport())->createRequestBody(1, 2, 3));

        self::assertNotFalse($document);
    }

    protected function getLogEntries(): array
    {
        return (new LogReport())->parseResponse($this->getFixture('log_report.xml'));
    }

    public function testParseResponseReadsRevisions()
    {
        $entries = $this->getLogEntries();

        self::assertCount(2, $entries);
        self::assertContainsOnlyInstancesOf(LogEntry::class, $entries);
        self::assertSame([3383710, 3289318], array_column($entries, 'revision'));
        self::assertSame(['dd32', 'Otto42'], array_column($entries, 'author'));
    }

    public function testParseResponseReadsRevisionDetails()
    {
        $entry = $this->getLogEntries()[1];

        self::assertSame('2025-05-07T16:50:12+00:00', $entry->date->format(DATE_ATOM));
        self::assertSame('Update tested up to value.', $entry->message);
    }

    public function testParseResponseReadsChangedPaths()
    {
        $paths = $this->getLogEntries()[0]->paths;

        self::assertCount(3, $paths);
        self::assertSame('/hello-dolly/tags/1.7.2/readme.txt', $paths[0]->path);
        self::assertSame(LogPathAction::Modified, $paths[0]->action);
        self::assertSame('file', $paths[0]->nodeKind);

        self::assertSame('/hello-dolly/tags/1.7.3', $paths[1]->path);
        self::assertSame(LogPathAction::Deleted, $paths[1]->action);
        self::assertSame('dir', $paths[1]->nodeKind);
        self::assertNull($paths[1]->copyFromPath);
        self::assertNull($paths[1]->copyFromRevision);
    }

    public function testParseResponseReadsCopiedPaths()
    {
        $body = <<<'XML'
        <?xml version="1.0" encoding="utf-8"?>
        <S:log-report xmlns:S="svn:" xmlns:D="DAV:">
        <S:log-item>
        <S:added-path node-kind="dir" copyfrom-path="/hello-dolly/trunk" copyfrom-rev="2995208">/hello-dolly/tags/1.7.3</S:added-path>
        <D:version-name>2995248</D:version-name>
        </S:log-item>
        </S:log-report>
        XML;

        $path = (new LogReport())->parseResponse($body)[0]->paths[0];

        self::assertSame(LogPathAction::Added, $path->action);
        self::assertSame('/hello-dolly/trunk', $path->copyFromPath);
        self::assertSame(2995208, $path->copyFromRevision);
    }

    public function testParseResponseReadsEmptyReport()
    {
        $body = '<?xml version="1.0" encoding="utf-8"?>' . "\n"
            . '<S:log-report xmlns:S="svn:" xmlns:D="DAV:"></S:log-report>';

        self::assertSame([], (new LogReport())->parseResponse($body));
    }

    public function testParseResponseThrowsOnInvalidXml()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unable to parse the SVN log report response.');

        (new LogReport())->parseResponse('<html><body>Service unavailable');
    }
}
