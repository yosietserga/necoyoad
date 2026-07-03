<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\View\Components\WidgetComponent;

/**
 * Search widget — displays a search bar.
 * Submits to the search route.
 */
class Search extends WidgetComponent
{
    public function data(): array
    {
        return [
            'placeholder' => $this->settings['placeholder'] ?? 'Search...',
            'action' => route('search'),
        ];
    }
}
