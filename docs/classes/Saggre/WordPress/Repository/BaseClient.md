
Base class for WordPress.org plugin and theme clients.

***

* Full name: `\Saggre\WordPress\Repository\BaseClient`
* This class is an **Abstract class**

## Constants

| Constant         | Visibility | Type | Value                                                                |
|------------------|------------|------|----------------------------------------------------------------------|
| `CLIENT_VERSION` | public     |      | \Saggre\WordPress\Repository\Config\BaseClientConfig::CLIENT_VERSION |

## Properties

### client

```php
protected \Sabre\DAV\Client $client
```

***

### filesystem

```php
protected \League\Flysystem\Filesystem $filesystem
```

***

## Methods

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
When the start revision is older than the end revision.
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
