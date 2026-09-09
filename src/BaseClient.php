<?php

namespace Saggre\WordPress\Repository;

use InvalidArgumentException;
use League\Flysystem\DirectoryListing;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToListContents;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;
use Saggre\WordPress\Repository\Config\BaseClientConfig;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Model\LogEntry;
use Saggre\WordPress\Repository\Util\LogReport;
use Saggre\WordPress\Repository\Util\Path;

/**
 * Base class for WordPress.org plugin and theme clients.
 */
abstract class BaseClient
{
    public const CLIENT_VERSION = BaseClientConfig::CLIENT_VERSION;

    protected Client $client;
    protected Filesystem $filesystem;

    public function __construct()
    {
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

        // Commit logs are large XML documents that compress by more than an order of magnitude.
        $client->addCurlSetting(CURLOPT_ENCODING, '');

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
     * Get the path for a given plugin or theme file.
     *
     * @param string $path Relative file path from the plugin or theme root.
     * @return string
     */
    protected function getPath(string $path): string
    {
        return (new Path('/'))->join(
            $this->config->getSlug(),
            $this->config->getVersion() === 'trunk' ? null : 'tags',
            $this->config->getVersion(),
            $path
        );
    }

    /**
     * Get the repository absolute path of the plugin or theme root, e.g. '/hello-dolly'.
     *
     * @return string
     */
    protected function getRootPath(): string
    {
        return (new Path('/'))->join('/', $this->config->getSlug());
    }

    /**
     * Get the content of a plugin or theme file.
     *
     * @param string $path Relative file path from the plugin or theme root.
     * @return string File content.
     * @throws FilesystemException On repository read error.
     */
    public function getFile(string $path): string
    {
        $fullPath = $this->getPath($path);

        return $this->getFilesystem()->read($fullPath);
    }

    /**
     * Get the content of a plugin or theme file as a stream.
     *
     * @param string $path Relative file path from the plugin or theme root.
     * @return resource File content stream.
     * @throws FilesystemException On repository read error.
     */
    public function getFileStream(string $path)
    {
        $fullPath = $this->getPath($path);

        return $this->getFilesystem()->readStream($fullPath);
    }

    /**
     * Get the content of a plugin or theme directory.
     *
     * @param string $path Relative directory path from the plugin or theme root.
     * @param bool $deep Whether to list the contents of subdirectories as well.
     * @return DirectoryListing Directory listing of the plugin or theme directory.
     * @throws UnableToListContents On repository read error or if the path is not a directory.
     * @throws FilesystemException On repository read error.
     */
    public function getDirectory(string $path = '', bool $deep = false): DirectoryListing
    {
        $fullPath = $this->getPath($path);

        return $this->getFilesystem()->listContents($fullPath, $deep);
    }

    /**
     * Write the tree of the configured version to a local directory.
     *
     * The repository equivalent of an svn export, which retrieves builds that are no longer
     * available on the distribution host.
     *
     * @param string $destination Local directory to write to. Created if it does not exist.
     * @return int The number of files written.
     * @throws ClientException When the destination cannot be written to.
     * @throws FilesystemException On repository read error.
     */
    public function export(string $destination): int
    {
        $localPath = new Path(DIRECTORY_SEPARATOR);
        $remotePath = new Path('/');
        $base = strlen(trim($this->getPath(''), '/'));
        $files = 0;

        $this->createDirectory($destination);

        foreach ($this->getDirectory('', true) as $item) {
            $parts = $remotePath->explode(substr(trim($item->path(), '/'), $base));

            if (in_array('..', $parts, true)) {
                continue;
            }

            $target = $localPath->join($destination, ...$parts);

            if ($item->type() === StorageAttributes::TYPE_DIRECTORY) {
                $this->createDirectory($target);

                continue;
            }

            $this->createDirectory(dirname($target));
            $this->writeFile($target, $this->getFilesystem()->readStream($item->path()));
            $files++;
        }

        return $files;
    }

    /**
     * Get the commit log of the configured plugin or theme, newest revision first.
     *
     * @param int $limit Maximum number of revisions to return.
     * @param int|null $startRevision Revision to start from, defaults to the youngest revision.
     * @param int $endRevision Revision to stop at.
     * @return LogEntry[]
     * @throws ClientException On repository read error.
     * @throws InvalidArgumentException On a negative end revision or an inverted range.
     */
    public function getLog(int $limit = 100, ?int $startRevision = null, int $endRevision = 0): array
    {
        return $this->getLogForPath($this->getRootPath(), $limit, $startRevision, $endRevision);
    }

    /**
     * Get the commit log of the whole repository, newest revision first.
     *
     * A single revision spans every plugin or theme changed by that commit.
     *
     * @param int $limit Maximum number of revisions to return.
     * @param int|null $startRevision Revision to start from, defaults to the youngest revision.
     * @param int $endRevision Revision to stop at.
     * @return LogEntry[]
     * @throws ClientException On repository read error.
     * @throws InvalidArgumentException On a negative end revision or an inverted range.
     */
    public function getRepositoryLog(int $limit = 100, ?int $startRevision = null, int $endRevision = 0): array
    {
        return $this->getLogForPath('/', $limit, $startRevision, $endRevision);
    }

    /**
     * Get the revisions that changed a path of the configured plugin or theme, newest first.
     *
     * The range is inclusive at both ends, as in getLog(). Scoping to a path selects the
     * revisions; each of them still reports every path it touched, including paths outside the
     * scope, so a revision that changed both trunk and a tag lists both.
     *
     * @param int $startRevision Newer bound of the range.
     * @param int $endRevision Older bound of the range.
     * @param string $path Path relative to the plugin or theme root, e.g. 'tags' or 'trunk/admin'.
     * @param int $limit Maximum number of revisions to return. 0 for no limit.
     * @return LogEntry[]
     * @throws ClientException On repository read error.
     * @throws InvalidArgumentException On a negative end revision or an inverted range.
     */
    public function getChangedPaths(int $startRevision, int $endRevision, string $path = '', int $limit = 0): array
    {
        return $this->getLogForPath($this->getRootPath(), $limit, $startRevision, $endRevision, $path);
    }

    /**
     * Run an SVN log-report against a repository path.
     *
     * The server only answers a REPORT at the repository root or at a plugin or theme root, so
     * narrower scopes go into the request body rather than into the target.
     *
     * @param string $target Repository absolute path to send the report to.
     * @param int $limit
     * @param int|null $startRevision
     * @param int $endRevision
     * @param string $path Path relative to the target, to restrict the revisions to.
     * @return LogEntry[]
     * @throws ClientException On repository read error.
     * @throws InvalidArgumentException On a negative end revision or an inverted range.
     */
    protected function getLogForPath(
        string $target,
        int $limit,
        ?int $startRevision,
        int $endRevision,
        string $path = ''
    ): array {
        $report = new LogReport();

        $response = $this->client->request(
            'REPORT',
            $target,
            $report->createRequestBody($limit, $startRevision, $endRevision, $path),
            ['Content-Type' => 'text/xml']
        );

        if ($response['statusCode'] >= 400) {
            throw new ClientException(
                sprintf('Unable to read the commit log of "%s".', $target),
                $response['statusCode']
            );
        }

        return $report->parseResponse($response['body']);
    }

    /**
     * Create a local directory.
     *
     * @param string $path
     * @return void
     * @throws ClientException When the directory cannot be created.
     */
    protected function createDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new ClientException(sprintf('Unable to create the directory "%s".', $path));
        }
    }

    /**
     * Write a stream to a local file.
     *
     * @param string $path
     * @param resource $stream
     * @return void
     * @throws ClientException When the file cannot be written.
     */
    protected function writeFile(string $path, $stream): void
    {
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new ClientException(sprintf('Unable to write the file "%s".', $path));
        }

        $copied = stream_copy_to_stream($stream, $handle);
        fclose($handle);

        if ($copied === false) {
            throw new ClientException(sprintf('Unable to write the file "%s".', $path));
        }
    }
}
