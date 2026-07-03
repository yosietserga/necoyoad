<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LanguageContext;
use Closure;
use Illuminate\Http\Request;

/**
 * ResolveLanguageContext — multi-language resolution middleware.
 *
 * Runs on every request. Resolves the language via the 6-level
 * detection priority chain, and binds it to the container as
 * 'language.context'.
 *
 * @see v5 (6-level language detection)
 */
class ResolveLanguageContext
{
    public function __construct(
        private readonly LanguageContext $languageContext
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $language = $this->languageContext->resolve();

        // Bind to container for dependency injection
        app()->instance('language.context', $this->languageContext);

        // Set Laravel's locale
        app()->setLocale($language->code);

        // Share with views
        view()->share('language', $language);

        return $next($request);
    }
}
