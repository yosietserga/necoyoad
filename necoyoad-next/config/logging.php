<?php
return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'deprecations' => ['channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'), 'trace' => false],
    'channels' => [
        'stack' => ['driver' => 'stack', 'channels' => ['single']],
        'single' => ['driver' => 'single', 'path' => storage_path('logs/laravel.log'), 'level' => env('LOG_LEVEL', 'debug')],
        'daily' => ['driver' => 'daily', 'path' => storage_path('logs/laravel.log'), 'level' => env('LOG_LEVEL', 'debug'), 'days' => 14],
        'null' => ['driver' => 'monolog', 'handler' => Monolog\Handler\NullHandler::class],
        'emergency' => ['path' => storage_path('logs/laravel.log')],

        // Audit channel — for DB queries, HTTP errors, exec failures, model events
        // Implements the user mandate: "all DB queries, API requests with response
        // distinct to 200-399 and exec process in the backend with errors must be
        // listened and logged for audit"
        'audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/audit.log'),
            'level' => env('AUDIT_LOG_LEVEL', 'info'),
            'days' => 30,
        ],

        // Campaign channel — for campaign send/bounce/tracking events
        'campaign' => [
            'driver' => 'daily',
            'path' => storage_path('logs/campaign.log'),
            'level' => 'info',
            'days' => 14,
        ],

        // Widget channel — for widget rendering issues
        'widget' => [
            'driver' => 'daily',
            'path' => storage_path('logs/widget.log'),
            'level' => 'info',
            'days' => 14,
        ],
    ],
];
