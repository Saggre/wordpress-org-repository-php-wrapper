<?php

namespace Saggre\WordPress\Repository\Config;

/**
 * Configuration class for the WordPress.org Plugin Download Client.
 */
class PluginDownloadClientConfig extends BaseClientConfig
{
    /**
     * @param string $baseUrl The base URL for the plugin distribution host.
     * @param string $userAgent The user agent string for HTTP requests.
     */
    public function __construct(
        string $baseUrl = 'https://downloads.wordpress.org',
        string $userAgent = 'wordpress-org-repository-php-wrapper/' . self::CLIENT_VERSION
    ) {
        parent::__construct($baseUrl, $userAgent);
    }
}
