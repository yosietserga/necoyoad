<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Base exception for all file/image operations.
 */
class FileOperationException extends StorefrontException
{
    public function __construct(string $message = 'File operation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 500);
    }
}
