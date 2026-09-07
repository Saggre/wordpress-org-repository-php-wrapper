<?php

namespace Saggre\WordPress\Repository\Util;

use DateTimeImmutable;
use Exception;

/**
 * Utility class for parsing the date formats used by WordPress.org.
 */
class Date
{
    /**
     * Parse an API date string, e.g. '2025-10-24 4:13am GMT' or '2008-07-06'.
     *
     * @param string|null $value
     * @return DateTimeImmutable|null Null when the value is empty or unparseable.
     */
    public static function parse(?string $value): ?DateTimeImmutable
    {
        if (empty($value)) {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }
    }
}
