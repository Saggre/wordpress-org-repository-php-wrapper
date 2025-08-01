<?php

namespace Saggre\WordPress\Repository\Exception;

use Exception;

class LogReportException extends Exception
{
    /**
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param Exception|null $previous The previous exception for chaining.
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
