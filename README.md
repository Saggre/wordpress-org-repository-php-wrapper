# A WordPress.org Repository API wrapper for PHP

Use cases: Plugin and theme directory data, update checks, analysis.

[![codecov](https://img.shields.io/codecov/c/github/Saggre/wordpress-org-repository-php-wrapper)](https://codecov.io/gh/Saggre/wordpress-org-repository-php-wrapper)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper?ref=badge_shield)

This library provides a simple way to access the WordPress.org [plugins](https://wordpress.org/plugins/)
and [themes](https://wordpress.org/themes/) repositories. It allows you to retrieve raw plugin and theme files, list
directories, read commit logs, query the plugin directory and download releases.

## Installation

#### Installation via Composer

```bash
composer require --dev saggre/wordpress-org-repository-php-wrapper
```

## Usage examples

### Configuring the client

#### Plugin client

```php
// Client for the latest version (trunk) of the WooCommerce plugin
$config = new PluginClientConfig('woocommerce', 'trunk');
$client = new PluginClient($config);
```

#### Theme client

```php
// Client for version 1.2 of the Twenty Twenty-Five theme
$config = new ThemeClientConfig('twentytwentyfive', '1.2');
$client = new ThemeClient($config);
```

### Client methods

#### Get plugin or theme file contents

```php
$content = $client->getFile('readme.txt');

/*
 * === WooCommerce ===
 * Contributors: automattic, woocommerce
 * Tags: online store, ecommerce, shop, shopping cart, sell online
 * ...
 */
```

#### Get plugin or theme file contents as a stream

```php
$file = $client->getFileStream('readme.txt');
$content = stream_get_contents($file);

/*
 * === WooCommerce ===
 * Contributors: automattic, woocommerce
 * Tags: online store, ecommerce, shop, shopping cart, sell online
 * ...
 */
```

#### List plugin or theme directory contents

Pass `true` as the second argument to list the contents of subdirectories as well.

```php
use League\Flysystem\StorageAttributes;

$directory = $client->getDirectory();

$directory = array_map(
    fn(StorageAttributes $item) => $item->jsonSerialize(), 
    $directory->toArray()
)

/*
 * array(
 *     ...
 *     array(
 *         'type' => 'file',
 *         'path' => 'woocommerce/trunk/woocommerce.php',
 *         'file_size' => 1851,
 *         'visibility' => null,
 *         'last_modified' => 1753778097,
 *         'mime_type' => 'text/xml; charset="utf-8"',
 *         'extra_metadata' => array(),
 *     ),
 *     ...
 * );
 */
```

#### List a plugin's tagged versions

```php
$tags = $client->getTagsDirectory();

// array('1.5', '1.6', '1.7.2')
$versions = array_map(fn($tag) => basename($tag->path()), $tags->toArray());
```

#### Read the commit log

`getLog()` reads the history of the configured plugin or theme, `getRepositoryLog()` the history of every plugin or
theme at once. Both return the newest revision first, and both accept a revision range.

```php
$log = $client->getLog(limit: 10);

foreach ($log as $entry) {
    echo "r{$entry->revision} by {$entry->author} at {$entry->date->format('c')}: {$entry->message}\n";

    foreach ($entry->paths as $path) {
        // Tags are copies, so a copied path resolves the release a version was cut from.
        echo "  [{$path->action->value}] {$path->path} {$path->copyFromPath}\n";
    }
}
```

#### Compare two published versions

`diffVersions()` lists the files a release touched without downloading either tree. It resolves both tags, then reads
the revision range between them in a single request. Paths come back relative to the plugin root, deduplicated across
trunk and the new tag, which vendors commonly commit the same edit to. A deleted or copied directory is listed in place
of the files it removed or brought along, since the log does not name them.

```php
$paths = $client->diffVersions('4.4.3', '4.4.4');

foreach ($paths as $path) {
    // A path with textMods false changed only its properties, its bytes are identical.
    echo "[{$path->action->value}] {$path->path}\n";
}

// Fetch only what changed, then diff locally.
$before = (new PluginClient(new PluginClientConfig('gdpr-cookie-consent', '4.4.3')))->getFile('gdpr-cookie-consent.php');
```

A version that was published without ever being tagged throws `TagNotFoundException` rather than silently comparing
the wrong pair, and an old version that was tagged after the new one throws `InvalidArgumentException`.

Resolving the two tags is the expensive half: it reads the tag history, which for a plugin with hundreds of releases
costs far more than the diff itself. A caller comparing recent releases can cap that read, at the price of a
`TagNotFoundException` for a version tagged before the window.

```php
// Read the newest twenty tag revisions instead of a decade of them.
$paths = $client->diffVersions('4.4.3', '4.4.4', 20);
```

#### Map versions to revisions

```php
$tags = $client->getTagRevisions();

// Ordered by revision, oldest release first. Version strings cannot be sorted as text,
// where '1.10.4' lands between '1.1.9' and '1.2.0'.
foreach ($tags as $version => $entry) {
    // A tag is a directory copy, so it also names the trunk revision the release was cut from.
    echo "{$version} => r{$entry->revision} from {$entry->paths[0]->copyFromRevision}\n";
}
```

`getTagRevisions($limit)` reads only the newest `$limit` revisions of the `tags` directory. A release usually takes one
revision, but retagging a release and editing a file inside a tag take their own, so the window can hold fewer versions.

#### Read a revision range

`getChangedPaths()` reads the revisions between two bounds, optionally scoped to a subtree. Both bounds are inclusive,
and both are required: the server answers an empty report with HTTP 200 when the end revision is missing. An inverted
range throws `InvalidArgumentException`, as it does on `getLog()` and `getRepositoryLog()`.

```php
$log = $client->getChangedPaths(3686273, 3679496, 'trunk/admin');
```

#### Export a tagged version

Writes the whole tree of the configured version to a local directory, which recovers releases that are no longer
served by the distribution host.

```php
$files = $client->export('/tmp/hello-dolly-1.7.2');
```

### Plugin API client

Reads plugin metadata from the WordPress.org plugin API.

```php
use Saggre\WordPress\Repository\PluginApiClient;

$client = new PluginApiClient();
```

#### Enumerate plugins

Switch off the bulky prose and switch on the contributors to keep a page of results small.

```php
use Saggre\WordPress\Repository\Model\PluginBrowse;
use Saggre\WordPress\Repository\Model\PluginQuery;

$result = $client->queryPlugins(new PluginQuery(
    browse: PluginBrowse::Updated,
    page: 1,
    perPage: 250,
    fields: [
        'sections' => false,
        'description' => false,
        'screenshots' => false,
        'icons' => false,
        'contributors' => true,
    ],
));

// 71793 plugins on 288 pages, newest last_updated first
echo "{$result->results} plugins on {$result->pages} pages\n";

foreach ($result->plugins as $plugin) {
    echo "{$plugin->slug} {$plugin->version} {$plugin->lastUpdated->format('c')}\n";
}
```

#### Read one plugin's record

```php
$info = $client->getPluginInformation('hello-dolly');

// array('1.5' => 'https://downloads.wordpress.org/plugin/hello-dolly.1.5.zip', ...)
$versions = $info->versions;
```

#### Check whether a plugin is closed

```php
$status = $client->getPluginStatus('hana-flv-player');

if ($status->closed) {
    // 'security-issue' as of 2021-06-21
    echo "{$status->reason} as of {$status->closedDate->format('Y-m-d')}\n";
}
```

### Plugin download client

Downloads plugin releases from the WordPress.org distribution host. Only the current release is available without a
version, and withdrawn releases are no longer served even when they still exist in SVN.

```php
use Saggre\WordPress\Repository\PluginDownloadClient;

$client = new PluginDownloadClient();

$zip = $client->getZip('hello-dolly', '1.7.2');
$stream = $client->getZipStream('hello-dolly');
```

## API reference

### `PluginClient` and `ThemeClient`

Every method reads the slug and version held by the client's config. `getTagsDirectory()`, `getTagRevisions()` and
`diffVersions()` are plugin only, since the theme repository has no `tags` directory.

| Method                                                                            | Returns            | Description                                                              |
|-----------------------------------------------------------------------------------|--------------------|--------------------------------------------------------------------------|
| `getFile(string $path)`                                                           | `string`           | Contents of a file.                                                      |
| `getFileStream(string $path)`                                                     | `resource`         | Contents of a file as a stream.                                          |
| `getDirectory(string $path = '', bool $deep = false)`                             | `DirectoryListing` | Directory contents, optionally including subdirectories.                 |
| `getTagsDirectory()`                                                              | `DirectoryListing` | One entry per published version tag, with `lastModified` populated.      |
| `export(string $destination)`                                                     | `int`              | Writes the tree to a local directory and returns the number of files.    |
| `getLog(int $limit = 100, ?int $start = null, int $end = 0)`                       | `LogEntry[]`       | Commit log of this plugin or theme, newest revision first.               |
| `getRepositoryLog(int $limit = 100, ?int $start = null, int $end = 0)`             | `LogEntry[]`       | Commit log of every plugin or theme at once.                             |
| `getChangedPaths(int $start, int $end, string $path = '', int $limit = 0)`        | `LogEntry[]`       | Revisions in an inclusive range, optionally scoped to a subtree.         |
| `getTagRevisions(int $limit = 0)`                                                 | `LogEntry[]`       | Every published version to the revision that created its tag.           |
| `diffVersions(string $old, string $new, int $limit = 0)`                          | `LogPath[]`        | Files changed between two published versions, keyed by path.            |
| `getFilesystem()`                                                                 | `Filesystem`       | The underlying Flysystem instance, for anything the client does not do.  |

### `PluginApiClient`

| Method                                                    | Returns             | Description                                                      |
|-----------------------------------------------------------|---------------------|------------------------------------------------------------------|
| `queryPlugins(PluginQuery $query)`                        | `PluginQueryResult` | One page of the plugin directory.                                |
| `getPluginInformation(string $slug, array $fields = [])`  | `PluginInfo`        | Full record of one plugin, including its versions map.           |
| `getPluginStatus(string $slug)`                           | `PluginStatus`      | Whether a plugin is closed, and why.                             |

### `PluginDownloadClient`

| Method                                                | Returns    | Description                            |
|-------------------------------------------------------|------------|----------------------------------------|
| `getZipUrl(string $slug, ?string $version = null)`    | `string`   | Download URL of a release.             |
| `getZip(string $slug, ?string $version = null)`       | `string`   | Contents of the release archive.       |
| `getZipStream(string $slug, ?string $version = null)` | `resource` | Release archive as a stream.           |

Repository reads throw `League\Flysystem\FilesystemException`. Everything else throws
`Saggre\WordPress\Repository\Exception\ClientException`, whose code is the HTTP status of the failed response.
`TagNotFoundException` extends it and is thrown when a version has no tag.

## Running tests

```bash
# Clone the repository
git clone git@github.com:Saggre/wordpress-org-repository-php-wrapper.git

# Go to the cloned repository
cd wordpress-org-repository-php-wrapper

# Install dependencies
composer install

# Run PHPUnit in project root directory
./vendor/bin/phpunit
```

## Documentation

Code documentation is available in the [docs](./docs/Home.md) directory.

## License

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper?ref=badge_large)
