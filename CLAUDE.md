# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install

# Run all tests
./vendor/bin/phpunit

# Run a single test suite
./vendor/bin/phpunit --testsuite Unit
./vendor/bin/phpunit --testsuite Functional

# Run a single test file
./vendor/bin/phpunit test/Unit/Util/PathTest.php

# Generate docs
composer run create-docs
```

## Architecture

This is a general PHP library (PSR-12, short array syntax `[]`, no Yoda comparisons). It wraps the WordPress.org SVN repositories for plugins and themes via WebDAV.

**Transport layer:** `BaseClient` uses a SabreDAV `Client` talking to WordPress.org SVN over WebDAV, exposed through a League Flysystem `Filesystem`. `PluginClient` and `ThemeClient` extend `BaseClient` with no additional logic beyond holding their respective config.

**Config:** `PluginClientConfig` and `ThemeClientConfig` extend `BaseClientConfig`. They hold `slug`, `version`, `baseUrl`, and `userAgent`. Version `'trunk'` maps to the trunk path; any other value maps to `tags/<version>`.

**Path construction:** `BaseClient::getPath()` uses `Util\Path` to build the full SVN path: `/<slug>/<version>/<file>` for trunk or `/<slug>/tags/<version>/<file>` for tagged releases.

**Tests:** `test/Unit/` covers pure logic; `test/Functional/` hits the live WordPress.org SVN endpoints. Functional tests compare against snapshot files in `test/Functional/expected/<slug>/<version>/`.
