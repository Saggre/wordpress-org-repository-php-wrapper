<?php

use League\Flysystem\FilesystemException;
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
}
