<?php

namespace Saggre\WordPress\Repository\Exception;

/**
 * Thrown when a version has no tag in the repository.
 *
 * A plugin can publish a version without ever tagging it, which leaves its real predecessor
 * several versions back. Comparing against the wrong pair silently reads as a clean release,
 * so the missing tag is reported instead.
 */
class TagNotFoundException extends ClientException
{
}
