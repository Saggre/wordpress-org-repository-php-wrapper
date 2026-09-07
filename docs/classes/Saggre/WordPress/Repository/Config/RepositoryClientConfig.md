
Base configuration class for the WordPress.org SVN repository clients.

***

* Full name: `\Saggre\WordPress\Repository\Config\RepositoryClientConfig`
* Parent class: [`\Saggre\WordPress\Repository\Config\BaseClientConfig`](./BaseClientConfig)
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

**Throws:**

On empty slug or version.
- [`InvalidArgumentException`](../../../../InvalidArgumentException)

***

### getSlug

Get the slug of the plugin or theme.

```php
public getSlug(): string
```

***

### getVersion

Get the version of the plugin or theme.

```php
public getVersion(): string
```

***

## Inherited methods

### __construct

```php
public __construct(string $baseUrl, string $userAgent): mixed
```

**Parameters:**

| Parameter    | Type       | Description |
|--------------|------------|-------------|
| `$baseUrl`   | **string** |             |
| `$userAgent` | **string** |             |

***

### getBaseUrl

Get the base URL the client sends its requests to.

```php
public getBaseUrl(): string
```

***

### getUserAgent

Get the user agent string for the client.

```php
public getUserAgent(): string
```

***
