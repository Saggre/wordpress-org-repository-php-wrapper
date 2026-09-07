
Availability of a plugin in the plugin directory.

A closed plugin keeps its record in the API, but is no longer downloadable.

***

* Full name: `\Saggre\WordPress\Repository\Model\PluginStatus`

## Properties

### slug

```php
public string $slug
```

***

### closed

```php
public bool $closed
```

***

### name

```php
public ?string $name
```

***

### closedDate

```php
public ?\DateTimeImmutable $closedDate
```

***

### reason

```php
public ?string $reason
```

***

### reasonText

```php
public ?string $reasonText
```

***

## Methods

### __construct

```php
public __construct(string $slug, bool $closed, ?string $name = null, ?\DateTimeImmutable $closedDate = null, ?string $reason = null, ?string $reasonText = null): mixed
```

**Parameters:**

| Parameter     | Type                    | Description |
|---------------|-------------------------|-------------|
| `$slug`       | **string**              |             |
| `$closed`     | **bool**                |             |
| `$name`       | **?string**             |             |
| `$closedDate` | **?\DateTimeImmutable** |             |
| `$reason`     | **?string**             |             |
| `$reasonText` | **?string**             |             |

***

### fromArray

Build a status from a decoded plugins/info/1.0 payload.

```php
public static fromArray(string $slug, array<string,mixed> $data): self
```

* This method is **static**.
**Parameters:**

| Parameter | Type                    | Description |
|-----------|-------------------------|-------------|
| `$slug`   | **string**              |             |
| `$data`   | **array<string,mixed>** |             |

***
