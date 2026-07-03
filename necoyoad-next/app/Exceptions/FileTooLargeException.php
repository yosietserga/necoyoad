<?php

declare(strict_types=1);

namespace App\Exceptions;

class FileTooLargeException extends FileOperationException
{
    public function __construct(int $size, int $maxSize)
    {
        $sizeMB = round($size / 1048576, 2);
        $maxMB = round($maxSize / 1048576, 2);
        parent::__construct("File size {$sizeMB}MB exceeds maximum {$maxMB}MB");
        $this->statusCode = 413;
    }
}
