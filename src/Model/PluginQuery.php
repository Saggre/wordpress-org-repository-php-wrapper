<?php

namespace Saggre\WordPress\Repository\Model;

/**
 * Parameters for a plugin query API request.
 */
class PluginQuery
{
    /**
     * @param PluginBrowse|null $browse Browse mode, e.g. PluginBrowse::Updated for the most recently updated plugins.
     * @param string|null $search Free text search term.
     * @param string|null $tag Plugin tag to filter by.
     * @param string|null $author WordPress.org username to filter by.
     * @param int $page Page number, starting at 1.
     * @param int $perPage Results per page. The API caps this at 250.
     * @param array<string,bool> $fields Response field toggles, e.g. ['sections' => false, 'contributors' => true].
     */
    public function __construct(
        public readonly ?PluginBrowse $browse = null,
        public readonly ?string $search = null,
        public readonly ?string $tag = null,
        public readonly ?string $author = null,
        public readonly int $page = 1,
        public readonly int $perPage = 250,
        public readonly array $fields = [],
    ) {
    }

    /**
     * Build the request parameters sent as request[...] in the query string.
     *
     * @return array<string,mixed>
     */
    public function toRequestParameters(): array
    {
        $parameters = [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];

        if ($this->browse !== null) {
            $parameters['browse'] = $this->browse->value;
        }

        foreach (['search' => $this->search, 'tag' => $this->tag, 'author' => $this->author] as $key => $value) {
            if ($value !== null) {
                $parameters[$key] = $value;
            }
        }

        if (!empty($this->fields)) {
            $parameters['fields'] = array_map(fn(bool $enabled) => $enabled ? '1' : '0', $this->fields);
        }

        return $parameters;
    }
}
