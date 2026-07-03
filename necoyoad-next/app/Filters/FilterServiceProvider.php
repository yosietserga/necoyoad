<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Support\ServiceProvider;

/**
 * FilterServiceProvider — registers the FilterPipeline as a singleton.
 *
 * Bound to the 'filter' alias so the Filter facade can resolve it.
 */
class FilterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('filter', FilterPipeline::class);
    }
}
