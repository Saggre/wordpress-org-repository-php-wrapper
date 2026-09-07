<?php

namespace Saggre\WordPress\Repository\Test\Unit\Util;

use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Saggre\WordPress\Repository\Util\Date;

class DateTest extends TestCase
{
    protected string $timezone;

    protected function setUp(): void
    {
        // WordPress.org reports UTC, so parsing must not depend on the host timezone.
        $this->timezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Helsinki');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->timezone);
    }

    public static function dataProviderTestParse(): iterable
    {
        return [
            [
                'expected' => '2025-10-24T04:13:00+00:00',
                'value' => '2025-10-24 4:13am GMT',
            ],
            [
                'expected' => '2008-07-06T00:00:00+00:00',
                'value' => '2008-07-06',
            ],
            [
                'expected' => '2025-10-24T04:13:10+00:00',
                'value' => '2025-10-24T04:13:10.489435Z',
            ],
            [
                'expected' => '2025-10-24T02:13:00+00:00',
                'value' => '2025-10-24 04:13:00 +02:00',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestParse
     */
    public function testParse(string $expected, string $value)
    {
        $date = Date::parse($value)->setTimezone(new DateTimeZone('UTC'));

        self::assertSame($expected, $date->format(DATE_ATOM));
    }

    public static function dataProviderTestParseInvalid(): iterable
    {
        return [
            ['value' => null],
            ['value' => ''],
            ['value' => 'not a date'],
        ];
    }

    /**
     * @dataProvider dataProviderTestParseInvalid
     */
    public function testParseInvalid(?string $value)
    {
        self::assertNull(Date::parse($value));
    }
}
