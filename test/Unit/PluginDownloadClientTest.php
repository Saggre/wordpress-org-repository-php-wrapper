<?php

namespace Saggre\WordPress\Repository\Test\Unit;

use Sabre\HTTP\Client;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\PluginDownloadClient;
use Saggre\WordPress\Repository\Test\Unit\Stub\HttpClientStub;

class PluginDownloadClientTest extends UnitTestCase
{
    /**
     * Build a client that talks to a stub instead of the live distribution host.
     *
     * @param HttpClientStub $http
     * @return PluginDownloadClient
     */
    protected function createDownloadClient(HttpClientStub $http): PluginDownloadClient
    {
        return new class ($http) extends PluginDownloadClient {
            public function __construct(protected HttpClientStub $http)
            {
                parent::__construct();
            }

            protected function createClient(): Client
            {
                return $this->http;
            }
        };
    }

    public static function dataProviderTestGetZipUrl(): iterable
    {
        return [
            [
                'expected' => 'https://downloads.wordpress.org/plugin/hello-dolly.zip',
                'slug' => 'hello-dolly',
                'version' => null,
            ],
            [
                'expected' => 'https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip',
                'slug' => 'hello-dolly',
                'version' => '1.7.2',
            ],
            [
                'expected' => 'https://downloads.wordpress.org/plugin/woo%2Fcommerce.zip',
                'slug' => 'woo/commerce',
                'version' => null,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetZipUrl
     */
    public function testGetZipUrl(string $expected, string $slug, ?string $version)
    {
        $client = new PluginDownloadClient();

        self::assertSame($expected, $client->getZipUrl($slug, $version));
    }

    public function testGetZipDownloadsTheRelease()
    {
        $http = HttpClientStub::respondWith(200, 'PK zip contents');

        $zip = $this->createDownloadClient($http)->getZip('hello-dolly', '1.7.2');

        self::assertSame('PK zip contents', $zip);
        self::assertSame('https://downloads.wordpress.org/plugin/hello-dolly.1.7.2.zip', $http->getLastUrl());
    }

    public function testGetZipDownloadsTheCurrentRelease()
    {
        $http = HttpClientStub::respondWith(200, 'PK zip contents');

        $this->createDownloadClient($http)->getZip('hello-dolly');

        self::assertSame('https://downloads.wordpress.org/plugin/hello-dolly.zip', $http->getLastUrl());
    }

    public function testGetZipStreamReturnsAStream()
    {
        $http = HttpClientStub::respondWith(200, 'PK zip contents');

        $stream = $this->createDownloadClient($http)->getZipStream('hello-dolly', '1.7.2');

        self::assertIsResource($stream);
        self::assertSame('PK zip contents', stream_get_contents($stream));
    }

    public function testGetZipThrowsOnWithdrawnRelease()
    {
        $http = HttpClientStub::respondWith(404, 'Not Found');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage(
            'Unable to download "https://downloads.wordpress.org/plugin/hello-dolly.0.0.1.zip".'
        );
        $this->expectExceptionCode(404);

        $this->createDownloadClient($http)->getZip('hello-dolly', '0.0.1');
    }
}
