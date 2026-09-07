
Parameters for a plugin query API request.

***

* Full name: `\Saggre\WordPress\Repository\Model\PluginQuery`

## Properties

### browse

```php
public ?\Saggre\WordPress\Repository\Model\PluginBrowse $browse
```

***

### search

```php
public ?string $search
```

***

### tag

```php
public ?string $tag
```

***

### author

```php
public ?string $author
```

***

### page

```php
public int $page
```

***

### perPage

```php
public int $perPage
```

***

### fields

```php
public array $fields
```

***

## Methods

### __construct

```php
public __construct(\Saggre\WordPress\Repository\Model\PluginBrowse|null $browse = null, string|null $search = null, string|null $tag = null, string|null $author = null, int $page = 1, int $perPage = 250, array<string,bool> $fields = []): mixed
```

**Parameters:**

| Parameter  | Type                                                      | Description                                                                    |
|------------|-----------------------------------------------------------|--------------------------------------------------------------------------------|
| `$browse`  | **\Saggre\WordPress\Repository\Model\PluginBrowse\|null** | Browse mode, e.g. PluginBrowse::Updated for the most recently updated plugins. |
| `$search`  | **string\|null**                                          | Free text search term.                                                         |
| `$tag`     | **string\|null**                                          | Plugin tag to filter by.                                                       |
| `$author`  | **string\|null**                                          | WordPress.org username to filter by.                                           |
| `$page`    | **int**                                                   | Page number, starting at 1.                                                    |
| `$perPage` | **int**                                                   | Results per page. The API caps this at 250.                                    |
| `$fields`  | **array<string,bool>**                                    | Response field toggles, e.g. ['sections' => false, 'contributors' => true].    |

***

### toRequestParameters

Build the request parameters sent as request[...] in the query string.

```php
public toRequestParameters(): array<string,mixed>
```

***
