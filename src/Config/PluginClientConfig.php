<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;
use Saggre\WordPress\Repository\PluginClient;

class PluginClientConfig
{
    /**
     * Create a new PluginClientConfig instance.
     *
     * @param string $slug
     * @param string $version
     * @param string $baseUrl
     * @param string $userAgent
     * @throws InvalidArgumentException On empty slug or version.
     */
    public function __construct(
        protected string $slug,
        protected string $version = 'trunk',
        protected string $baseUrl = 'https://plugins.svn.wordpress.org',
        protected string $userAgent = 'wordpress-org-php-repository-wrapper/' . PluginClient::CLIENT_VERSION
    ) {
        if (empty($slug)) {
            throw new InvalidArgumentException('Plugin slug cannot be empty.');
        }

        if (empty($version)) {
            throw new InvalidArgumentException('Plugin version cannot be empty.');
        }
    }

    /**
     * Get the slug of the plugin.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get the version of the plugin.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the base URL for the plugin repository.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the user agent string for the plugin client.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
