<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidFileTypeException extends FileOperationException
{
    public function __construct(string $extension, array $allowed = [])
    {
        $allowedStr = implode(', ', $allowed);
        parent::__construct("File type '.{$extension}' is not allowed. Allowed: {$allowedStr}");
        $this->statusCode = 415;
    }
}
