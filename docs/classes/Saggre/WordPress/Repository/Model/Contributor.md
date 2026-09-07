
A plugin contributor as returned by the plugin API.

***

* Full name: `\Saggre\WordPress\Repository\Model\Contributor`

## Properties

### username

```php
public string $username
```

***

### displayName

```php
public ?string $displayName
```

***

### profile

```php
public ?string $profile
```

***

### avatar

```php
public ?string $avatar
```

***

## Methods

### __construct

```php
public __construct(string $username, ?string $displayName = null, ?string $profile = null, ?string $avatar = null): mixed
```

**Parameters:**

| Parameter      | Type        | Description |
|----------------|-------------|-------------|
| `$username`    | **string**  |             |
| `$displayName` | **?string** |             |
| `$profile`     | **?string** |             |
| `$avatar`      | **?string** |             |

***

### fromArray

Build a contributor from a single entry of the API contributors map.

```php
public static fromArray(string $username, array<string,mixed> $data): self
```

* This method is **static**.
**Parameters:**

| Parameter   | Type                    | Description                          |
|-------------|-------------------------|--------------------------------------|
| `$username` | **string**              | Map key, the WordPress.org username. |
| `$data`     | **array<string,mixed>** |                                      |

***
