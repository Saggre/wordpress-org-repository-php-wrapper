<?php

namespace Saggre\WordPress\Repository\Config;

/**
 * Base configuration class for WordPress.org clients.
 */
abstract class BaseClientConfig
{
    public const CLIENT_VERSION = '1.0.0';

    public function __construct(
        protected string $baseUrl,
        protected string $userAgent
    ) {
    }

    /**
     * Get the base URL the client sends its requests to.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the user agent string for the client.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
