<?php

namespace Saggre\WordPress\Repository\Test\Functional;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToListContents;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Model\LogEntry;
use Saggre\WordPress\Repository\Model\LogPath;
use Saggre\WordPress\Repository\Model\LogPathAction;
use Saggre\WordPress\Repository\PluginClient;
use Saggre\WordPress\Repository\Util\Path;

class PluginClientTest extends FunctionalTestCase
{
    public static function dataProviderTestGetFile(): iterable
    {
        return [
            [
                'woocommerce',
                '9.6.2',
                'includes/class-wc-privacy-exporters.php',
            ],
            [
                'wordpress-seo',
                '25.5',
                'wpml-config.xml',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetFile
     */
    public function testGetFile(string $slug, string $version, string $path)
    {
        $config = new PluginClientConfig($slug, $version);
        $client = new PluginClient($config);

        try {
            $file = $client->getFile($path);
        } catch (FilesystemException $e) {
            $this->fail("Failed to read file: {$e->getMessage()}");
        }

        $expected = $this->getExpectedFileContent($slug, $version, $path);
        $this->assertEquals($expected, $file);
    }

    /**
     * @dataProvider dataProviderTestGetFile
     */
    public function testGetFileStream(string $slug, string $version, string $path)
    {
        $config = new PluginClientConfig($slug, $version);
        $client = new PluginClient($config);

        try {
            $fileStream = $client->getFileStream($path);
        } catch (FilesystemException $e) {
            $this->fail("Failed to read file: {$e->getMessage()}");
        }

        $expected = $this->getExpectedFileContent($slug, $version, $path);
        $this->assertIsResource($fileStream);
        $this->assertEquals($expected, stream_get_contents($fileStream));
    }

    public static function dataProviderTestGetDirectory(): iterable
    {
        return [
            [
                'woocommerce',
                '9.6.2',
                '/i18n',
            ],
            [
                'wordpress-seo',
                '25.5',
                '/',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetDirectory
     */
    public function testGetDirectory(string $slug, string $version, string $path)
    {
        $config = new PluginClientConfig($slug, $version);
        $client = new PluginClient($config);

        try {
            $directory = $client->getDirectory($path);
            $directory = array_map(fn(StorageAttributes $item) => $item->jsonSerialize(), $directory->toArray());
        } catch (FilesystemException $e) {
            $this->fail("Failed to read directory: {$e->getMessage()}");
        }

        $expected = $this->getExpectedDirectoryListing($slug, $version, $path);
        $this->assertEqualsCanonicalizing($expected, $directory);
    }

    public function testGetTagsDirectoryReturnsVersionList(): void
    {
        $client = new PluginClient(new PluginClientConfig('akismet'));
        $listing = $client->getTagsDirectory();
        $versions = iterator_to_array($listing);

        $this->assertNotEmpty($versions);
        $this->assertContainsOnlyInstancesOf(DirectoryAttributes::class, $versions);

        foreach ($versions as $dir) {
            $this->assertNotNull($dir->lastModified(), "Tag {$dir->path()} missing lastModified");
        }
    }

    public function testGetDirectoryInvalidPath()
    {
        $config = new PluginClientConfig('woocommerce', '9.6.2');
        $client = new PluginClient($config);

        $this->expectException(UnableToListContents::class);
        $this->expectExceptionMessage(
            "Unable to list contents for 'woocommerce/tags/9.6.2/invalid/path', shallow listing\n\nReason: Not Found"
        );

        $client->getDirectory('/invalid/path')->toArray();
    }

    public function testGetDirectoryDeepListsSubdirectories()
    {
        $client = new PluginClient(new PluginClientConfig('classic-editor', '1.6.7'));

        $shallow = $client->getDirectory('')->toArray();
        $deep = $client->getDirectory('', true)->toArray();
        $paths = array_map(fn(StorageAttributes $item) => $item->path(), $deep);

        self::assertCount(5, $shallow);
        self::assertCount(8, $deep);
        self::assertContains('classic-editor/tags/1.6.7/scripts/post.js', $paths);
    }

    public function testExportWritesTheTree()
    {
        $client = new PluginClient(new PluginClientConfig('classic-editor', '1.6.7'));
        $destination = $this->getExportDestination();

        $files = $client->export($destination);
        $nested = (new Path(DIRECTORY_SEPARATOR))->join($destination, 'scripts', 'post.js');

        self::assertSame(6, $files);
        self::assertFileExists($nested);
        self::assertStringEqualsFile($nested, $client->getFile('scripts/post.js'));
        self::assertStringEqualsFile(
            (new Path(DIRECTORY_SEPARATOR))->join($destination, 'classic-editor.php'),
            $client->getFile('classic-editor.php')
        );
    }

    public function testGetLogReadsPluginHistory()
    {
        $client = new PluginClient(new PluginClientConfig('hello-dolly'));
        $log = $client->getLog(3);

        self::assertNotEmpty($log);
        self::assertLessThanOrEqual(3, count($log));
        self::assertContainsOnlyInstancesOf(LogEntry::class, $log);

        $revisions = array_column($log, 'revision');
        $sorted = $revisions;
        rsort($sorted);

        self::assertSame($sorted, $revisions, 'Revisions are not ordered newest first.');

        foreach ($log as $entry) {
            self::assertNotEmpty($entry->author);
            self::assertNotNull($entry->date);
            self::assertNotEmpty($entry->paths);
            self::assertContainsOnlyInstancesOf(LogPath::class, $entry->paths);

            foreach ($entry->paths as $path) {
                self::assertStringStartsWith('/hello-dolly/', $path->path);
            }
        }
    }

    public function testGetLogReadsARevisionRange()
    {
        $client = new PluginClient(new PluginClientConfig('hello-dolly'));
        $log = $client->getLog(1, 2995248, 2995248);

        self::assertCount(1, $log);
        self::assertSame(2995248, $log[0]->revision);
        self::assertSame('priethor', $log[0]->author);

        $tag = $log[0]->paths[0];

        self::assertSame('/hello-dolly/tags/1.7.3', $tag->path);
        self::assertSame(LogPathAction::Added, $tag->action);
        self::assertSame('dir', $tag->nodeKind);
        self::assertSame('/hello-dolly/trunk', $tag->copyFromPath);
        self::assertSame(2995208, $tag->copyFromRevision);
    }

    public function testGetRepositoryLogReadsEveryPlugin()
    {
        $client = new PluginClient(new PluginClientConfig('hello-dolly'));
        $log = $client->getRepositoryLog(2);

        self::assertCount(2, $log);
        self::assertGreaterThan($log[1]->revision, $log[0]->revision);

        foreach ($log as $entry) {
            self::assertNotEmpty($entry->paths);
        }
    }
}
