<?php

declare(strict_types=1);

namespace App\Exceptions;

class ImageProcessingException extends FileOperationException
{
    public function __construct(string $operation, string $reason)
    {
        parent::__construct("Image '{$operation}' failed: {$reason}");
    }
}
