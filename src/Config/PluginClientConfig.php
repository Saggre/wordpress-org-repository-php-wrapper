<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;
use Saggre\WordPress\Repository\PluginClient;

/**
 * Configuration class for the WordPress.org Plugin Client.
 */
class PluginClientConfig extends BaseClientConfig
{
    /**
     * @param string $slug The slug of the plugin.
     * @param string $version The version of the plugin, defaults to 'trunk'.
     * @param string $baseUrl The base URL for the plugin repository.
     * @param string $userAgent The user agent string for HTTP request.
     * @throws InvalidArgumentException On empty slug or version.
     */
    public function __construct(
        protected string $slug,
        protected string $version = 'trunk',
        protected string $baseUrl = 'https://plugins.svn.wordpress.org',
        protected string $userAgent = 'wordpress-org-repository-php-wrapper/' . PluginClient::CLIENT_VERSION
    ) {
        parent::__construct($slug, $version, $baseUrl, $userAgent);
    }
}
