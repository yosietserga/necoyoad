<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\View\Components\WidgetComponent;

/**
 * RichText widget — displays rich text / HTML content.
 * The simplest widget: just renders the text from settings.
 */
class RichText extends WidgetComponent
{
    public function data(): array
    {
        return [
            'content' => $this->settings['content'] ?? '',
            'heading' => $this->settings['title'] ?? '',
        ];
    }
}
