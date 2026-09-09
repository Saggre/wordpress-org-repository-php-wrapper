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
- `BaseClient::getChangedPaths()`, which reads a revision range and can scope it to a subtree of the plugin or theme.
- `PluginClient::getTagRevisions()`, which maps every published version to the revision that created its tag, ordered
  by revision rather than by version string.
- `PluginClient::diffVersions()`, which lists the files that changed between two published versions in a single request
  instead of downloading and comparing two complete trees.
- `TagNotFoundException`, thrown when a version was published without ever being tagged.
- `textMods` and `propMods` on `LogPath`, which separate content changes from property only ones.
- `BaseClient::export()`, which writes the tree of the configured version to a local directory.
- A `$deep` argument on `BaseClient::getDirectory()` for listing subdirectories.

### Changed

- `BaseClientConfig` now only holds the base URL and the user agent. The slug and version moved to the new
  `RepositoryClientConfig`, which `PluginClientConfig` and `ThemeClientConfig` extend.
- The `$path` argument of `BaseClient::getDirectory()` defaults to the plugin or theme root.
- Repository requests now negotiate gzip, which is worth more than an order of magnitude on commit logs.
- `Util\LogReport::createRequestBody()` takes a path to scope the revisions to, and rejects a negative or inverted
  revision range before the request is sent.
- The minimum PHP version is now 8.1.
- `composer run create-docs` now passes the markdown template and the title the committed `docs/` are generated with.

### Fixed

- `Util\Path` no longer drops path segments equal to `"0"`.
