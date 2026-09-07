<?php

namespace Saggre\WordPress\Repository\Model;

/**
 * A plugin contributor as returned by the plugin API.
 */
class Contributor
{
    public function __construct(
        public readonly string $username,
        public readonly ?string $displayName = null,
        public readonly ?string $profile = null,
        public readonly ?string $avatar = null,
    ) {
    }

    /**
     * Build a contributor from a single entry of the API contributors map.
     *
     * @param string $username Map key, the WordPress.org username.
     * @param array<string,mixed> $data
     * @return self
     */
    public static function fromArray(string $username, array $data): self
    {
        return new self(
            $username,
            $data['display_name'] ?? null,
            $data['profile'] ?? null,
            $data['avatar'] ?? null,
        );
    }
}
