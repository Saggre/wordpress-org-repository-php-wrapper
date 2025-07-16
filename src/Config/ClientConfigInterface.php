<?php

namespace Saggre\WordPress\Repository\Config;

use League\Flysystem\Filesystem;

interface ClientConfigInterface
{
    public function getFilesystem(): Filesystem;
}
