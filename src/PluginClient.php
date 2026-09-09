<?php

namespace Saggre\WordPress\Repository;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Exception\TagNotFoundException;
use Saggre\WordPress\Repository\Model\LogEntry;
use Saggre\WordPress\Repository\Model\LogPath;
use Saggre\WordPress\Repository\Model\LogPathAction;
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

    /**
     * Map every published version to the revision that created its tag.
     *
     * Reads the whole tag history in one request, so the cost grows with the number of releases.
     * A tag is a directory copy, so the entry also carries the trunk revision the release was cut
     * from, in the copyFromRevision of its path.
     *
     * Ordered by revision, oldest release first. Version strings cannot be sorted as text, where
     * '1.10.4' lands between '1.1.9' and '1.2.0', but revision numbers are monotonic.
     *
     * @return array<string, LogEntry> Version string to the revision that added its tag.
     * @throws ClientException On repository read error.
     */
    public function getTagRevisions(): array
    {
        $prefix = (new Path('/'))->join('/', $this->config->getSlug(), 'tags') . '/';
        $revisions = [];

        $log = $this->getLogForPath(
            (new Path('/'))->join('/', $this->config->getSlug()),
            0,
            null,
            0,
            'tags'
        );

        foreach ($log as $entry) {
            foreach ($entry->paths as $path) {
                if ($path->action !== LogPathAction::Added || $path->nodeKind !== 'dir') {
                    continue;
                }

                if (!str_starts_with($path->path, $prefix)) {
                    continue;
                }

                $version = substr($path->path, strlen($prefix));

                // The log runs newest first, so the first add wins and a tag that was deleted and
                // recreated resolves to the copy the repository actually holds.
                if ($version !== '' && !str_contains($version, '/') && !isset($revisions[$version])) {
                    $revisions[$version] = $entry;
                }
            }
        }

        uasort($revisions, fn(LogEntry $a, LogEntry $b) => $a->revision <=> $b->revision);

        return $revisions;
    }

    /**
     * Get the files that changed between two published versions.
     *
     * Resolves both tags, then reads the revision range between them in a single request, which
     * is the cheap alternative to downloading and comparing two complete trees.
     *
     * Vendors commonly commit the same edit to trunk and to the new tag, so both trees are read
     * and deduplicated. The tag directory itself is a copy rather than a file change and is left
     * out, as is anything committed to an unrelated tag in the same range.
     *
     * @param string $old The older version, e.g. '4.4.3'.
     * @param string $new The newer version, e.g. '4.4.4'.
     * @return array<string, LogPath> Changed files, keyed by their path relative to the plugin root.
     * @throws TagNotFoundException When either version has no tag.
     * @throws ClientException On repository read error.
     */
    public function diffVersions(string $old, string $new): array
    {
        $tags = $this->getTagRevisions();

        foreach ([$old, $new] as $version) {
            if (!isset($tags[$version])) {
                throw new TagNotFoundException(sprintf(
                    'Version "%s" of "%s" has no tag in the repository.',
                    $version,
                    $this->config->getSlug()
                ));
            }
        }

        // The revision that creates a tag also fills it from trunk, so it belongs to the older
        // release rather than to the range between the two.
        $log = $this->getChangedPaths($tags[$new]->revision, $tags[$old]->revision + 1);

        return $this->normalizePaths($log, $new);
    }

    /**
     * Reduce the paths of a revision range to one entry per file of the plugin tree.
     *
     * @param LogEntry[] $log
     * @param string $version The tagged version the range ends at.
     * @return array<string, LogPath>
     */
    protected function normalizePaths(array $log, string $version): array
    {
        $root = new Path('/');
        $slug = $this->config->getSlug();
        $prefixes = [
            $root->join('/', $slug, 'trunk') . '/',
            $root->join('/', $slug, 'tags', $version) . '/',
        ];
        $paths = [];

        foreach ($log as $entry) {
            foreach ($entry->paths as $path) {
                if ($path->nodeKind === 'dir') {
                    continue;
                }

                $relative = $this->stripPrefix($path->path, $prefixes);

                if ($relative === null) {
                    continue;
                }

                // A property only change in one tree must not hide a content change in the other.
                if (isset($paths[$relative]) && !$path->textMods) {
                    continue;
                }

                $paths[$relative] = new LogPath(
                    $relative,
                    $path->action,
                    $path->nodeKind,
                    $path->textMods,
                    $path->propMods,
                    $path->copyFromPath,
                    $path->copyFromRevision,
                );
            }
        }

        ksort($paths);

        return $paths;
    }

    /**
     * Strip the trunk or tag prefix from a repository absolute path.
     *
     * @param string $path
     * @param string[] $prefixes
     * @return string|null Null when the path lies outside every given tree.
     */
    protected function stripPrefix(string $path, array $prefixes): ?string
    {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return substr($path, strlen($prefix));
            }
        }

        return null;
    }
}
