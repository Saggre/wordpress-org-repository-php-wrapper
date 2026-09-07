<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;

/**
 * Configuration class for the WordPress.org Theme Client.
 */
class ThemeClientConfig extends RepositoryClientConfig
{
    /**
     * @param string $slug The slug of the theme.
     * @param string $version The version of the theme, defaults to 'trunk'.
     * @param string $baseUrl The base URL for the theme repository.
     * @param string $userAgent The user agent string for HTTP request.
     * @throws InvalidArgumentException On empty slug or version.
     */
    public function __construct(
        protected string $slug,
        protected string $version = 'trunk',
        string $baseUrl = 'https://themes.svn.wordpress.org',
        string $userAgent = 'wordpress-org-repository-php-wrapper/' . self::CLIENT_VERSION
    ) {
        parent::__construct($slug, $version, $baseUrl, $userAgent);
    }
}
