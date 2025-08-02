<?php

namespace Saggre\WordPress\Repository\Test\Functional;

use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToListContents;
use Saggre\WordPress\Repository\Config\ThemeClientConfig;
use Saggre\WordPress\Repository\ThemeClient;

class ThemeClientTest extends FunctionalTestCase
{
    public static function dataProviderTestGetFile(): iterable
    {
        return [
            [
                'twentytwentyfive',
                '1.2',
                'theme.json',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetFile
     */
    public function testGetFile(string $slug, string $version, string $path)
    {
        $config = new ThemeClientConfig($slug, $version);
        $client = new ThemeClient($config);

        try {
            $file = $client->getFile($path);
        } catch (FilesystemException $e) {
            $this->fail("Failed to read file: {$e->getMessage()}");
        }

        $expected = $this->getExpectedFileContent($slug, $version, $path);
        $this->assertEquals($expected, $file);
    }

    /**
     * @dataProvider dataProviderTestGetFile
     */
    public function testGetFileStream(string $slug, string $version, string $path)
    {
        $config = new ThemeClientConfig($slug, $version);
        $client = new ThemeClient($config);

        try {
            $fileStream = $client->getFileStream($path);
        } catch (FilesystemException $e) {
            $this->fail("Failed to read file: {$e->getMessage()}");
        }

        $expected = $this->getExpectedFileContent($slug, $version, $path);
        $this->assertIsResource($fileStream);
        $this->assertEquals($expected, stream_get_contents($fileStream));
    }

    public static function dataProviderTestGetDirectory(): iterable
    {
        return [
            [
                'twentytwentyfive',
                '1.2',
                '/',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetDirectory
     */
    public function testGetDirectory(string $slug, string $version, string $path)
    {
        $config = new ThemeClientConfig($slug, $version);
        $client = new ThemeClient($config);

        try {
            $directory = $client->getDirectory($path);
            $directory = array_map(fn(StorageAttributes $item) => $item->jsonSerialize(), $directory->toArray());
        } catch (FilesystemException $e) {
            $this->fail("Failed to read directory: {$e->getMessage()}");
        }

        $expected = $this->getExpectedDirectoryListing($slug, $version, $path);
        $this->assertEqualsCanonicalizing($expected, $directory);
    }

    public function testGetDirectoryInvalidPath()
    {
        $config = new ThemeClientConfig('twentytwentyfive', '0.0.1');
        $client = new ThemeClient($config);

        $this->expectException(UnableToListContents::class);
        $this->expectExceptionMessage(
            "Unable to list contents for 'twentytwentyfive/0.0.1/invalid/path', shallow listing\n\nReason: Not Found"
        );

        $client->getDirectory('/invalid/path')->toArray();
    }
}
