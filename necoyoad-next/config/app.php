<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'New Necoyoad'),
    'env' => env('APP_ENV', 'local'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            array_map(strval(...), array_slice(explode(',', env('APP_PREVIOUS_KEYS', '')), 0, 5)),
        ),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'providers' => array_filter(explode(',', env('APP_EXTRA_PROVIDERS', ''))),
    'aliases' => Facade::defaultAliases()->merge([
        'Filter' => \App\Filters\Filter::class,
    ])->toArray(),

    // Audit logging configuration
    'audit_all_queries' => env('AUDIT_ALL_QUERIES', false),
];
