<?php

namespace Saggre\WordPress\Repository\Model;

/**
 * Browse modes supported by the plugin query API.
 */
enum PluginBrowse: string
{
    case Featured = 'featured';
    case Popular = 'popular';
    case New = 'new';
    case Updated = 'updated';
}
