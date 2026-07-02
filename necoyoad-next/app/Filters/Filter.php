<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Support\Facades\Facade;

/**
 * Filter — the Hooks system (WordPress-style filters + actions with short-circuit).
 *
 * This is the Hooks system from the original Necoyoad (v2, v3), reimplemented
 * using Laravel's Pipeline pattern.
 *
 * - Filter::apply('render', $html) chains $html through registered filters.
 * - Filter::run('loadWidgets', $params) can short-circuit by returning a value.
 *
 * Events (observer pattern, no short-circuit) use Laravel's native Event system.
 *
 * @see v2 (dual Hooks/Events extension system)
 * @see v3 (Hooks emit points in the rendering pipeline)
 * @see v11 (widget engine preservation — Abstraction 4)
 */

/**
 * @method static mixed apply(string $name, mixed $value, ...$args)
 * @method static mixed run(string $name, ...$args)
 * @method static void addFilter(string $name, callable $callback, int $priority = 10)
 * @method static void addAction(string $name, callable $callback, int $priority = 10)
 * @method static bool hasFilter(string $name)
 * @method static void removeFilter(string $name)
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filter';
    }
}
