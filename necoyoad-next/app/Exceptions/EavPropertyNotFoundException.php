<?php

declare(strict_types=1);

namespace App\Exceptions;

class EavPropertyNotFoundException extends StorefrontException
{
    public function __construct(string $group, string $key, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("EAV property '{$group}.{$key}' not found", $code, $previous, 404);
    }
}
