
Encodes and decodes the SVN log-report protocol used by the REPORT method.

***

* Full name: `\Saggre\WordPress\Repository\Util\LogReport`

## Constants

| Constant        | Visibility | Type | Value                                                                                                                                                                                                                                                                                                                |
|-----------------|------------|------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `NAMESPACE_SVN` | public     |      | 'svn:'                                                                                                                                                                                                                                                                                                               |
| `NAMESPACE_DAV` | public     |      | 'DAV:'                                                                                                                                                                                                                                                                                                               |
| `PATH_ELEMENTS` | protected  |      | ['added-path' => \Saggre\WordPress\Repository\Model\LogPathAction::Added, 'modified-path' => \Saggre\WordPress\Repository\Model\LogPathAction::Modified, 'deleted-path' => \Saggre\WordPress\Repository\Model\LogPathAction::Deleted, 'replaced-path' => \Saggre\WordPress\Repository\Model\LogPathAction::Replaced] |

## Methods

### createRequestBody

Build the request body of a log-report.

```php
public createRequestBody(int $limit, int|null $startRevision = null, int $endRevision): string
```

**Parameters:**

| Parameter        | Type          | Description                                                |
|------------------|---------------|------------------------------------------------------------|
| `$limit`         | **int**       | Maximum number of revisions to return, newest first.       |
| `$startRevision` | **int\|null** | Revision to start from, defaults to the youngest revision. |
| `$endRevision`   | **int**       | Revision to stop at.                                       |

***

### parseResponse

Parse the response body of a log-report into log entries, newest revision first.

```php
public parseResponse(string $body): \Saggre\WordPress\Repository\Model\LogEntry[]
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$body`   | **string** |             |

**Throws:**

On an unparseable response body.
- [`ClientException`](../Exception/ClientException)

***

### getPaths

Get the changed paths of a single log item, in the order the server reports them.

```php
protected getPaths(\DOMElement $item): \Saggre\WordPress\Repository\Model\LogPath[]
```

**Parameters:**

| Parameter | Type            | Description |
|-----------|-----------------|-------------|
| `$item`   | **\DOMElement** |             |

***

### getValue

Get the text content of a single child element of a log item.

```php
protected getValue(\DOMXPath $xpath, \DOMElement $item, string $expression): string|null
```

**Parameters:**

| Parameter     | Type            | Description |
|---------------|-----------------|-------------|
| `$xpath`      | **\DOMXPath**   |             |
| `$item`       | **\DOMElement** |             |
| `$expression` | **string**      |             |

***
