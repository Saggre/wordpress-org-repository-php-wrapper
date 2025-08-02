<?php

namespace Saggre\WordPress\Repository\Test\Unit\Util;

use PHPUnit\Framework\TestCase;
use Saggre\WordPress\Repository\Util\Path;

class PathTest extends TestCase
{
    public static function dataProviderTestNormalize(): iterable
    {
        return [
            [
                'expected' => 'foo/bar/baz',
                'separator' => '/',
                'path' => 'foo\\bar\\baz',
            ],
            [
                'expected' => 'foo/bar/baz',
                'separator' => '/',
                'path' => 'foo/bar/baz',
            ],
            [
                'expected' => 'foo\\bar\\baz',
                'separator' => '\\',
                'path' => 'foo/bar/baz',
            ],
            [
                'expected' => '\\foo\\bar\\baz',
                'separator' => '\\',
                'path' => '\\foo\\bar\\baz',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestNormalize
     */
    public function testNormalize(string $expected, string $separator, string $path)
    {
        $pathService = new Path($separator);
        self::assertEquals($expected, $pathService->normalize($path));
    }

    public static function dataProviderTestExplode(): iterable
    {
        return [
            [
                'expected' => ['foo', 'bar', 'baz'],
                'separator' => '/',
                'path' => 'foo/bar/baz',
            ],
            [
                'expected' => ['foo', 'bar', 'baz'],
                'separator' => '/',
                'path' => 'foo\\bar\\baz',
            ],
            [
                'expected' => ['foo', 'bar', 'baz'],
                'separator' => '\\',
                'path' => 'foo/bar/baz',
            ],
            [
                'expected' => ['foo', 'bar', 'baz'],
                'separator' => '\\',
                'path' => '\\foo\\bar\\baz',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestExplode
     */
    public function testExplode(array $expected, string $separator, string $path)
    {
        $pathService = new Path($separator);
        self::assertEquals($expected, $pathService->explode($path));
    }

    public static function dataProviderTestJoin(): iterable
    {
        return [
            [
                'expected' => 'foo/bar/baz',
                'separator' => '/',
                'parts' => ['foo', 'bar', 'baz'],
            ],
            [
                'expected' => '/foo/bar/baz',
                'separator' => '/',
                'parts' => ['/foo/bar', 'baz'],
            ],
            [
                'expected' => 'foo\\bar\\baz',
                'separator' => '\\',
                'parts' => ['foo', 'bar', 'baz'],
            ],
            [
                'expected' => '\\foo\\bar\\baz',
                'separator' => '\\',
                'parts' => ['\\foo\\bar', 'baz'],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestJoin
     */
    public function testJoin(string $expected, string $separator, array $parts)
    {
        $pathService = new Path($separator);
        self::assertEquals($expected, $pathService->join(...$parts));
    }
}
