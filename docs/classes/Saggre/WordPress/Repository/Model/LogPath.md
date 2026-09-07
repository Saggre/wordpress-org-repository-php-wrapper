
A single path changed by an SVN revision.

***

* Full name: `\Saggre\WordPress\Repository\Model\LogPath`

## Properties

### path

```php
public string $path
```

***

### action

```php
public \Saggre\WordPress\Repository\Model\LogPathAction $action
```

***

### nodeKind

```php
public ?string $nodeKind
```

***

### copyFromPath

```php
public ?string $copyFromPath
```

***

### copyFromRevision

```php
public ?int $copyFromRevision
```

***

## Methods

### __construct

```php
public __construct(string $path, \Saggre\WordPress\Repository\Model\LogPathAction $action, string|null $nodeKind = null, string|null $copyFromPath = null, int|null $copyFromRevision = null): mixed
```

**Parameters:**

| Parameter           | Type                                                 | Description                                                              |
|---------------------|------------------------------------------------------|--------------------------------------------------------------------------|
| `$path`             | **string**                                           | Repository absolute path, e.g. '/hello-dolly/tags/1.7.2/readme.txt'.     |
| `$action`           | **\Saggre\WordPress\Repository\Model\LogPathAction** | How the path changed in this revision.                                   |
| `$nodeKind`         | **string\|null**                                     | 'file' or 'dir'.                                                         |
| `$copyFromPath`     | **string\|null**                                     | Source path when the node was copied, e.g. the trunk a tag was cut from. |
| `$copyFromRevision` | **int\|null**                                        | Source revision when the node was copied.                                |

***
