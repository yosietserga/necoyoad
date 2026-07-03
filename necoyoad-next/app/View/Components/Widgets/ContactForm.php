<?php

declare(strict_types=1);

namespace App\View\Components\Widgets;

use App\View\Components\WidgetComponent;

/**
 * ContactForm widget — displays a contact form.
 * Uses Livewire for form submission (handled by Livewire\Storefront\ContactForm component).
 */
class ContactForm extends WidgetComponent
{
    public function data(): array
    {
        return [
            'heading' => $this->settings['title'] ?? '',
            'email' => $this->settings['email'] ?? config('mail.from.address'),
        ];
    }
}
