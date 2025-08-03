
Configuration class for the WordPress.org Plugin Client.

***

* Full name: `\Saggre\WordPress\Repository\Config\PluginClientConfig`
* Parent class: [`\Saggre\WordPress\Repository\Config\BaseClientConfig`](./BaseClientConfig)

## Properties

### slug

```php
protected string $slug
```

***

### version

```php
protected string $version
```

***

### baseUrl

```php
protected string $baseUrl
```

***

### userAgent

```php
protected string $userAgent
```

***

## Methods

### __construct

```php
public __construct(string $slug, string $version = 'trunk', string $baseUrl = 'https://plugins.svn.wordpress.org', string $userAgent = 'wordpress-org-repository-php-wrapper/' . PluginClient::CLIENT_VERSION): mixed
```

**Parameters:**

| Parameter    | Type       | Description                                     |
|--------------|------------|-------------------------------------------------|
| `$slug`      | **string** | The slug of the plugin.                         |
| `$version`   | **string** | The version of the plugin, defaults to 'trunk'. |
| `$baseUrl`   | **string** | The base URL for the plugin repository.         |
| `$userAgent` | **string** | The user agent string for HTTP request.         |

**Throws:**

On empty slug or version.
- [`InvalidArgumentException`](../../../../InvalidArgumentException)

***

## Inherited methods

### __construct

```php
public __construct(string $slug, string $version, string $baseUrl, string $userAgent): mixed
```

**Parameters:**

| Parameter    | Type       | Description |
|--------------|------------|-------------|
| `$slug`      | **string** |             |
| `$version`   | **string** |             |
| `$baseUrl`   | **string** |             |
| `$userAgent` | **string** |             |

***

### getSlug

Get the slug of the plugin.

```php
public getSlug(): string
```

***

### getVersion

Get the version of the plugin.

```php
public getVersion(): string
```

***

### getBaseUrl

Get the base URL for the plugin repository.

```php
public getBaseUrl(): string
```

***

### getUserAgent

Get the user agent string for the plugin client.

```php
public getUserAgent(): string
```

***
