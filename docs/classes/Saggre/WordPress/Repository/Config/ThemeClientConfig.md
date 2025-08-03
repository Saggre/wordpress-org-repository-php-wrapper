
Configuration class for the WordPress.org Theme Client.

***

* Full name: `\Saggre\WordPress\Repository\Config\ThemeClientConfig`
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
public __construct(string $slug, string $version = 'trunk', string $baseUrl = 'https://themes.svn.wordpress.org', string $userAgent = 'wordpress-org-repository-php-wrapper/' . ThemeClient::CLIENT_VERSION): mixed
```

**Parameters:**

| Parameter    | Type       | Description                                                                                                                                                                                                                                                                                        |
|--------------|------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `$slug`      | **string** | The slug of the theme.
** @param string $version The version of the theme, defaults to 'trunk'.
** @param string $baseUrl The base URL for the theme repository.
** @param string $userAgent The user agent string for HTTP request.
** @throws InvalidArgumentException On empty slug or version. |
| `$version`   | **string** |                                                                                                                                                                                                                                                                                                    |
| `$baseUrl`   | **string** |                                                                                                                                                                                                                                                                                                    |
| `$userAgent` | **string** |                                                                                                                                                                                                                                                                                                    |

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
