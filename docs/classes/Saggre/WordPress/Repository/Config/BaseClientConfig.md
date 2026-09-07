
Base configuration class for WordPress.org clients.

***

* Full name: `\Saggre\WordPress\Repository\Config\BaseClientConfig`
* This class is an **Abstract class**

## Constants

| Constant         | Visibility | Type | Value   |
|------------------|------------|------|---------|
| `CLIENT_VERSION` | public     |      | '1.0.0' |

## Properties

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
