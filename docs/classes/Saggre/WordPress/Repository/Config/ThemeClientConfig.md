
Configuration class for the WordPress.org Theme Client.

***

* Full name: `\Saggre\WordPress\Repository\Config\ThemeClientConfig`
* Parent class: [`\Saggre\WordPress\Repository\Config\RepositoryClientConfig`](./RepositoryClientConfig)

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
public __construct(string $slug, string $version = 'trunk', string $baseUrl = 'https://themes.svn.wordpress.org', string $userAgent = 'wordpress-org-repository-php-wrapper/' . self::CLIENT_VERSION): mixed
```

**Parameters:**

| Parameter    | Type       | Description                                    |
|--------------|------------|------------------------------------------------|
| `$slug`      | **string** | The slug of the theme.                         |
| `$version`   | **string** | The version of the theme, defaults to 'trunk'. |
| `$baseUrl`   | **string** | The base URL for the theme repository.         |
| `$userAgent` | **string** | The user agent string for HTTP request.        |

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

**Throws:**

On empty slug or version.
- [`InvalidArgumentException`](../../../../InvalidArgumentException)

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
