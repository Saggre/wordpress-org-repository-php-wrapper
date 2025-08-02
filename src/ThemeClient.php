<?php

namespace Saggre\WordPress\Repository;

use Saggre\WordPress\Repository\Config\ThemeClientConfig;
use Saggre\WordPress\Repository\Util\Path;

class ThemeClient extends BaseClient
{
    public function __construct(
        protected ThemeClientConfig $config,
    ) {
        parent::__construct();
    }

    /**
     * Get the path for a given theme file.
     *
     * @param string $path Relative file path from the theme root.
     * @return string
     */
    protected function getPath(string $path): string
    {
        return (new Path('/'))->join(
            $this->config->getSlug(),
            $this->config->getVersion(),
            $path
        );
    }
}
