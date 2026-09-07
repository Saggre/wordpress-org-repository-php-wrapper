
A single revision of the WordPress.org SVN repository.

***

* Full name: `\Saggre\WordPress\Repository\Model\LogEntry`

## Properties

### revision

```php
public int $revision
```

***

### author

```php
public ?string $author
```

***

### date

```php
public ?\DateTimeImmutable $date
```

***

### message

```php
public ?string $message
```

***

### paths

```php
public array $paths
```

***

## Methods

### __construct

```php
public __construct(int $revision, string|null $author = null, \DateTimeImmutable|null $date = null, string|null $message = null, \Saggre\WordPress\Repository\Model\LogPath[] $paths = []): mixed
```

**Parameters:**

| Parameter   | Type                                             | Description                         |
|-------------|--------------------------------------------------|-------------------------------------|
| `$revision` | **int**                                          | The revision number.                |
| `$author`   | **string\|null**                                 | The committer username.             |
| `$date`     | **\DateTimeImmutable\|null**                     | The commit timestamp.               |
| `$message`  | **string\|null**                                 | The commit message.                 |
| `$paths`    | **\Saggre\WordPress\Repository\Model\LogPath[]** | The paths changed by this revision. |

***
