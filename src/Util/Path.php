<?php

namespace Saggre\WordPress\Repository\Util;

class Path
{
    public function __construct(
        protected string $separator = DIRECTORY_SEPARATOR,
    ) {
    }

    /**
     * Normalizes a path string to use the specified directory separator.
     *
     * @param string $path
     * @return string
     */
    public function normalize(string $path): string
    {
        return str_replace(['/', '\\'], $this->separator, $path);
    }

    /**
     * Explodes a path string into an array of parts.
     *
     * @param string $path
     * @return array
     */
    public function explode(string $path): array
    {
        $path = self::normalize($path);
        $parts = explode($this->separator, $path);

        return array_values(array_filter($parts));
    }

    /**
     * Joins two or more path strings into a canonical path.
     */
    public function join(string ...$paths): string
    {
        $parts = [];

        foreach ($paths as $path) {
            $parts = array_merge($parts, self::explode($path));
        }

        $result = implode($this->separator, $parts);

        if (count($paths) > 0 && $this->startsWithSeparator($paths[0])) {
            $result = $this->separator . ltrim($result, $this->separator);
        }

        return $result;
    }

    /**
     * If the path starts with a directory separator.
     *
     * @param string $path
     * @return bool
     */
    protected function startsWithSeparator(string $path): bool
    {
        return str_starts_with(self::normalize($path), $this->separator);
    }
}
