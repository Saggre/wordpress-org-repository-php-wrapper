<?php

namespace Saggre\WordPress\Repository\Test\Unit\Model;

use Saggre\WordPress\Repository\Model\PluginInfo;
use Saggre\WordPress\Repository\Model\PluginQueryResult;
use Saggre\WordPress\Repository\Test\Unit\UnitTestCase;

class PluginQueryResultTest extends UnitTestCase
{
    public function testFromArrayReadsPage()
    {
        $result = PluginQueryResult::fromArray($this->getJsonFixture('query_plugins.json'));

        self::assertSame(1, $result->page);
        self::assertSame(23931, $result->pages);
        self::assertSame(71793, $result->results);
        self::assertCount(3, $result->plugins);
        self::assertContainsOnlyInstancesOf(PluginInfo::class, $result->plugins);
    }

    public function testFromArrayReadsPlugins()
    {
        $result = PluginQueryResult::fromArray($this->getJsonFixture('query_plugins.json'));
        $plugin = $result->plugins[0];

        self::assertSame('immowp-gestion-immobiliere', $plugin->slug);
        self::assertSame('2.0.3', $plugin->version);
        self::assertSame('2026-09-07T16:21:00+00:00', $plugin->lastUpdated->format(DATE_ATOM));
        self::assertSame(['immowp'], array_keys($plugin->contributors));
        self::assertArrayHasKey('real-estate', $plugin->tags);
    }

    public function testFromArrayReadsEmptyPage()
    {
        $result = PluginQueryResult::fromArray([
            'info' => ['page' => 2, 'pages' => 0, 'results' => 0],
            'plugins' => [],
        ]);

        self::assertSame([], $result->plugins);
        self::assertSame(2, $result->page);
        self::assertSame(0, $result->results);
    }
}
