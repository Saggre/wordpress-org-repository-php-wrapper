
Utility class for handling file paths.

***

* Full name: `\Saggre\WordPress\Repository\Util\Path`

## Properties

### separator

```php
protected string $separator
```

***

## Methods

### __construct

```php
public __construct(string $separator = DIRECTORY_SEPARATOR): mixed
```

**Parameters:**

| Parameter    | Type       | Description |
|--------------|------------|-------------|
| `$separator` | **string** |             |

***

### normalize

Normalizes a path string to use the specified directory separator.

```php
public normalize(string $path): string
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$path`   | **string** |             |

***

### explode

Explodes a path string into an array of parts.

```php
public explode(string $path): array
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$path`   | **string** |             |

***

### join

Joins two or more path strings into a canonical path.

```php
public join(string $paths): string
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$paths`  | **string** |             |

***

### startsWithSeparator

If the path starts with a directory separator.

```php
protected startsWithSeparator(string $path): bool
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$path`   | **string** |             |

***
