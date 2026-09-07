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
