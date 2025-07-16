<?php

namespace Saggre\WordPress\Repository\Config;

use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;

class PluginClientConfig implements ClientConfigInterface
{
    protected Client $client;
    protected Filesystem $filesystem;

    /**
     * Create a new PluginClientConfig instance.
     *
     * @param string $slug
     * @param string $version
     * @param string $baseUrl
     * @throws InvalidArgumentException On empty slug or version.
     */
    public function __construct(
        protected string $slug,
        protected string $version = 'trunk',
        protected string $baseUrl = 'https://plugins.svn.wordpress.org',
    ) {
        if (empty($slug)) {
            throw new InvalidArgumentException('Plugin slug cannot be empty.');
        }

        if (empty($version)) {
            throw new InvalidArgumentException('Plugin version cannot be empty.');
        }

        $this->client = new Client(['baseUri' => $this->baseUrl]);
        $this->filesystem = $this->createFilesystem($this->client);
    }

    /**
     * Create a League\Flysystem Filesystem instance using the WebDAV adapter.
     *
     * @param Client $client
     * @return Filesystem
     * @codeCoverageIgnore
     */
    protected function createFilesystem(Client $client): Filesystem
    {
        $adapter = new WebDAVAdapter($client);
        return new Filesystem($adapter);
    }

    /**
     * Get the League\Flysystem Filesystem instance.
     *
     * @return Filesystem
     * @codeCoverageIgnore
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
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
}
