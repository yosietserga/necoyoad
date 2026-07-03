<?php
return [
    'cache' => [
        'enabled' => env('ROUTE_CACHE_ENABLED', false),
        'path' => storage_path('framework/routes.php'),
    ],
];
