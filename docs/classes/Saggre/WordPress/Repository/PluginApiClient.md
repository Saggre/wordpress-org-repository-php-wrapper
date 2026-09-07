
WordPress.org plugin API client.

***

* Full name: `\Saggre\WordPress\Repository\PluginApiClient`

## Constants

| Constant         | Visibility | Type | Value                                                                |
|------------------|------------|------|----------------------------------------------------------------------|
| `CLIENT_VERSION` | public     |      | \Saggre\WordPress\Repository\Config\BaseClientConfig::CLIENT_VERSION |

## Properties

### client

```php
protected \Sabre\HTTP\Client $client
```

***

### config

```php
protected \Saggre\WordPress\Repository\Config\PluginApiClientConfig $config
```

***

## Methods

### __construct

```php
public __construct(\Saggre\WordPress\Repository\Config\PluginApiClientConfig $config = new PluginApiClientConfig()): mixed
```

**Parameters:**

| Parameter | Type                                                          | Description |
|-----------|---------------------------------------------------------------|-------------|
| `$config` | **\Saggre\WordPress\Repository\Config\PluginApiClientConfig** |             |

***

### createClient

Create a SabreHTTP Client instance.

```php
protected createClient(): \Sabre\HTTP\Client
```

***

### queryPlugins

Query the plugin directory for a single page of plugins.

```php
public queryPlugins(\Saggre\WordPress\Repository\Model\PluginQuery $query): \Saggre\WordPress\Repository\Model\PluginQueryResult
```

**Parameters:**

| Parameter | Type                                               | Description |
|-----------|----------------------------------------------------|-------------|
| `$query`  | **\Saggre\WordPress\Repository\Model\PluginQuery** |             |

**Throws:**

On API error.
- [`ClientException`](./Exception/ClientException)

***

### getPluginInformation

Read the full record of a single plugin, including its versions map.

```php
public getPluginInformation(string $slug, array<string,bool> $fields = []): \Saggre\WordPress\Repository\Model\PluginInfo
```

**Parameters:**

| Parameter | Type                   | Description                                         |
|-----------|------------------------|-----------------------------------------------------|
| `$slug`   | **string**             |                                                     |
| `$fields` | **array<string,bool>** | Response field toggles, e.g. ['sections' => false]. |

**Throws:**

On API error, including closed and unknown plugins.
- [`ClientException`](./Exception/ClientException)

***

### getPluginStatus

Check whether a plugin is still available in the plugin directory.

```php
public getPluginStatus(string $slug): \Saggre\WordPress\Repository\Model\PluginStatus
```

A closed plugin is reported with a non-2xx status, so the response body is the answer.

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$slug`   | **string** |             |

**Throws:**

On API error, including unknown plugins.
- [`ClientException`](./Exception/ClientException)

***

### getQueryUrl

Build the URL of a plugins/info/1.2 request.

```php
protected getQueryUrl(string $action, array<string,mixed> $request): string
```

**Parameters:**

| Parameter  | Type                    | Description                      |
|------------|-------------------------|----------------------------------|
| `$action`  | **string**              |                                  |
| `$request` | **array<string,mixed>** | Parameters sent as request[...]. |

***

### getStatusUrl

Build the URL of a plugins/info/1.0 request.

```php
protected getStatusUrl(string $slug): string
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$slug`   | **string** |             |

***

### get

Send a GET request and decode its JSON body.

```php
protected get(string $url): array{0: int, 1: array<string,mixed>}
```

**Parameters:**

| Parameter | Type       | Description |
|-----------|------------|-------------|
| `$url`    | **string** |             |

**Return Value:**

The HTTP status code and the decoded body.

**Throws:**

When the response body is not JSON.
- [`ClientException`](./Exception/ClientException)

***

### assertSuccess

Throw when the API reported an error.

```php
protected assertSuccess(array<string,mixed> $data, int $status, string $subject): void
```

**Parameters:**

| Parameter  | Type                    | Description                                    |
|------------|-------------------------|------------------------------------------------|
| `$data`    | **array<string,mixed>** |                                                |
| `$status`  | **int**                 |                                                |
| `$subject` | **string**              | The plugin slug or request the error is about. |

**Throws:**

- [`ClientException`](./Exception/ClientException)

***
