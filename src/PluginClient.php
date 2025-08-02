<?php

namespace Saggre\WordPress\Repository;

use Saggre\WordPress\Repository\Config\PluginClientConfig;

class PluginClient extends BaseClient
{
    public function __construct(
        protected PluginClientConfig $config,
    ) {
        parent::__construct();
    }
}
