<?php

namespace Saggre\WordPress\Repository\Test\Functional;

use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\PluginDownloadClient;

class PluginDownloadClientTest extends FunctionalTestCase
{
    protected PluginDownloadClient $client;

    protected function setUp(): void
    {
        $this->client = new PluginDownloadClient();
    }

    public function testGetZipDownloadsAnExactVersion()
    {
        $zip = $this->client->getZip('hello-dolly', '1.7.2');

        self::assertStringStartsWith('PK', $zip);
        self::assertGreaterThan(500, strlen($zip));
    }

    public function testGetZipDownloadsTheCurrentRelease()
    {
        $zip = $this->client->getZip('hello-dolly');

        self::assertStringStartsWith('PK', $zip);
    }

    public function testGetZipStreamDownloadsAnExactVersion()
    {
        $stream = $this->client->getZipStream('hello-dolly', '1.7.2');

        self::assertIsResource($stream);
        self::assertStringStartsWith('PK', stream_get_contents($stream));
    }

    public function testGetZipThrowsOnWithdrawnRelease()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->client->getZip('hello-dolly', '0.0.1');
    }
}
