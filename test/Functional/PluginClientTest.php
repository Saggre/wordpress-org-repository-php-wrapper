<?php

use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToListContents;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\PluginClient;
use Saggre\WordPress\Repository\Test\Functional\FunctionalTestCase;

class PluginClientTest extends FunctionalTestCase
{
    public static function dataProviderTestGetFile(): iterable
    {
        return [
            [
                'woocommerce',
                '9.6.2',
                'readme.txt',
            ],
            [
                'wordpress-seo',
                '25.5',
                'wp-seo.php',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetFile
     */
    public function testGetFile(string $slug, string $version, string $path)
    {
        $config = new PluginClientConfig($slug, $version);
        $client = new PluginClient($config);

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
        $config = new PluginClientConfig($slug, $version);
        $client = new PluginClient($config);

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
                'woocommerce',
                '9.6.2',
                '/i18n',
            ],
            [
                'wordpress-seo',
                '25.5',
                '/',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetDirectory
     */
    public function testGetDirectory(string $slug, string $version, string $path)
    {
        $config = new PluginClientConfig($slug, $version);
        $client = new PluginClient($config);

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
        $config = new PluginClientConfig('woocommerce', '9.6.2');
        $client = new PluginClient($config);

        $this->expectException(UnableToListContents::class);
        $this->expectExceptionMessage(
            "Unable to list contents for 'woocommerce/tags/9.6.2/invalid/path', shallow listing\n\nReason: Not Found"
        );

        $client->getDirectory('/invalid/path')->toArray();
    }
}
