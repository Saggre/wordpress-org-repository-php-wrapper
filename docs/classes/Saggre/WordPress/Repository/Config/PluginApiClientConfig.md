
Configuration class for the WordPress.org Plugin API Client.

***

* Full name: `\Saggre\WordPress\Repository\Config\PluginApiClientConfig`
* Parent class: [`\Saggre\WordPress\Repository\Config\BaseClientConfig`](./BaseClientConfig)

## Methods

### __construct

```php
public __construct(string $baseUrl = 'https://api.wordpress.org', string $userAgent = 'wordpress-org-repository-php-wrapper/' . self::CLIENT_VERSION): mixed
```

**Parameters:**

| Parameter    | Type       | Description                              |
|--------------|------------|------------------------------------------|
| `$baseUrl`   | **string** | The base URL for the plugin API.         |
| `$userAgent` | **string** | The user agent string for HTTP requests. |

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
