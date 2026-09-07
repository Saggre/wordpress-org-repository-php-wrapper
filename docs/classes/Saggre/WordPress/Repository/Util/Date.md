
Utility class for parsing the date formats used by WordPress.org.

***

* Full name: `\Saggre\WordPress\Repository\Util\Date`

## Methods

### parse

Parse an API date string, e.g. '2025-10-24 4:13am GMT' or '2008-07-06'.

```php
public static parse(string|null $value): \DateTimeImmutable|null
```

WordPress.org reports times in UTC. Values that carry no zone of their own are read as UTC
rather than as the host timezone, so the parsed instant does not depend on the environment.

* This method is **static**.
**Parameters:**

| Parameter | Type             | Description |
|-----------|------------------|-------------|
| `$value`  | **string\|null** |             |

**Return Value:**

Null when the value is empty or unparseable.

***
