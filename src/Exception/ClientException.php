<?php

namespace Saggre\WordPress\Repository\Exception;

use RuntimeException;

/**
 * Thrown when a WordPress.org endpoint responds with an error.
 *
 * The exception code holds the HTTP status code of the response.
 */
class ClientException extends RuntimeException
{
}
