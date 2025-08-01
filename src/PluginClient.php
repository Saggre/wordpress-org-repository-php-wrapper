<?php

namespace Saggre\WordPress\Repository;

use League\Flysystem\FilesystemException;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Symfony\Component\Filesystem\Path;

class PluginClient
{
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
    protected function getFilePath(string $path): string
    {
        return Path::join($this->config->getSlug(), 'tags', $this->config->getVersion(), $path);
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
        $fullPath = $this->getFilePath($path);
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
        $fullPath = $this->getFilePath($path);
        return $this->config
            ->getFilesystem()
            ->readStream($fullPath);
    }
}
