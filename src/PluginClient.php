<?php

namespace Saggre\WordPress\Repository;

use League\Flysystem\DirectoryListing;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToListContents;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Util\Path;

class PluginClient
{
    public const CLIENT_VERSION = '1.0.0';

    protected Client $client;
    protected Filesystem $filesystem;

    public function __construct(
        protected PluginClientConfig $config,
    ) {
        $this->client = $this->createClient();
        $this->filesystem = $this->createFilesystem($this->client);
    }

    /**
     * Create a SabreDAV Client instance.
     *
     * @return Client
     * @codeCoverageIgnore
     */
    protected function createClient(): Client
    {
        $client = new Client(['baseUri' => $this->config->getBaseUrl()]);
        $client->addCurlSetting(CURLOPT_USERAGENT, $this->config->getUserAgent());

        return $client;
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
     * Get the path for a given plugin file.
     *
     * @param string $path Relative file path from the plugin root.
     * @return string
     */
    protected function getPath(string $path): string
    {
        return (new Path('/'))->join(
            $this->config->getSlug(),
            'tags',
            $this->config->getVersion(),
            $path
        );
    }

    /**
     * Get the content of a plugin file.
     *
     * @param string $path Relative file path from the plugin root.
     * @return string File content.
     * @throws FilesystemException On repository read error.
     */
    public function getFile(string $path): string
    {
        $fullPath = $this->getPath($path);

        return $this->getFilesystem()->read($fullPath);
    }

    /**
     * Get the content of a plugin file as a stream.
     *
     * @param string $path Relative file path from the plugin root.
     * @return resource File content stream.
     * @throws FilesystemException On repository read error.
     */
    public function getFileStream(string $path)
    {
        $fullPath = $this->getPath($path);

        return $this->getFilesystem()->readStream($fullPath);
    }

    /**
     * Get the content of a plugin directory.
     *
     * @param string $path Relative file path from the plugin root.
     * @return DirectoryListing Directory listing of the plugin directory.
     * @throws UnableToListContents On repository read error or if the path is not a directory.
     * @throws FilesystemException On repository read error.
     */
    public function getDirectory(string $path): DirectoryListing
    {
        $fullPath = $this->getPath($path);

        return $this->getFilesystem()->listContents($fullPath, false);
    }
}
