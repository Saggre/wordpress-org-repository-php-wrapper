<?php

namespace Saggre\WordPress\Repository\Model;

use DateTimeImmutable;
use Saggre\WordPress\Repository\Util\Date;

/**
 * Availability of a plugin in the plugin directory.
 *
 * A closed plugin keeps its record in the API, but is no longer downloadable.
 */
class PluginStatus
{
    public function __construct(
        public readonly string $slug,
        public readonly bool $closed,
        public readonly ?string $name = null,
        public readonly ?DateTimeImmutable $closedDate = null,
        public readonly ?string $reason = null,
        public readonly ?string $reasonText = null,
    ) {
    }

    /**
     * Build a status from a decoded plugins/info/1.0 payload.
     *
     * @param string $slug
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromArray(string $slug, array $data): self
    {
        return new self(
            $slug,
            !empty($data['closed']),
            empty($data['name']) ? null : (string) $data['name'],
            Date::parse(empty($data['closed_date']) ? null : (string) $data['closed_date']),
            empty($data['reason']) ? null : (string) $data['reason'],
            empty($data['reason_text']) ? null : (string) $data['reason_text'],
        );
    }
}
