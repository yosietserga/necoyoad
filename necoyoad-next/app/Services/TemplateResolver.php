<?php

declare(strict_types=1);

namespace App\Services;

/**
 * TemplateResolver — resolves the template for a given entity.
 *
 * 3-level resolution (same as original Necoyoad, v8):
 *   1. Per-entity template override (EAV property('style', 'view') or JSON column)
 *   2. Config default (config("defaults.{$type}"))
 *   3. Hardcoded fallback
 *
 * Also checks the active theme folder first, then falls back to 'choroni'.
 *
 * @see v8 (per-entity template override)
 */
class TemplateResolver
{
    public function __construct(
        private readonly StoreContext $storeContext
    ) {}

    public function resolve(?string $entityTemplate, string $type, string $fallback): string
    {
        // 1. Per-entity override
        $template = $entityTemplate;

        // 2. Config default (key is 'necoyoad.defaults.{$type}', not 'defaults.{$type}')
        if (!$template) {
            $template = config("necoyoad.defaults.{$type}");
        }

        // 3. Hardcoded fallback
        if (!$template) {
            $template = $fallback;
        }

        // Check active theme
        $theme = $this->storeContext->setting('config_template', 'choroni');
        if (view()->exists("themes.{$theme}.{$template}")) {
            return "themes.{$theme}.{$template}";
        }

        // Fall back to choroni
        if (view()->exists("themes.choroni.{$template}")) {
            return "themes.choroni.{$template}";
        }

        // Final fallback
        return $fallback;
    }
}
