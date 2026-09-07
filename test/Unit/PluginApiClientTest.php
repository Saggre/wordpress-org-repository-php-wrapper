<?php

namespace Saggre\WordPress\Repository\Test\Unit;

use Sabre\HTTP\Client;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Model\PluginBrowse;
use Saggre\WordPress\Repository\Model\PluginQuery;
use Saggre\WordPress\Repository\PluginApiClient;
use Saggre\WordPress\Repository\Test\Unit\Stub\HttpClientStub;

class PluginApiClientTest extends UnitTestCase
{
    /**
     * Build a client that talks to a stub instead of the live API.
     *
     * @param HttpClientStub $http
     * @return PluginApiClient
     */
    protected function createApiClient(HttpClientStub $http): PluginApiClient
    {
        return new class ($http) extends PluginApiClient {
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

    public function testQueryPluginsRequestsTheQueryEndpoint()
    {
        $http = HttpClientStub::respondWith(200, $this->getFixture('query_plugins.json'));

        $this->createApiClient($http)->queryPlugins(new PluginQuery(
            browse: PluginBrowse::Updated,
            page: 2,
            perPage: 250,
            fields: ['sections' => false, 'contributors' => true],
        ));

        $url = urldecode($http->getLastUrl());

        self::assertStringStartsWith('https://api.wordpress.org/plugins/info/1.2/?', $url);
        self::assertStringContainsString('action=query_plugins', $url);
        self::assertStringContainsString('request[browse]=updated', $url);
        self::assertStringContainsString('request[page]=2', $url);
        self::assertStringContainsString('request[per_page]=250', $url);
        self::assertStringContainsString('request[fields][sections]=0', $url);
        self::assertStringContainsString('request[fields][contributors]=1', $url);
    }

    public function testQueryPluginsReturnsResultPage()
    {
        $http = HttpClientStub::respondWith(200, $this->getFixture('query_plugins.json'));

        $result = $this->createApiClient($http)->queryPlugins(new PluginQuery(browse: PluginBrowse::Updated));

        self::assertSame(71793, $result->results);
        self::assertCount(3, $result->plugins);
        self::assertSame('immowp-gestion-immobiliere', $result->plugins[0]->slug);
    }

    public function testQueryPluginsThrowsOnApiError()
    {
        $http = HttpClientStub::respondWith(400, '{"error":"Invalid request."}');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Plugin API error for "plugin query": Invalid request.');
        $this->expectExceptionCode(400);

        $this->createApiClient($http)->queryPlugins(new PluginQuery());
    }

    public function testGetPluginInformationRequestsTheSlug()
    {
        $http = HttpClientStub::respondWith(200, $this->getFixture('plugin_information.json'));

        $info = $this->createApiClient($http)->getPluginInformation('hello-dolly', ['sections' => false]);

        $url = urldecode($http->getLastUrl());

        self::assertStringContainsString('action=plugin_information', $url);
        self::assertStringContainsString('request[slug]=hello-dolly', $url);
        self::assertStringContainsString('request[fields][sections]=0', $url);
        self::assertSame('hello-dolly', $info->slug);
        self::assertArrayHasKey('1.7.2', $info->versions);
    }

    public function testGetPluginInformationThrowsOnUnknownPlugin()
    {
        $http = HttpClientStub::respondWith(404, '{"error":"Plugin not found."}');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Plugin API error for "not-a-plugin": Plugin not found.');
        $this->expectExceptionCode(404);

        $this->createApiClient($http)->getPluginInformation('not-a-plugin');
    }

    public function testGetPluginInformationThrowsOnClosedPlugin()
    {
        $http = HttpClientStub::respondWith(404, $this->getFixture('plugin_status_closed.json'));

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Plugin API error for "hana-flv-player": closed');

        $this->createApiClient($http)->getPluginInformation('hana-flv-player');
    }

    public function testGetPluginStatusRequestsTheStatusEndpoint()
    {
        $http = HttpClientStub::respondWith(200, $this->getFixture('plugin_status_open.json'));

        $status = $this->createApiClient($http)->getPluginStatus('akismet');

        self::assertSame('https://api.wordpress.org/plugins/info/1.0/akismet.json', $http->getLastUrl());
        self::assertFalse($status->closed);
    }

    public function testGetPluginStatusReadsTheBodyOfANonSuccessResponse()
    {
        $http = HttpClientStub::respondWith(404, $this->getFixture('plugin_status_closed.json'));

        $status = $this->createApiClient($http)->getPluginStatus('hana-flv-player');

        self::assertTrue($status->closed);
        self::assertSame('security-issue', $status->reason);
    }

    public function testGetPluginStatusThrowsOnUnknownPlugin()
    {
        $http = HttpClientStub::respondWith(404, '{"error":"Plugin not found."}');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Plugin API error for "not-a-plugin": Plugin not found.');

        $this->createApiClient($http)->getPluginStatus('not-a-plugin');
    }

    public function testThrowsOnUndecodableResponse()
    {
        $http = HttpClientStub::respondWith(503, '<html><body>Service Unavailable</body></html>');

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Unable to decode the response of');
        $this->expectExceptionCode(503);

        $this->createApiClient($http)->getPluginInformation('hello-dolly');
    }
}
