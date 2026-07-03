<?php

declare(strict_types=1);

namespace App\Exceptions;

class FileNotFoundException extends FileOperationException
{
    public function __construct(string $path)
    {
        parent::__construct("File not found: {$path}", 0, null);
        $this->statusCode = 404;
    }
}
