<?php

namespace Saggre\WordPress\Repository\Model;

/**
 * A single path changed by an SVN revision.
 */
class LogPath
{
    /**
     * @param string $path Repository absolute path, e.g. '/hello-dolly/tags/1.7.2/readme.txt'.
     * @param LogPathAction $action How the path changed in this revision.
     * @param string|null $nodeKind 'file' or 'dir'.
     * @param bool $textMods Whether the content changed. False means only properties did.
     * @param bool $propMods Whether the properties changed.
     * @param string|null $copyFromPath Source path when the node was copied, e.g. the trunk a tag was cut from.
     * @param int|null $copyFromRevision Source revision when the node was copied.
     */
    public function __construct(
        public readonly string $path,
        public readonly LogPathAction $action,
        public readonly ?string $nodeKind = null,
        public readonly bool $textMods = false,
        public readonly bool $propMods = false,
        public readonly ?string $copyFromPath = null,
        public readonly ?int $copyFromRevision = null,
    ) {
    }
}
