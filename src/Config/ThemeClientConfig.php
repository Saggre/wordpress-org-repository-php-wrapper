<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;
use Saggre\WordPress\Repository\ThemeClient;

/**
 * Configuration class for the WordPress.org Theme Client.
 */
class ThemeClientConfig extends BaseClientConfig
{
    /**
     * @param string $slug The slug of the theme.
     * * @param string $version The version of the theme, defaults to 'trunk'.
     * * @param string $baseUrl The base URL for the theme repository.
     * * @param string $userAgent The user agent string for HTTP request.
     * * @throws InvalidArgumentException On empty slug or version.
     */
    public function __construct(
        protected string $slug,
        protected string $version = 'trunk',
        protected string $baseUrl = 'https://themes.svn.wordpress.org',
        protected string $userAgent = 'wordpress-org-repository-php-wrapper/' . ThemeClient::CLIENT_VERSION
    ) {
        parent::__construct($slug, $version, $baseUrl, $userAgent);
    }
}
