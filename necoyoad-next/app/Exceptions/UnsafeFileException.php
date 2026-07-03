<?php

declare(strict_types=1);

namespace App\Exceptions;

class UnsafeFileException extends FileOperationException
{
    public function __construct(string $reason)
    {
        parent::__construct("Unsafe file rejected: {$reason}");
        $this->statusCode = 422;
    }
}
