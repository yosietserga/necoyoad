<?php

declare(strict_types=1);

namespace App\Exceptions;

class ThemeFileNotFoundException extends FileOperationException
{
    public function __construct(string $theme, string $path)
    {
        parent::__construct("Theme file not found: {$theme}/{$path}");
        $this->statusCode = 404;
    }
}
