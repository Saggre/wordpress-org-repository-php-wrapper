
A single page of plugin query API results.

***

* Full name: `\Saggre\WordPress\Repository\Model\PluginQueryResult`

## Properties

### plugins

```php
public array $plugins
```

***

### page

```php
public int $page
```

***

### pages

```php
public int $pages
```

***

### results

```php
public int $results
```

***

## Methods

### __construct

```php
public __construct(\Saggre\WordPress\Repository\Model\PluginInfo[] $plugins, int $page, int $pages, int $results): mixed
```

**Parameters:**

| Parameter  | Type                                                | Description                                     |
|------------|-----------------------------------------------------|-------------------------------------------------|
| `$plugins` | **\Saggre\WordPress\Repository\Model\PluginInfo[]** | The plugins on this page.                       |
| `$page`    | **int**                                             | The page number this result represents.         |
| `$pages`   | **int**                                             | The total number of pages available.            |
| `$results` | **int**                                             | The total number of plugins matching the query. |

***

### fromArray

Build a result page from a decoded API payload.

```php
public static fromArray(array<string,mixed> $data): self
```

* This method is **static**.
**Parameters:**

| Parameter | Type                    | Description |
|-----------|-------------------------|-------------|
| `$data`   | **array<string,mixed>** |             |

***
