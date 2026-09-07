<?php

namespace Saggre\WordPress\Repository\Model;

use DateTimeImmutable;

/**
 * A single revision of the WordPress.org SVN repository.
 */
class LogEntry
{
    /**
     * @param int $revision The revision number.
     * @param string|null $author The committer username.
     * @param DateTimeImmutable|null $date The commit timestamp.
     * @param string|null $message The commit message.
     * @param LogPath[] $paths The paths changed by this revision.
     */
    public function __construct(
        public readonly int $revision,
        public readonly ?string $author = null,
        public readonly ?DateTimeImmutable $date = null,
        public readonly ?string $message = null,
        public readonly array $paths = [],
    ) {
    }
}
