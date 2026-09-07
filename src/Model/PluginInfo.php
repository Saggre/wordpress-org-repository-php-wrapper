<?php

namespace Saggre\WordPress\Repository\Model;

use DateTimeImmutable;
use Saggre\WordPress\Repository\Util\Date;

/**
 * A plugin record as returned by the plugin API.
 *
 * Fields the API omits, either because the plugin has none or because they were switched off through
 * PluginQuery::$fields, are null or empty. The complete decoded payload is available in $raw.
 */
class PluginInfo
{
    /**
     * @param array<string,Contributor> $contributors Keyed by WordPress.org username.
     * @param array<string,string> $tags Tag slug to tag label.
     * @param array<string,string> $versions Version number to download URL.
     * @param array<string,string> $sections Section name to HTML content.
     * @param array<string,mixed> $raw The complete decoded API payload.
     */
    public function __construct(
        public readonly string $slug,
        public readonly ?string $name = null,
        public readonly ?string $version = null,
        public readonly ?string $author = null,
        public readonly ?string $authorProfile = null,
        public readonly ?string $homepage = null,
        public readonly ?string $donateLink = null,
        public readonly ?string $shortDescription = null,
        public readonly ?string $downloadLink = null,
        public readonly ?string $requires = null,
        public readonly ?string $requiresPhp = null,
        public readonly ?string $tested = null,
        public readonly ?DateTimeImmutable $lastUpdated = null,
        public readonly ?DateTimeImmutable $added = null,
        public readonly ?int $activeInstalls = null,
        public readonly ?int $downloaded = null,
        public readonly ?float $rating = null,
        public readonly ?int $numRatings = null,
        public readonly array $contributors = [],
        public readonly array $tags = [],
        public readonly array $versions = [],
        public readonly array $sections = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * Build a plugin record from a decoded API payload.
     *
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $contributors = [];

        foreach (self::toArray($data, 'contributors') as $username => $contributor) {
            $contributors[$username] = Contributor::fromArray((string) $username, (array) $contributor);
        }

        return new self(
            (string) $data['slug'],
            self::toString($data, 'name'),
            self::toString($data, 'version'),
            self::toString($data, 'author'),
            self::toString($data, 'author_profile'),
            self::toString($data, 'homepage'),
            self::toString($data, 'donate_link'),
            self::toString($data, 'short_description'),
            self::toString($data, 'download_link'),
            self::toString($data, 'requires'),
            self::toString($data, 'requires_php'),
            self::toString($data, 'tested'),
            Date::parse(self::toString($data, 'last_updated')),
            Date::parse(self::toString($data, 'added')),
            isset($data['active_installs']) ? (int) $data['active_installs'] : null,
            isset($data['downloaded']) ? (int) $data['downloaded'] : null,
            isset($data['rating']) ? (float) $data['rating'] : null,
            isset($data['num_ratings']) ? (int) $data['num_ratings'] : null,
            $contributors,
            self::toArray($data, 'tags'),
            self::toArray($data, 'versions'),
            self::toArray($data, 'sections'),
            $data,
        );
    }

    /**
     * Read a field the API returns either as a string or as false when it is not set.
     *
     * @param array<string,mixed> $data
     * @param string $key
     * @return string|null
     */
    protected static function toString(array $data, string $key): ?string
    {
        return empty($data[$key]) ? null : (string) $data[$key];
    }

    /**
     * Read a field the API returns either as a map or as false when it is empty.
     *
     * @param array<string,mixed> $data
     * @param string $key
     * @return array<mixed>
     */
    protected static function toArray(array $data, string $key): array
    {
        return is_array($data[$key] ?? null) ? $data[$key] : [];
    }
}
