<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * LogHttpResponse — terminating middleware that audits all HTTP responses
 * with status codes outside the 200-399 range.
 *
 * Implements the user mandate: "all API requests with response distinct to
 * 200-399 must be listened and logged for audit."
 */
class LogHttpResponse
{
    public function __construct(
        private readonly AuditService $auditService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip auditing the audit endpoint itself (prevents recursive logging)
        if ($request->is('api/audit/*')) {
            return $response;
        }

        // Log non-success responses (outside 200-399)
        if ($response->status() < 200 || $response->status() >= 400) {
            $this->auditService->logRequest(
                method: $request->method(),
                url: $request->fullUrl(),
                status: $response->status(),
            );
        }

        return $response;
    }
}
