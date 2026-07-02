<?php

use App\Http\Controllers\AuditController;
use Illuminate\Support\Facades\Route;

/**
 * API routes.
 *
 * The browser audit endpoint is CSRF-exempt (api routes don't run CSRF
 * middleware) because navigator.sendBeacon() can't reliably attach the
 * CSRF token. Rate-limited to prevent abuse.
 */

// Browser audit endpoint (receives console errors + failed network requests)
Route::post('/audit/browser', [AuditController::class, 'browser'])
    ->middleware('throttle:60,1')
    ->name('audit.browser');
