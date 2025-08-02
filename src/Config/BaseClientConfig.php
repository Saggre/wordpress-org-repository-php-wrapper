<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;

/**
 * Base configuration class for WordPress.org plugin and theme clients.
 */
abstract class BaseClientConfig
{
    public function __construct(
        protected string $slug,
        protected string $version,
        protected string $baseUrl,
        protected string $userAgent
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
