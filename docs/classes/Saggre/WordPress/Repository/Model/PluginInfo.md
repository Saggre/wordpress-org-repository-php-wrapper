
A plugin record as returned by the plugin API.

Fields the API omits, either because the plugin has none or because they were switched off through
PluginQuery::$fields, are null or empty. The complete decoded payload is available in $raw.

***

* Full name: `\Saggre\WordPress\Repository\Model\PluginInfo`

## Properties

### slug

```php
public string $slug
```

***

### name

```php
public ?string $name
```

***

### version

```php
public ?string $version
```

***

### author

```php
public ?string $author
```

***

### authorProfile

```php
public ?string $authorProfile
```

***

### homepage

```php
public ?string $homepage
```

***

### donateLink

```php
public ?string $donateLink
```

***

### shortDescription

```php
public ?string $shortDescription
```

***

### downloadLink

```php
public ?string $downloadLink
```

***

### requires

```php
public ?string $requires
```

***

### requiresPhp

```php
public ?string $requiresPhp
```

***

### tested

```php
public ?string $tested
```

***

### lastUpdated

```php
public ?\DateTimeImmutable $lastUpdated
```

***

### added

```php
public ?\DateTimeImmutable $added
```

***

### activeInstalls

```php
public ?int $activeInstalls
```

***

### downloaded

```php
public ?int $downloaded
```

***

### rating

```php
public ?float $rating
```

***

### numRatings

```php
public ?int $numRatings
```

***

### contributors

```php
public array $contributors
```

***

### tags

```php
public array $tags
```

***

### versions

```php
public array $versions
```

***

### sections

```php
public array $sections
```

***

### raw

```php
public array $raw
```

***

## Methods

### __construct

```php
public __construct(string $slug, ?string $name = null, ?string $version = null, ?string $author = null, ?string $authorProfile = null, ?string $homepage = null, ?string $donateLink = null, ?string $shortDescription = null, ?string $downloadLink = null, ?string $requires = null, ?string $requiresPhp = null, ?string $tested = null, ?\DateTimeImmutable $lastUpdated = null, ?\DateTimeImmutable $added = null, ?int $activeInstalls = null, ?int $downloaded = null, ?float $rating = null, ?int $numRatings = null, array<string,\Saggre\WordPress\Repository\Model\Contributor> $contributors = [], array<string,string> $tags = [], array<string,string> $versions = [], array<string,string> $sections = [], array<string,mixed> $raw = []): mixed
```

**Parameters:**

| Parameter           | Type                                                             | Description                       |
|---------------------|------------------------------------------------------------------|-----------------------------------|
| `$slug`             | **string**                                                       |                                   |
| `$name`             | **?string**                                                      |                                   |
| `$version`          | **?string**                                                      |                                   |
| `$author`           | **?string**                                                      |                                   |
| `$authorProfile`    | **?string**                                                      |                                   |
| `$homepage`         | **?string**                                                      |                                   |
| `$donateLink`       | **?string**                                                      |                                   |
| `$shortDescription` | **?string**                                                      |                                   |
| `$downloadLink`     | **?string**                                                      |                                   |
| `$requires`         | **?string**                                                      |                                   |
| `$requiresPhp`      | **?string**                                                      |                                   |
| `$tested`           | **?string**                                                      |                                   |
| `$lastUpdated`      | **?\DateTimeImmutable**                                          |                                   |
| `$added`            | **?\DateTimeImmutable**                                          |                                   |
| `$activeInstalls`   | **?int**                                                         |                                   |
| `$downloaded`       | **?int**                                                         |                                   |
| `$rating`           | **?float**                                                       |                                   |
| `$numRatings`       | **?int**                                                         |                                   |
| `$contributors`     | **array<string,\Saggre\WordPress\Repository\Model\Contributor>** | Keyed by WordPress.org username.  |
| `$tags`             | **array<string,string>**                                         | Tag slug to tag label.            |
| `$versions`         | **array<string,string>**                                         | Version number to download URL.   |
| `$sections`         | **array<string,string>**                                         | Section name to HTML content.     |
| `$raw`              | **array<string,mixed>**                                          | The complete decoded API payload. |

***

### fromArray

Build a plugin record from a decoded API payload.

```php
public static fromArray(array<string,mixed> $data): self
```

* This method is **static**.
**Parameters:**

| Parameter | Type                    | Description |
|-----------|-------------------------|-------------|
| `$data`   | **array<string,mixed>** |             |

***

### toString

Read a field the API returns either as a string or as false when it is not set.

```php
protected static toString(array<string,mixed> $data, string $key): string|null
```

* This method is **static**.
**Parameters:**

| Parameter | Type                    | Description |
|-----------|-------------------------|-------------|
| `$data`   | **array<string,mixed>** |             |
| `$key`    | **string**              |             |

***

### toArray

Read a field the API returns either as a map or as false when it is empty.

```php
protected static toArray(array<string,mixed> $data, string $key): array
```

* This method is **static**.
**Parameters:**

| Parameter | Type                    | Description |
|-----------|-------------------------|-------------|
| `$data`   | **array<string,mixed>** |             |
| `$key`    | **string**              |             |

***
