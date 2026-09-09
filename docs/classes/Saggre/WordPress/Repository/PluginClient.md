
WordPress.org plugin client.

***

* Full name: `\Saggre\WordPress\Repository\PluginClient`
* Parent class: [`\Saggre\WordPress\Repository\BaseClient`](./BaseClient)

## Properties

### config

```php
protected \Saggre\WordPress\Repository\Config\PluginClientConfig $config
```

***

## Methods

### __construct

```php
public __construct(\Saggre\WordPress\Repository\Config\PluginClientConfig $config): mixed
```

**Parameters:**

| Parameter | Type                                                       | Description |
|-----------|------------------------------------------------------------|-------------|
| `$config` | **\Saggre\WordPress\Repository\Config\PluginClientConfig** |             |

***

### getTagsDirectory

List all tagged versions for this plugin.

```php
public getTagsDirectory(): \League\Flysystem\DirectoryListing
```

Returns a DirectoryListing of DirectoryAttributes, one per version tag.
Each entry has lastModified populated from the WebDAV getlastmodified property.

**Throws:**

On repository read error.
- [`FilesystemException`](../../../League/Flysystem/FilesystemException)

***

### getTagRevisions

Map every published version to the revision that created its tag.

```php
public getTagRevisions(int $limit = 0): array<string,\Saggre\WordPress\Repository\Model\LogEntry>
```

Reads the tag history in one request, so without a limit the cost grows with the number of
releases. A caller that only needs the newest releases can cap the revisions read, which is
the whole cost of a diff for a plugin with a long history. A tag is a directory copy, so the
entry also carries the trunk revision the release was cut from, in the copyFromRevision of
its path.

Ordered by revision, oldest release first. Version strings cannot be sorted as text, where
'1.10.4' lands between '1.1.9' and '1.2.0', but revision numbers are monotonic.

**Parameters:**

| Parameter | Type    | Description                                                                                                                                                                                        |
|-----------|---------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `$limit`  | **int** | Maximum number of tag revisions to read, newest first. 0 for no limit. A release usually takes one revision, but retagging a release and editing a file inside a tag take their own, so the window can hold fewer versions. |

**Return Value:**

Version string to the revision that added its tag.

**Throws:**

On repository read error.
- [`ClientException`](./Exception/ClientException)

***

### diffVersions

Get the files that changed between two published versions.

```php
public diffVersions(string $old, string $new, int $limit = 0): array<string,\Saggre\WordPress\Repository\Model\LogPath>
```

Resolves both tags, then reads the revision range between them in a single request, which
is the cheap alternative to downloading and comparing two complete trees.

Vendors commonly commit the same edit to trunk and to the new tag, so both trees are read
and deduplicated. The tag directory itself is a copy rather than a file change and is left
out, as is anything committed to an unrelated tag in the same range. A deleted or copied
directory is listed in place of the files it removed or brought along, since the log does
not name them.

Resolving the tags is the expensive half for a plugin with a long history, since it reads
the whole tag log to find two revisions. A caller diffing consecutive releases can cap that
read with $limit, at the price of a TagNotFoundException for a version tagged before the
window.

**Parameters:**

| Parameter | Type       | Description                                                                  |
|-----------|------------|------------------------------------------------------------------------------|
| `$old`    | **string** | The older version, e.g. '4.4.3'.                                             |
| `$new`    | **string** | The newer version, e.g. '4.4.4'.                                             |
| `$limit`  | **int**    | Maximum number of tag revisions to read, newest first. 0 for no limit.       |

**Return Value:**

Changed paths, keyed by their path relative to the plugin root.

**Throws:**

When either version has no tag in the revisions read.
- [`TagNotFoundException`](./Exception/TagNotFoundException)
When the old version was not tagged before the new one.
- [`InvalidArgumentException`](../../../InvalidArgumentException)
On repository read error.
- [`ClientException`](./Exception/ClientException)

***

### normalizePaths

Reduce the paths of a revision range to one entry per file of the plugin tree.

```php
protected normalizePaths(\Saggre\WordPress\Repository\Model\LogEntry[] $log, string $version): array<string,\Saggre\WordPress\Repository\Model\LogPath>
```

**Parameters:**

