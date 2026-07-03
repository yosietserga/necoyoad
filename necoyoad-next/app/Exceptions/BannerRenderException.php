<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a banner fails to render (engine not found, slide data missing, etc.)
 */
class BannerRenderException extends StorefrontException
{
    public function __construct(string $bannerName, string $reason, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("Banner '{$bannerName}' failed to render: {$reason}", $code, $previous, 500);
    }
}
