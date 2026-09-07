<?php

namespace Saggre\WordPress\Repository\Util;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Utility class for parsing the date formats used by WordPress.org.
 */
class Date
{
    /**
     * Parse an API date string, e.g. '2025-10-24 4:13am GMT' or '2008-07-06'.
     *
     * WordPress.org reports times in UTC. Values that carry no zone of their own are read as UTC
     * rather than as the host timezone, so the parsed instant does not depend on the environment.
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
            return new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (Exception) {
            return null;
        }
    }
}