| Parameter  | Type                                              | Description                           |
|------------|---------------------------------------------------|---------------------------------------|
| `$log`     | **\Saggre\WordPress\Repository\Model\LogEntry[]** |                                       |
| `$version` | **string**                                        | The tagged version the range ends at. |

***

### stripPrefix

Strip the trunk or tag prefix from a repository absolute path.

```php
protected stripPrefix(string $path, string[] $prefixes): string|null
```

**Parameters:**

| Parameter   | Type         | Description |
|-------------|--------------|-------------|
| `$path`     | **string**   |             |
| `$prefixes` | **string[]** |             |

**Return Value:**

Null when the path lies outside every given tree.

***

## Inherited methods

### __construct

```php
public __construct(): mixed
```

***

### createClient

Create a SabreDAV Client instance.

```php
protected createClient(): \Sabre\DAV\Client
```

***

### createFilesystem

Create a League\Flysystem Filesystem instance using the WebDAV adapter.

```php
protected createFilesystem(\Sabre\DAV\Client $client): \League\Flysystem\Filesystem
```

**Parameters:**

| Parameter | Type                  | Description |
|-----------|-----------------------|-------------|
| `$client` | **\Sabre\DAV\Client** |             |

***

### getFilesystem

Get the League\Flysystem Filesystem instance.

```php
public getFilesystem(): \League\Flysystem\Filesystem
```

***

### getPath

Get the path for a given plugin or theme file.

```php
protected getPath(string $path): string
```

**Parameters:**

| Parameter | Type       | Description                                       |
|-----------|------------|---------------------------------------------------|
| `$path`   | **string** | Relative file path from the plugin or theme root. |

***

### getRootPath

Get the repository absolute path of the plugin or theme root, e.g. '/hello-dolly'.

```php
protected getRootPath(): string
```

***

### getFile

Get the content of a plugin or theme file.

```php
public getFile(string $path): string
```

**Parameters:**

| Parameter | Type       | Description                                       |
|-----------|------------|---------------------------------------------------|
| `$path`   | **string** | Relative file path from the plugin or theme root. |

**Return Value:**

File content.

**Throws:**

On repository read error.
- [`FilesystemException`](../../../League/Flysystem/FilesystemException)

***

### getFileStream

Get the content of a plugin or theme file as a stream.

```php
public getFileStream(string $path): resource
```

**Parameters:**

| Parameter | Type       | Description                                       |
|-----------|------------|---------------------------------------------------|
| `$path`   | **string** | Relative file path from the plugin or theme root. |

**Return Value:**

File content stream.

**Throws:**

On repository read error.
- [`FilesystemException`](../../../League/Flysystem/FilesystemException)

***

### getDirectory

Get the content of a plugin or theme directory.

```php
public getDirectory(string $path = '', bool $deep = false): \League\Flysystem\DirectoryListing
```

**Parameters:**

| Parameter | Type       | Description                                             |
|-----------|------------|---------------------------------------------------------|
| `$path`   | **string** | Relative directory path from the plugin or theme root.  |
| `$deep`   | **bool**   | Whether to list the contents of subdirectories as well. |

**Return Value:**

Directory listing of the plugin or theme directory.

**Throws:**

On repository read error or if the path is not a directory.
- [`UnableToListContents`](../../../League/Flysystem/UnableToListContents)
On repository read error.
- [`FilesystemException`](../../../League/Flysystem/FilesystemException)

***

### export

Write the tree of the configured version to a local directory.

```php
public export(string $destination): int
```

The repository equivalent of an svn export, which retrieves builds that are no longer
available on the distribution host.

**Parameters:**

| Parameter      | Type       | Description                                                |
|----------------|------------|------------------------------------------------------------|
| `$destination` | **string** | Local directory to write to. Created if it does not exist. |

**Return Value:**

The number of files written.

**Throws:**

When the destination cannot be written to.
- [`ClientException`](./Exception/ClientException)
On repository read error.
- [`FilesystemException`](../../../League/Flysystem/FilesystemException)

***

### getLog

Get the commit log of the configured plugin or theme, newest revision first.

```php
public getLog(int $limit = 100, int|null $startRevision = null, int $endRevision = 0): \Saggre\WordPress\Repository\Model\LogEntry[]
```

