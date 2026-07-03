<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Base storefront exception — all domain-specific storefront errors
 * extend this. Rendered as a user-friendly error page (not a generic 500).
 */
class StorefrontException extends Exception
{
    protected int $statusCode = 400;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, int $statusCode = 400)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
