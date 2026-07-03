<?php

declare(strict_types=1);

namespace App\Exceptions;

class StoreNotResolvedException extends StorefrontException
{
    protected int $statusCode = 503;

    public function __construct(string $message = 'Store could not be resolved', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous, 503);
    }
}
