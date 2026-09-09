<?php

namespace Saggre\WordPress\Repository;

use InvalidArgumentException;
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
     * Reads the tag history in one request, so without a limit the cost grows with the number of
     * releases. A caller that only needs the newest releases can cap the revisions read, which is
     * the whole cost of a diff for a plugin with a long history. A tag is a directory copy, so the
     * entry also carries the trunk revision the release was cut from, in the copyFromRevision of
     * its path.
     *
     * Ordered by revision, oldest release first. Version strings cannot be sorted as text, where
     * '1.10.4' lands between '1.1.9' and '1.2.0', but revision numbers are monotonic.
     *
     * @param int $limit Maximum number of tag revisions to read, newest first. 0 for no limit. A
     *                   release usually takes one revision, but retagging a release and editing a
     *                   file inside a tag take their own, so the window can hold fewer versions.
     * @return array<string, LogEntry> Version string to the revision that added its tag.
     * @throws ClientException On repository read error.
     */
    public function getTagRevisions(int $limit = 0): array
    {
        $tagsPath = $this->getRootPath() . '/tags';
        $revisions = [];
        $deleted = [];

        $log = $this->getLogForPath($this->getRootPath(), $limit, null, 0, 'tags');

        foreach ($log as $entry) {
            foreach ($entry->paths as $path) {
                if (
                    $path->nodeKind !== 'dir'
                    || $path->action === LogPathAction::Modified
                    || dirname($path->path) !== $tagsPath
                ) {
                    continue;
                }

                $version = basename($path->path);

                // The log runs newest first, so the newest event wins: a tag whose newest event is
                // a deletion no longer exists, and a recreated tag resolves to the copy the
                // repository actually holds.
                if (isset($revisions[$version]) || isset($deleted[$version])) {
                    continue;
                }

                if ($path->action === LogPathAction::Deleted) {
                    $deleted[$version] = true;
                } else {
                    $revisions[$version] = $entry;
                }
            }
        }

        return array_reverse($revisions, true);
    }

    /**
     * Get the files that changed between two published versions.
     *
     * Resolves both tags, then reads the revision range between them in a single request, which
     * is the cheap alternative to downloading and comparing two complete trees.
     *
     * Vendors commonly commit the same edit to trunk and to the new tag, so both trees are read
     * and deduplicated. The tag directory itself is a copy rather than a file change and is left
     * out, as is anything committed to an unrelated tag in the same range. A deleted or copied
     * directory is listed in place of the files it removed or brought along, since the log does
     * not name them.
     *
     * Resolving the tags is the expensive half for a plugin with a long history, since it reads
     * the whole tag log to find two revisions. A caller diffing consecutive releases can cap that
     * read with $limit, at the price of a TagNotFoundException for a version tagged before the
     * window.
     *
     * @param string $old The older version, e.g. '4.4.3'.
     * @param string $new The newer version, e.g. '4.4.4'.
     * @param int $limit Maximum number of tag revisions to read, newest first. 0 for no limit.
     * @return array<string, LogPath> Changed paths, keyed by their path relative to the plugin root.
     * @throws TagNotFoundException When either version has no tag in the revisions read.
     * @throws InvalidArgumentException When the old version was not tagged before the new one.
     * @throws ClientException On repository read error.
     */
    public function diffVersions(string $old, string $new, int $limit = 0): array
    {
        $tags = $this->getTagRevisions($limit);

        foreach ([$old, $new] as $version) {
            if (!isset($tags[$version])) {
                throw new TagNotFoundException(sprintf(
                    'Version "%s" of "%s" has no tag in %s.',
                    $version,
                    $this->config->getSlug(),
                    $limit > 0
                        ? sprintf('the newest %d revisions of its tags', $limit)
                        : 'the repository'
                ));
            }
        }

        if ($tags[$old]->revision >= $tags[$new]->revision) {
            throw new InvalidArgumentException(sprintf(
                'Version "%s" was not tagged before version "%s".',
                $old,
                $new
            ));
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
        $prefixes = [
            $this->getRootPath() . '/trunk/',
            $this->getRootPath() . '/tags/' . $version . '/',
        ];
        $paths = [];

        foreach ($log as $entry) {
            foreach ($entry->paths as $path) {
                // A plain directory add lists its files separately, but a deleted or copied
                // directory is the only trace of the files it removed or brought along.
                if (
                    $path->nodeKind === 'dir'
                    && $path->action !== LogPathAction::Deleted
                    && $path->copyFromPath === null
                ) {
                    continue;
                }

                $relative = $this->stripPrefix($path->path, $prefixes);

                if ($relative === null) {
                    continue;
                }

                // A property only change in one tree must not hide a content change in the other,
                // but nothing older than a deletion can bring a path back.
                if (
                    isset($paths[$relative])
                    && (!$path->textMods || $paths[$relative]->action === LogPathAction::Deleted)
                ) {
                    continue;
                }

                $paths[$relative] = new LogPath(
                    $relative,
                    $path->action,
                    $path->nodeKind,
                    $path->copyFromPath,
                    $path->copyFromRevision,
                    $path->textMods,
                    $path->propMods,
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
