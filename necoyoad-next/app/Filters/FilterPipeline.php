<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * FilterPipeline — the concrete implementation of the Hooks system.
 *
 * Registered as a singleton ('filter' alias) by FilterServiceProvider.
 * The Filter facade delegates to this class.
 */
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
