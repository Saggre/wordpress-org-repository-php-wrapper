<?php

namespace Saggre\WordPress\Repository;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToListContents;
use Saggre\WordPress\Repository\Config\PluginClientConfig;
use Saggre\WordPress\Repository\Exception\LogReportException;
use Symfony\Component\Filesystem\Path;

class PluginClient
{
    public const CLIENT_VERSION = '1.0.0';

    protected XmlService $xmlService;

    public function __construct(
        protected PluginClientConfig $config,
    ) {
    }

    /**
     * Get the path for a given plugin file.
     *
     * @param string $path Relative file path from the plugin root.
     * @return string
     */
    protected function getPath(string $path): string
    {
        return Path::join($this->config->getSlug(), 'tags', $this->config->getVersion(), $path);
    }

    /**
     * Get the content of a plugin file.
     *
     * @param string $path Relative file path from the plugin root.
     * @return string File content.
     * @throws FilesystemException On repository read error.
     */
    public function getFile(string $path): string
    {
        $fullPath = $this->getPath($path);
        return $this->config
            ->getFilesystem()
            ->read($fullPath);
    }

    /**
     * Get the content of a plugin file as a stream.
     *
     * @param string $path Relative file path from the plugin root.
     * @return resource File content stream.
     * @throws FilesystemException On repository read error.
     */
    public function getFileStream(string $path)
    {
        $fullPath = $this->getPath($path);
        return $this->config
            ->getFilesystem()
            ->readStream($fullPath);
    }

    /**
     * Get the content of a plugin directory.
     *
     * @param string $path Relative file path from the plugin root.
     * @return DirectoryListing Directory listing of the plugin directory.
     * @throws UnableToListContents On repository read error or if the path is not a directory.
     * @throws FilesystemException On repository read error.
     */
    public function getDirectory(string $path): DirectoryListing
    {
        $filesystem = $this->config->getFilesystem();
        $fullPath = $this->getPath($path);

        return $filesystem->listContents($fullPath, false);
    }

    public function getLogReport(string $path, string $start = '10000', string $end = 'HEAD'): string
    {
        $client = $this->config->getClient();
        $path = htmlspecialchars($this->getPath($path), ENT_NOQUOTES, 'UTF-8');

        $headers = [
            'Content-Type' => 'application/xml',
        ];

        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>', "\n";
        echo "<S:log-report xmlns:S=\"svn:\">\n";
        echo "<S:start-revision>{$start}</S:start-revision>\n";
        echo "<S:end-revision>{$end}</S:end-revision>\n";
        echo "<S:path>/akismet/license.txt</S:path>\n";
        echo '</S:log-report>';
        $body = ob_get_clean();

        $response = $client->request('REPORT', '/', $body, $headers);

        /*
         * <?xml version="1.0" encoding="utf-8"?>
<D:error xmlns:D="DAV:" xmlns:m="http://apache.org/dav/xmlns" xmlns:C="svn:">
<C:error/>
<m:human-readable errcode="160013">
File not found: revision 1, path '/woocommerce/tags/9.6.2/readme.txt'
</m:human-readable>
</D:error>
         */

        if ($response['statusCode'] !== 200) {
            throw new LogReportException(
                "Failed to get log report for {$path}",
                $response['statusCode']
            );
        }

        return $response['body'];
    }
}
