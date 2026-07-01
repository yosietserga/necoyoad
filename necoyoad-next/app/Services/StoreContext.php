<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\Request;

/**
 * StoreContext — resolves the current store from the request.
 *
 * Three detection strategies (same as original Necoyoad, v2/v5):
 *   1. URL path segment match (checks store.folder against path parts)
 *   2. ?store_id GET parameter
 *   3. Subdomain regex (captures leftmost label of the domain)
 *
 * The resolved store is cached for the request lifetime and made
 * available via the 'store.context' container binding.
 *
 * @see v2 (store detection algorithm)
 * @see v5 (multi-store/multi-language architecture)
 */
class StoreContext
{
    private ?Store $store = null;

    public function __construct(
        private readonly Request $request
    ) {}

    public function resolve(): ?Store
    {
        if ($this->store) {
            return $this->store;
        }

        // Strategy 1: ?store_id GET parameter
        if ($this->request->has('store_id')) {
            $this->store = Store::find($this->request->query('store_id'));
            return $this->store;
        }

        // Strategy 2: Subdomain detection
        $host = $this->request->getHost();
        $parts = explode('.', $host);

        // If we have a subdomain (more than 2 parts, not 'www')
        if (count($parts) > 2 && $parts[0] !== 'www') {
            $subdomain = $parts[0];
            $this->store = Store::where('folder', $subdomain)->first();
            if ($this->store) {
                return $this->store;
            }
        }

        // Strategy 3: URL path folder match
        $path = $this->request->path();
        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if (empty($segment)) continue;
            $store = Store::where('folder', $segment)->first();
            if ($store) {
                $this->store = $store;
                return $this->store;
            }
        }

        // Fallback: default store (id=0 or the first active store)
        $this->store = Store::where('is_default', true)->first()
            ?? Store::find(0)
            ?? Store::first();

        return $this->store;
    }

    public function id(): int
    {
        return $this->resolve()?->id ?? 0;
    }

    public function model(): ?Store
    {
        return $this->resolve();
    }

    public function folder(): ?string
    {
        return $this->resolve()?->folder;
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->resolve()?->settings[$key] ?? $default;
    }
}
