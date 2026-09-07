<?php

namespace Saggre\WordPress\Repository\Config;

/**
 * Configuration class for the WordPress.org Plugin API Client.
 */
class PluginApiClientConfig extends BaseClientConfig
{
    /**
     * @param string $baseUrl The base URL for the plugin API.
     * @param string $userAgent The user agent string for HTTP requests.
     */
    public function __construct(
        string $baseUrl = 'https://api.wordpress.org',
        string $userAgent = 'wordpress-org-repository-php-wrapper/' . self::CLIENT_VERSION
    ) {
        parent::__construct($baseUrl, $userAgent);
    }
}
