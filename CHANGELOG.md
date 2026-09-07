# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `PluginApiClient` for the WordPress.org plugin API: `queryPlugins()` enumerates the directory with browse modes,
  paging and response field toggles, `getPluginInformation()` reads a single plugin record including its versions map,
  and `getPluginStatus()` reports whether a plugin has been closed.
- `PluginDownloadClient` for downloading plugin releases from the distribution host, as a string or as a stream.
- `BaseClient::getLog()` and `BaseClient::getRepositoryLog()`, which read commit logs over the SVN `REPORT` method.
- `BaseClient::export()`, which writes the tree of the configured version to a local directory.
- A `$deep` argument on `BaseClient::getDirectory()` for listing subdirectories.

### Changed

- `BaseClientConfig` now only holds the base URL and the user agent. The slug and version moved to the new
  `RepositoryClientConfig`, which `PluginClientConfig` and `ThemeClientConfig` extend.
- The `$path` argument of `BaseClient::getDirectory()` defaults to the plugin or theme root.
- The minimum PHP version is now 8.1.
- `composer run create-docs` now passes the markdown template and the title the committed `docs/` are generated with.

### Fixed

- `Util\Path` no longer drops path segments equal to `"0"`.
