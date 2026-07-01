<?php

declare(strict_types=1);

namespace App\Filters;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

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

class FilterPipeline
{
    private array $filters = [];
    private array $actions = [];

    /**
     * Apply a filter — chain a value through registered callbacks.
     * Each callback receives the value and can transform it.
     */
    public function apply(string $name, mixed $value, ...$args): mixed
    {
        if (!isset($this->filters[$name])) {
            return $value;
        }

        foreach ($this->filters[$name] as $callback) {
            $value = call_user_func($callback, $value, ...$args);
        }

        return $value;
    }

    /**
     * Run an action — execute callbacks with short-circuit.
     * If any callback returns a truthy value, that value is returned
     * and the remaining callbacks are skipped.
     */
    public function run(string $name, ...$args): mixed
    {
        if (!isset($this->actions[$name])) {
            return null;
        }

        foreach ($this->actions[$name] as $callback) {
            $result = call_user_func($callback, ...$args);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    public function addFilter(string $name, callable $callback, int $priority = 10): void
    {
        $this->filters[$name][$priority][] = $callback;
        ksort($this->filters[$name]);
    }

    public function addAction(string $name, callable $callback, int $priority = 10): void
    {
        $this->actions[$name][$priority][] = $callback;
        ksort($this->actions[$name]);
    }

    public function hasFilter(string $name): bool
    {
        return isset($this->filters[$name]) || isset($this->actions[$name]);
    }

    public function removeFilter(string $name): void
    {
        unset($this->filters[$name], $this->actions[$name]);
    }
}

class FilterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('filter', FilterPipeline::class);
    }
}
