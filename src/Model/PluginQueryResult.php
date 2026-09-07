<?php

namespace Saggre\WordPress\Repository\Model;

/**
 * A single page of plugin query API results.
 */
class PluginQueryResult
{
    /**
     * @param PluginInfo[] $plugins The plugins on this page.
     * @param int $page The page number this result represents.
     * @param int $pages The total number of pages available.
     * @param int $results The total number of plugins matching the query.
     */
    public function __construct(
        public readonly array $plugins,
        public readonly int $page,
        public readonly int $pages,
        public readonly int $results,
    ) {
    }

    /**
     * Build a result page from a decoded API payload.
     *
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $info = $data['info'] ?? [];

        return new self(
            array_map(fn(array $plugin) => PluginInfo::fromArray($plugin), $data['plugins'] ?? []),
            (int) ($info['page'] ?? 1),
            (int) ($info['pages'] ?? 0),
            (int) ($info['results'] ?? 0),
        );
    }
}