**Parameters:**

| Parameter        | Type          | Description                                                |
|------------------|---------------|------------------------------------------------------------|
| `$limit`         | **int**       | Maximum number of revisions to return.                     |
| `$startRevision` | **int\|null** | Revision to start from, defaults to the youngest revision. |
| `$endRevision`   | **int**       | Revision to stop at.                                       |

**Throws:**

On repository read error.
- [`ClientException`](./Exception/ClientException)
On a negative end revision or an inverted range.
- [`InvalidArgumentException`](../../../InvalidArgumentException)

***

### getRepositoryLog

Get the commit log of the whole repository, newest revision first.

```php
public getRepositoryLog(int $limit = 100, int|null $startRevision = null, int $endRevision = 0): \Saggre\WordPress\Repository\Model\LogEntry[]
```

A single revision spans every plugin or theme changed by that commit.

**Parameters:**

| Parameter        | Type          | Description                                                |
|------------------|---------------|------------------------------------------------------------|
| `$limit`         | **int**       | Maximum number of revisions to return.                     |
| `$startRevision` | **int\|null** | Revision to start from, defaults to the youngest revision. |
| `$endRevision`   | **int**       | Revision to stop at.                                       |

**Throws:**

On repository read error.
- [`ClientException`](./Exception/ClientException)
On a negative end revision or an inverted range.
- [`InvalidArgumentException`](../../../InvalidArgumentException)

***

### getChangedPaths

Get the revisions that changed a path of the configured plugin or theme, newest first.

```php
public getChangedPaths(int $startRevision, int $endRevision, string $path = '', int $limit = 0): \Saggre\WordPress\Repository\Model\LogEntry[]
```

The range is inclusive at both ends, as in getLog(). Scoping to a path selects the
revisions; each of them still reports every path it touched, including paths outside the
scope, so a revision that changed both trunk and a tag lists both.

**Parameters:**

| Parameter        | Type       | Description                                                              |
|------------------|------------|--------------------------------------------------------------------------|
| `$startRevision` | **int**    | Newer bound of the range.                                                |
| `$endRevision`   | **int**    | Older bound of the range.                                                |
| `$path`          | **string** | Path relative to the plugin or theme root, e.g. 'tags' or 'trunk/admin'. |
| `$limit`         | **int**    | Maximum number of revisions to return. 0 for no limit.                   |

**Throws:**

On repository read error.
- [`ClientException`](./Exception/ClientException)
On a negative end revision or an inverted range.
- [`InvalidArgumentException`](../../../InvalidArgumentException)

***

### getLogForPath

Run an SVN log-report against a repository path.

```php
protected getLogForPath(string $target, int $limit, int|null $startRevision, int $endRevision, string $path = ''): \Saggre\WordPress\Repository\Model\LogEntry[]
```

The server only answers a REPORT at the repository root or at a plugin or theme root, so
narrower scopes go into the request body rather than into the target.

**Parameters:**

| Parameter        | Type          | Description                                                |
|------------------|---------------|------------------------------------------------------------|
| `$target`        | **string**    | Repository absolute path to send the report to.            |
| `$limit`         | **int**       |                                                            |
| `$startRevision` | **int\|null** |                                                            |
| `$endRevision`   | **int**       |                                                            |
| `$path`          | **string**    | Path relative to the target, to restrict the revisions to. |

**Throws:**

On repository read error.
- [`ClientException`](./Exception/ClientException)
On a negative end revision or an inverted range.
- [`InvalidArgumentException`](../../../InvalidArgumentException)

***

### createDirectory

Create a local directory.

```php
protected createDirectory(string $path): void
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$path`   | **string** |             |

**Throws:**

When the directory cannot be created.
- [`ClientException`](./Exception/ClientException)

***

### writeFile

Write a stream to a local file.

```php
protected writeFile(string $path, resource $stream): void
```

**Parameters:**

| Parameter | Type         | Description |
|-----------|--------------|-------------|
| `$path`   | **string**   |             |
| `$stream` | **resource** |             |

**Throws:**

When the file cannot be written.
- [`ClientException`](./Exception/ClientException)

***
