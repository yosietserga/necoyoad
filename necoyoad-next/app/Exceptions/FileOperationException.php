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

class FileNotFoundException extends FileOperationException
{
    public function __construct(string $path)
    {
        parent::__construct("File not found: {$path}", 0, null);
        $this->statusCode = 404;
    }
}

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

class InvalidFileTypeException extends FileOperationException
{
    public function __construct(string $extension, array $allowed = [])
    {
        $allowedStr = implode(', ', $allowed);
        parent::__construct("File type '.{$extension}' is not allowed. Allowed: {$allowedStr}");
        $this->statusCode = 415;
    }
}

class UnsafeFileException extends FileOperationException
{
    public function __construct(string $reason)
    {
        parent::__construct("Unsafe file rejected: {$reason}");
        $this->statusCode = 422;
    }
}

class ImageProcessingException extends FileOperationException
{
    public function __construct(string $operation, string $reason)
    {
        parent::__construct("Image '{$operation}' failed: {$reason}");
    }
}

class ThemeFileNotFoundException extends FileOperationException
{
    public function __construct(string $theme, string $path)
    {
        parent::__construct("Theme file not found: {$theme}/{$path}");
        $this->statusCode = 404;
    }
}
