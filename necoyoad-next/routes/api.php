<?php

use Illuminate\Support\Facades\Route;

/**
 * API routes — for the REST API (Sanctum-protected).
 * Will be implemented in Phase 4.
 */

Route::middleware('auth:sanctum')->group(function () {
    // Product API
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);

    // Category API
    Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);

    // Post/Page API
    Route::apiResource('posts', App\Http\Controllers\Api\PostController::class);

    // Banner API
    Route::apiResource('banners', App\Http\Controllers\Api\BannerController::class);
});
