<?php

namespace Saggre\WordPress\Repository;

use Sabre\HTTP\Client;
use Sabre\HTTP\Request;
use Saggre\WordPress\Repository\Config\BaseClientConfig;
use Saggre\WordPress\Repository\Config\PluginApiClientConfig;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Model\PluginInfo;
use Saggre\WordPress\Repository\Model\PluginQuery;
use Saggre\WordPress\Repository\Model\PluginQueryResult;
use Saggre\WordPress\Repository\Model\PluginStatus;

/**
 * WordPress.org plugin API client.
 */
class PluginApiClient
{
    public const CLIENT_VERSION = BaseClientConfig::CLIENT_VERSION;

    protected Client $client;

    public function __construct(
        protected PluginApiClientConfig $config = new PluginApiClientConfig(),
    ) {
        $this->client = $this->createClient();
    }

    /**
     * Create a SabreHTTP Client instance.
     *
     * @return Client
     * @codeCoverageIgnore
     */
    protected function createClient(): Client
    {
        $client = new Client();
        $client->addCurlSetting(CURLOPT_USERAGENT, $this->config->getUserAgent());

        return $client;
    }

    /**
     * Query the plugin directory for a single page of plugins.
     *
     * @param PluginQuery $query
     * @return PluginQueryResult
     * @throws ClientException On API error.
     */
    public function queryPlugins(PluginQuery $query): PluginQueryResult
    {
        [$status, $data] = $this->get($this->getQueryUrl('query_plugins', $query->toRequestParameters()));

        $this->assertSuccess($data, $status, 'plugin query');

        return PluginQueryResult::fromArray($data);
    }

    /**
     * Read the full record of a single plugin, including its versions map.
     *
     * @param string $slug
     * @param array<string,bool> $fields Response field toggles, e.g. ['sections' => false].
     * @return PluginInfo
     * @throws ClientException On API error, including closed and unknown plugins.
     */
    public function getPluginInformation(string $slug, array $fields = []): PluginInfo
    {
        $request = ['slug' => $slug];

        if (!empty($fields)) {
            $request['fields'] = array_map(fn(bool $enabled) => $enabled ? '1' : '0', $fields);
        }

        [$status, $data] = $this->get($this->getQueryUrl('plugin_information', $request));

        $this->assertSuccess($data, $status, $slug);

        return PluginInfo::fromArray($data);
    }

    /**
     * Check whether a plugin is still available in the plugin directory.
     *
     * A closed plugin is reported with a non-2xx status, so the response body is the answer.
     *
     * @param string $slug
     * @return PluginStatus
     * @throws ClientException On API error, including unknown plugins.
     */
    public function getPluginStatus(string $slug): PluginStatus
    {
        [$status, $data] = $this->get($this->getStatusUrl($slug));

        if (($data['error'] ?? null) !== 'closed') {
            $this->assertSuccess($data, $status, $slug);
        }

        return PluginStatus::fromArray($slug, $data);
    }

    /**
     * Build the URL of a plugins/info/1.2 request.
     *
     * @param string $action
     * @param array<string,mixed> $request Parameters sent as request[...].
     * @return string
     */
    protected function getQueryUrl(string $action, array $request): string
    {
        return sprintf(
            '%s/plugins/info/1.2/?%s',
            rtrim($this->config->getBaseUrl(), '/'),
            http_build_query(['action' => $action, 'request' => $request])
        );
    }

    /**
     * Build the URL of a plugins/info/1.0 request.
     *
     * @param string $slug
     * @return string
     */
    protected function getStatusUrl(string $slug): string
    {
        return sprintf(
            '%s/plugins/info/1.0/%s.json',
            rtrim($this->config->getBaseUrl(), '/'),
            rawurlencode($slug)
        );
    }

    /**
     * Send a GET request and decode its JSON body.
     *
     * @param string $url
     * @return array{0:int,1:array<string,mixed>} The HTTP status code and the decoded body.
     * @throws ClientException When the response body is not JSON.
     */
    protected function get(string $url): array
    {
        $response = $this->client->send(new Request('GET', $url));
        $data = json_decode($response->getBodyAsString(), true);

        if (!is_array($data)) {
            throw new ClientException(
                sprintf('Unable to decode the response of "%s".', $url),
                $response->getStatus()
            );
        }

        return [$response->getStatus(), $data];
    }

    /**
     * Throw when the API reported an error.
     *
     * @param array<string,mixed> $data
     * @param int $status
     * @param string $subject The plugin slug or request the error is about.
     * @return void
     * @throws ClientException
     */
    protected function assertSuccess(array $data, int $status, string $subject): void
    {
        if (empty($data['error']) && $status < 400) {
            return;
        }

        throw new ClientException(
            sprintf('Plugin API error for "%s": %s', $subject, $data['error'] ?? 'HTTP ' . $status),
            $status
        );
    }
}
