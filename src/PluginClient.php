<?php

namespace Saggre\WordPress\Repository;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToListContents;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Util\Path;

class PluginClient
{
    public const CLIENT_VERSION = '1.0.0';

    public function __construct(
        protected PluginClientConfig $config,
    ) {
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
        return $this->config
            ->getFilesystem()
            ->read($fullPath);
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
        return $this->config
            ->getFilesystem()
            ->readStream($fullPath);
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
        $filesystem = $this->config->getFilesystem();
        $fullPath = $this->getPath($path);

        return $filesystem->listContents($fullPath, false);
    }
}
