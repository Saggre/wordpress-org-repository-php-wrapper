
WordPress.org plugin distribution client.

***

* Full name: `\Saggre\WordPress\Repository\PluginDownloadClient`

## Constants

| Constant         | Visibility | Type | Value                                                                |
|------------------|------------|------|----------------------------------------------------------------------|
| `CLIENT_VERSION` | public     |      | \Saggre\WordPress\Repository\Config\BaseClientConfig::CLIENT_VERSION |

## Properties

### client

```php
protected \Sabre\HTTP\Client $client
```

***

### config

```php
protected \Saggre\WordPress\Repository\Config\PluginDownloadClientConfig $config
```

***

## Methods

### __construct

```php
public __construct(\Saggre\WordPress\Repository\Config\PluginDownloadClientConfig $config = new PluginDownloadClientConfig()): mixed
```

**Parameters:**

| Parameter | Type                                                               | Description |
|-----------|--------------------------------------------------------------------|-------------|
| `$config` | **\Saggre\WordPress\Repository\Config\PluginDownloadClientConfig** |             |

***

### createClient

Create a SabreHTTP Client instance.

```php
protected createClient(): \Sabre\HTTP\Client
```

***

### getZipUrl

Build the download URL of a plugin release.

```php
public getZipUrl(string $slug, string|null $version = null): string
```

**Parameters:**

| Parameter  | Type             | Description                                           |
|------------|------------------|-------------------------------------------------------|
| `$slug`    | **string**       |                                                       |
| `$version` | **string\|null** | Release to download, defaults to the current release. |

***

### getZip

Download a plugin release.

```php
public getZip(string $slug, string|null $version = null): string
```

Only the current release is available without a version. Withdrawn releases are no longer
served here even when they still exist in the SVN repository.

**Parameters:**

| Parameter  | Type             | Description                                           |
|------------|------------------|-------------------------------------------------------|
| `$slug`    | **string**       |                                                       |
| `$version` | **string\|null** | Release to download, defaults to the current release. |

**Return Value:**

The zip archive contents.

**Throws:**

When the release is not available.
- [`ClientException`](./Exception/ClientException)

***

### getZipStream

Download a plugin release as a stream.

```php
public getZipStream(string $slug, string|null $version = null): resource
```

**Parameters:**

| Parameter  | Type             | Description                                           |
|------------|------------------|-------------------------------------------------------|
| `$slug`    | **string**       |                                                       |
| `$version` | **string\|null** | Release to download, defaults to the current release. |

**Return Value:**

The zip archive stream.

**Throws:**

When the release is not available.
- [`ClientException`](./Exception/ClientException)

***

### download

Request a plugin release.

```php
protected download(string $slug, string|null $version): \Sabre\HTTP\ResponseInterface
```

**Parameters:**

| Parameter  | Type             | Description |
|------------|------------------|-------------|
| `$slug`    | **string**       |             |
| `$version` | **string\|null** |             |

**Throws:**

When the release is not available.
- [`ClientException`](./Exception/ClientException)

***
