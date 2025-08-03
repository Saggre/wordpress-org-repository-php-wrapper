# A WordPress.org Repository API wrapper for PHP

Use cases: Plugin and theme directory data, update checks, analysis.

![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/Saggre/wordpress-org-repository-php-wrapper/test.yml)
![Codecov](https://img.shields.io/codecov/c/github/Saggre/wordpress-org-repository-php-wrapper)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper?ref=badge_shield)

This library provides a simple way to access the WordPress.org [plugins](https://wordpress.org/plugins/)
and [themes](https://wordpress.org/themes/) repositories. It allows you to retrieve raw plugin and theme files and list
directories.

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

Code documentation is available in the [docs](.docs/Home.md) directory.


## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FSaggre%2Fwordpress-org-repository-php-wrapper?ref=badge_large)