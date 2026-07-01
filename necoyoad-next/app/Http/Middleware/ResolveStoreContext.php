<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\StoreContext;
use Closure;
use Illuminate\Http\Request;

/**
 * ResolveStoreContext — multi-store resolution middleware.
 *
 * Runs on every request. Resolves the store from the domain/path/subdomain
 * via StoreContext, and binds it to the container as 'store.context'.
 *
 * @see v2 (store detection — 3 strategies)
 * @see v5 (multi-store architecture)
 */
class ResolveStoreContext
{
    public function __construct(
        private readonly StoreContext $storeContext
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $store = $this->storeContext->resolve();

        // Bind to container for dependency injection
        app()->instance('store.context', $this->storeContext);

        // Share with views
        view()->share('store', $store);

        return $next($request);
    }
}
