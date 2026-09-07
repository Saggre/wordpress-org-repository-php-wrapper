<?php

namespace Saggre\WordPress\Repository;

use Sabre\HTTP\Client;
use Sabre\HTTP\Request;
use Sabre\HTTP\ResponseInterface;
use Saggre\WordPress\Repository\Config\BaseClientConfig;
use Saggre\WordPress\Repository\Config\PluginDownloadClientConfig;
use Saggre\WordPress\Repository\Exception\ClientException;

/**
 * WordPress.org plugin distribution client.
 */
class PluginDownloadClient
{
    public const CLIENT_VERSION = BaseClientConfig::CLIENT_VERSION;

    protected Client $client;

    public function __construct(
        protected PluginDownloadClientConfig $config = new PluginDownloadClientConfig(),
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
     * Build the download URL of a plugin release.
     *
     * @param string $slug
     * @param string|null $version Release to download, defaults to the current release.
     * @return string
     */
    public function getZipUrl(string $slug, ?string $version = null): string
    {
        return sprintf(
            '%s/plugin/%s%s.zip',
            rtrim($this->config->getBaseUrl(), '/'),
            rawurlencode($slug),
            $version === null ? '' : '.' . rawurlencode($version)
        );
    }

    /**
     * Download a plugin release.
     *
     * Only the current release is available without a version. Withdrawn releases are no longer
     * served here even when they still exist in the SVN repository.
     *
     * @param string $slug
     * @param string|null $version Release to download, defaults to the current release.
     * @return string The zip archive contents.
     * @throws ClientException When the release is not available.
     */
    public function getZip(string $slug, ?string $version = null): string
    {
        return $this->download($slug, $version)->getBodyAsString();
    }

    /**
     * Download a plugin release as a stream.
     *
     * @param string $slug
     * @param string|null $version Release to download, defaults to the current release.
     * @return resource The zip archive stream.
     * @throws ClientException When the release is not available.
     */
    public function getZipStream(string $slug, ?string $version = null)
    {
        return $this->download($slug, $version)->getBodyAsStream();
    }

    /**
     * Request a plugin release.
     *
     * @param string $slug
     * @param string|null $version
     * @return ResponseInterface
     * @throws ClientException When the release is not available.
     */
    protected function download(string $slug, ?string $version): ResponseInterface
    {
        $url = $this->getZipUrl($slug, $version);
        $response = $this->client->send(new Request('GET', $url));

        if ($response->getStatus() >= 400) {
            throw new ClientException(sprintf('Unable to download "%s".', $url), $response->getStatus());
        }

        return $response;
    }
}
