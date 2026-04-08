<?php

namespace Saggre\WordPress\Repository;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Util\Path;

/**
 * WordPress.org plugin client.
 */
class PluginClient extends BaseClient
{
    public function __construct(
        protected PluginClientConfig $config,
    ) {
        parent::__construct();
    }

    /**
     * List all tagged versions for this plugin.
     *
     * Returns a DirectoryListing of DirectoryAttributes, one per version tag.
     * Each entry has lastModified populated from the WebDAV getlastmodified property.
     *
     * @return DirectoryListing
     * @throws FilesystemException On repository read error.
     */
    public function getTagsDirectory(): DirectoryListing
    {
        $tagsPath = (new Path('/'))->join($this->config->getSlug(), 'tags');

        return $this->getFilesystem()->listContents($tagsPath, false);
    }
}
