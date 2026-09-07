<?php

namespace Saggre\WordPress\Repository\Test\Functional;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Saggre\WordPress\Repository\Util\Path;

class FunctionalTestCase extends TestCase
{
    protected ?string $exportDestination = null;

    protected function tearDown(): void
    {
        if ($this->exportDestination !== null) {
            $this->removeDirectory($this->exportDestination);
            $this->exportDestination = null;
        }
    }

    /**
     * Get a temporary directory to export into. Removed after the test.
     *
     * @return string
     */
    protected function getExportDestination(): string
    {
        $this->exportDestination = (new Path(DIRECTORY_SEPARATOR))->join(
            sys_get_temp_dir(),
            'wp-org-repository-export-' . uniqid()
        );

        return $this->exportDestination;
    }

    /**
     * Remove a directory and everything in it.
     *
     * @param string $path
     * @return void
     */
    protected function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $contents = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($contents as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }

    /**
     * Get the expected file path for a given plugin file.
     *
     * @param string $slug
     * @param string $version
     * @param string $path
     * @return string
     */
    protected function getExpectedContentPath(string $slug, string $version, string $path): string
    {
        return (new Path('/'))->join(
            __DIR__,
            'expected',
            $slug,
            $version,
            $path
        );
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
        $expectedPath = $this->getExpectedContentPath($slug, $version, $path);

        if (!file_exists($expectedPath)) {
            $this->fail("File {$expectedPath} does not exist.");
        }

        return file_get_contents($expectedPath);
    }

    /**
     * Get the expected directory listing for a given plugin directory.
     *
     * @param string $slug
     * @param string $version
     * @param string $path
     * @return array
     */
    protected function getExpectedDirectoryListing(string $slug, string $version, string $path): array
    {
        $expectedPath = $this->getExpectedContentPath($slug, $version, $path);

        if (!is_dir($expectedPath)) {
            $this->fail("Directory {$expectedPath} does not exist.");
        }

        if (!file_exists("$expectedPath/index.php")) {
            $this->fail("Expected index.php file does not exist in {$expectedPath}.");
        }

        return include "$expectedPath/index.php";
    }
}
