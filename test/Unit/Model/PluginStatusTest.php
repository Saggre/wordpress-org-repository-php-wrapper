<?php

namespace Saggre\WordPress\Repository\Test\Unit\Model;

use Saggre\WordPress\Repository\Model\PluginStatus;
use Saggre\WordPress\Repository\Test\Unit\UnitTestCase;

class PluginStatusTest extends UnitTestCase
{
    public function testFromArrayReadsClosedPlugin()
    {
        $status = PluginStatus::fromArray(
            'hana-flv-player',
            $this->getJsonFixture('plugin_status_closed.json')
        );

        self::assertSame('hana-flv-player', $status->slug);
        self::assertTrue($status->closed);
        self::assertSame('Hana Flv Player', $status->name);
        self::assertSame('2021-06-21', $status->closedDate->format('Y-m-d'));
        self::assertSame('security-issue', $status->reason);
        self::assertSame('Security Issue', $status->reasonText);
    }

    public function testFromArrayReadsOpenPlugin()
    {
        $status = PluginStatus::fromArray(
            'akismet',
            $this->getJsonFixture('plugin_status_open.json')
        );

        self::assertSame('akismet', $status->slug);
        self::assertFalse($status->closed);
        self::assertStringContainsString('Akismet', $status->name);
        self::assertNull($status->closedDate);
        self::assertNull($status->reason);
        self::assertNull($status->reasonText);
    }
}
