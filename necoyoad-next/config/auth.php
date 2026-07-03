<?php

/**
 * New Necoyoad — Auth Configuration
 *
 * Defines two guards:
 * - 'web' (default): session-based for Filament admin (users table)
 * - 'customer': session-based for storefront customers (customers table)
 *
 * Uses bcrypt (not double-MD5 from the original, v2/v10).
 */

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'customer' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => null,
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Customer::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Customer::class,
            'table' => 'customer_password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
