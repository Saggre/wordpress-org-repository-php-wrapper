<?php

namespace Saggre\WordPress\Repository\Util;

use DOMDocument;
use DOMElement;
use DOMXPath;
use InvalidArgumentException;
use Saggre\WordPress\Repository\Exception\ClientException;
use Saggre\WordPress\Repository\Model\LogEntry;
use Saggre\WordPress\Repository\Model\LogPath;
use Saggre\WordPress\Repository\Model\LogPathAction;

/**
 * Encodes and decodes the SVN log-report protocol used by the REPORT method.
 */
class LogReport
{
    public const NAMESPACE_SVN = 'svn:';
    public const NAMESPACE_DAV = 'DAV:';

    /**
     * Paths of a log item, by the element name the server reports them under.
     */
    protected const PATH_ELEMENTS = [
        'added-path' => LogPathAction::Added,
        'modified-path' => LogPathAction::Modified,
        'deleted-path' => LogPathAction::Deleted,
        'replaced-path' => LogPathAction::Replaced,
    ];

    /**
     * Build the request body of a log-report.
     *
     * The end revision is always sent. Without it the server answers 200 with an empty report,
     * which reads as a plugin with no history rather than as the malformed request it is.
     *
     * @param int $limit Maximum number of revisions to return, newest first. 0 for no limit.
     * @param int|null $startRevision Revision to start from, defaults to the youngest revision.
     * @param int $endRevision Revision to stop at.
     * @param string $path Path relative to the report target, to restrict the revisions to.
     * @return string
     * @throws InvalidArgumentException On a revision range the server cannot answer.
     */
    public function createRequestBody(
        int $limit,
        ?int $startRevision = null,
        int $endRevision = 0,
        string $path = ''
    ): string {
        if ($endRevision < 0) {
            throw new InvalidArgumentException('The end revision cannot be negative.');
        }

        if ($startRevision !== null && $startRevision < $endRevision) {
            throw new InvalidArgumentException(sprintf(
                'The start revision %d is older than the end revision %d.',
                $startRevision,
                $endRevision
            ));
        }

        $lines = ['<?xml version="1.0" encoding="utf-8"?>'];
        $lines[] = '<S:log-report xmlns:S="' . self::NAMESPACE_SVN . '" xmlns:D="' . self::NAMESPACE_DAV . '">';

        if ($startRevision !== null) {
            $lines[] = '<S:start-revision>' . $startRevision . '</S:start-revision>';
        }

        $lines[] = '<S:end-revision>' . $endRevision . '</S:end-revision>';
        $lines[] = '<S:limit>' . $limit . '</S:limit>';
        $lines[] = '<S:discover-changed-paths/>';
        $lines[] = '<S:revprop>svn:author</S:revprop>';
        $lines[] = '<S:revprop>svn:date</S:revprop>';
        $lines[] = '<S:revprop>svn:log</S:revprop>';
        $lines[] = '<S:path>' . htmlspecialchars($path, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</S:path>';
        $lines[] = '</S:log-report>';

        return implode("\n", $lines);
    }

    /**
     * Parse the response body of a log-report into log entries, newest revision first.
     *
     * @param string $body
     * @return LogEntry[]
     * @throws ClientException On an unparseable response body.
     */
    public function parseResponse(string $body): array
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($body);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            throw new ClientException('Unable to parse the SVN log report response.');
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('S', self::NAMESPACE_SVN);
        $xpath->registerNamespace('D', self::NAMESPACE_DAV);

        $entries = [];

        foreach ($xpath->query('//S:log-item') as $item) {
            if (!$item instanceof DOMElement) {
                continue;
            }

            $entries[] = new LogEntry(
                (int) $this->getValue($xpath, $item, 'D:version-name'),
                $this->getValue($xpath, $item, 'D:creator-displayname'),
                Date::parse($this->getValue($xpath, $item, 'S:date')),
                $this->getValue($xpath, $item, 'D:comment'),
                $this->getPaths($item),
            );
        }

        return $entries;
    }

    /**
     * Get the changed paths of a single log item, in the order the server reports them.
     *
     * @param DOMElement $item
     * @return LogPath[]
     */
    protected function getPaths(DOMElement $item): array
    {
        $paths = [];

        foreach ($item->childNodes as $path) {
            if (!$path instanceof DOMElement || !isset(self::PATH_ELEMENTS[$path->localName])) {
                continue;
            }

            $copyFromRevision = $path->getAttribute('copyfrom-rev');

            $paths[] = new LogPath(
                $path->textContent,
                self::PATH_ELEMENTS[$path->localName],
                $path->getAttribute('node-kind') ?: null,
                $path->getAttribute('text-mods') === 'true',
                $path->getAttribute('prop-mods') === 'true',
                $path->getAttribute('copyfrom-path') ?: null,
                $copyFromRevision === '' ? null : (int) $copyFromRevision,
            );
        }

        return $paths;
    }

    /**
     * Get the text content of a single child element of a log item.
     *
     * @param DOMXPath $xpath
     * @param DOMElement $item
     * @param string $expression
     * @return string|null
     */
    protected function getValue(DOMXPath $xpath, DOMElement $item, string $expression): ?string
    {
        $node = $xpath->query($expression, $item)->item(0);

        return $node === null ? null : $node->textContent;
    }
}
