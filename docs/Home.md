
This is an automatically generated documentation for **Wordpress.org Repository API wrapper**.

## Namespaces

### \Saggre\WordPress\Repository

#### Classes

| Class                                                                                | Description                                            |
|--------------------------------------------------------------------------------------|--------------------------------------------------------|
| [`BaseClient`](./classes/Saggre/WordPress/Repository/BaseClient)                     | Base class for WordPress.org plugin and theme clients. |
| [`PluginApiClient`](./classes/Saggre/WordPress/Repository/PluginApiClient)           | WordPress.org plugin API client.                       |
| [`PluginClient`](./classes/Saggre/WordPress/Repository/PluginClient)                 | WordPress.org plugin client.                           |
| [`PluginDownloadClient`](./classes/Saggre/WordPress/Repository/PluginDownloadClient) | WordPress.org plugin distribution client.              |
| [`ThemeClient`](./classes/Saggre/WordPress/Repository/ThemeClient)                   | WordPress.org theme client.                            |

### \Saggre\WordPress\Repository\Config

#### Classes

| Class                                                                                                   | Description                                                            |
|---------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------|
| [`BaseClientConfig`](./classes/Saggre/WordPress/Repository/Config/BaseClientConfig)                     | Base configuration class for WordPress.org clients.                    |
| [`PluginApiClientConfig`](./classes/Saggre/WordPress/Repository/Config/PluginApiClientConfig)           | Configuration class for the WordPress.org Plugin API Client.           |
| [`PluginClientConfig`](./classes/Saggre/WordPress/Repository/Config/PluginClientConfig)                 | Configuration class for the WordPress.org Plugin Client.               |
| [`PluginDownloadClientConfig`](./classes/Saggre/WordPress/Repository/Config/PluginDownloadClientConfig) | Configuration class for the WordPress.org Plugin Download Client.      |
| [`RepositoryClientConfig`](./classes/Saggre/WordPress/Repository/Config/RepositoryClientConfig)         | Base configuration class for the WordPress.org SVN repository clients. |
| [`ThemeClientConfig`](./classes/Saggre/WordPress/Repository/Config/ThemeClientConfig)                   | Configuration class for the WordPress.org Theme Client.                |

### \Saggre\WordPress\Repository\Exception

#### Classes

| Class                                                                                          | Description                                                  |
|------------------------------------------------------------------------------------------------|--------------------------------------------------------------|
| [`ClientException`](./classes/Saggre/WordPress/Repository/Exception/ClientException)           | Thrown when a WordPress.org endpoint responds with an error. |
| [`TagNotFoundException`](./classes/Saggre/WordPress/Repository/Exception/TagNotFoundException) | Thrown when a version has no tag in the repository.          |

### \Saggre\WordPress\Repository\Model

#### Classes

| Class                                                                                | Description                                            |
|--------------------------------------------------------------------------------------|--------------------------------------------------------|
| [`Contributor`](./classes/Saggre/WordPress/Repository/Model/Contributor)             | A plugin contributor as returned by the plugin API.    |
| [`LogEntry`](./classes/Saggre/WordPress/Repository/Model/LogEntry)                   | A single revision of the WordPress.org SVN repository. |
| [`LogPath`](./classes/Saggre/WordPress/Repository/Model/LogPath)                     | A single path changed by an SVN revision.              |
| [`PluginInfo`](./classes/Saggre/WordPress/Repository/Model/PluginInfo)               | A plugin record as returned by the plugin API.         |
| [`PluginQuery`](./classes/Saggre/WordPress/Repository/Model/PluginQuery)             | Parameters for a plugin query API request.             |
| [`PluginQueryResult`](./classes/Saggre/WordPress/Repository/Model/PluginQueryResult) | A single page of plugin query API results.             |
| [`PluginStatus`](./classes/Saggre/WordPress/Repository/Model/PluginStatus)           | Availability of a plugin in the plugin directory.      |

### \Saggre\WordPress\Repository\Util

#### Classes

| Class                                                               | Description                                                                |
|---------------------------------------------------------------------|----------------------------------------------------------------------------|
| [`Date`](./classes/Saggre/WordPress/Repository/Util/Date)           | Utility class for parsing the date formats used by WordPress.org.          |
| [`LogReport`](./classes/Saggre/WordPress/Repository/Util/LogReport) | Encodes and decodes the SVN log-report protocol used by the REPORT method. |
| [`Path`](./classes/Saggre/WordPress/Repository/Util/Path)           | Utility class for handling file paths.                                     |
