<?php

declare(strict_types=1);

namespace App\Exceptions;

class ProductNotFoundException extends StorefrontException
{
    protected int $statusCode = 404;

    public function __construct(string $message = 'Product not found', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 404);
    }
}
