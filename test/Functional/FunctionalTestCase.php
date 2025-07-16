<?php

namespace Saggre\WordPress\Repository\Test\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

class FunctionalTestCase extends TestCase
{
    /**
     * Get the expected file path for a given plugin file.
     *
     * @param string $slug
     * @param string $version
     * @param string $path
     * @return string
     */
    protected function getExpectedFilePath(string $slug, string $version, string $path): string
    {
        return Path::join(__DIR__, 'expected', $slug, $version, $path);
    }

    /**
     * Get the expected file content for a given plugin file.
     *
     * @param string $slug
     * @param string $version
     * @param string $path
     * @return string
     */
    protected function getExpectedFileContent(string $slug, string $version, string $path): string
    {
        $expectedPath = $this->getExpectedFilePath($slug, $version, $path);

        if (!file_exists($expectedPath)) {
            $this->fail("File {$expectedPath} does not exist.");
        }

        return file_get_contents($expectedPath);
    }
}
