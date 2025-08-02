
Base configuration class for WordPress.org plugin and theme clients.

***

* Full name: `\Saggre\WordPress\Repository\Config\BaseClientConfig`
* This class is an **Abstract class**

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
