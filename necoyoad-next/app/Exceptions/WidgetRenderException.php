<?php

declare(strict_types=1);

namespace App\Exceptions;

class WidgetRenderException extends StorefrontException
{
    protected int $statusCode = 500;

    public function __construct(string $widgetName, string $reason, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("Widget '{$widgetName}' failed to render: {$reason}", $code, $previous, 500);
    }
}
