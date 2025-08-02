
Base class for WordPress.org plugin and theme clients.

***

* Full name: `\Saggre\WordPress\Repository\BaseClient`
* This class is an **Abstract class**

## Constants

| Constant         | Visibility | Type | Value   |
|------------------|------------|------|---------|
| `CLIENT_VERSION` | public     |      | '1.0.0' |

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
public getDirectory(string $path): \League\Flysystem\DirectoryListing
```

**Parameters:**

| Parameter | Type       | Description                                       |
|-----------|------------|---------------------------------------------------|
| `$path`   | **string** | Relative file path from the plugin or theme root. |

**Return Value:**

Directory listing of the plugin or theme directory.

**Throws:**

On repository read error or if the path is not a directory.
- [`UnableToListContents`](../../../League/Flysystem/UnableToListContents)
On repository read error.
- [`FilesystemException`](../../../League/Flysystem/FilesystemException)

***
