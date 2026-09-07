<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;

/**
 * Base configuration class for the WordPress.org SVN repository clients.
 */
abstract class RepositoryClientConfig extends BaseClientConfig
{
    /**
     * @throws InvalidArgumentException On empty slug or version.
     */
    public function __construct(
        protected string $slug,
        protected string $version,
        string $baseUrl,
        string $userAgent
    ) {
        if (empty($slug)) {
            throw new InvalidArgumentException('Slug cannot be empty.');
        }

        if (empty($version)) {
            throw new InvalidArgumentException('Version cannot be empty.');
        }

        parent::__construct($baseUrl, $userAgent);
    }

    /**
     * Get the slug of the plugin or theme.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get the version of the plugin or theme.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
