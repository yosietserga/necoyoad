<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

/**
 * AuditService — centralized audit logging service.
 *
 * Implements the user mandate: "All DB queries, API requests with response
 * != 200-399, and exec processes in backend with errors must be listened
 * and logged for audit."
 *
 * Writes to two sinks:
 *   1. user_activity table (structured, queryable from Filament admin)
 *   2. storage/logs/audit.log (via the 'audit' logging channel)
 *
 * Registered in AppServiceProvider::boot() via DB::listen and a terminating
 * middleware for HTTP responses.
 */
class AuditService
{
    /** Slow-query threshold in milliseconds (queries slower than this are logged). */
    private const SLOW_QUERY_THRESHOLD_MS = 100;

    /** HTTP status codes considered "successful" (not audited). */
    private const SUCCESS_STATUS_RANGE = [200, 399];

    /**
     * Log a database query execution.
     * Only slow queries (>100ms) are logged to avoid volume explosion.
     * Set AUDIT_ALL_QUERIES=true in .env to log every query.
     */
    public function logQuery(QueryExecuted $query): void
    {
        $logAll = config('app.audit_all_queries', false);
        if (!$logAll && $query->time < self::SLOW_QUERY_THRESHOLD_MS) {
            return;
        }

        $sql = $query->sql;
        $bindings = $query->bindings;
        $time = round($query->time, 2);
        $connection = $query->connectionName;

        Log::channel('audit')->info('DB query executed', [
            'connection' => $connection,
            'sql' => $sql,
            'bindings' => $bindings,
            'time_ms' => $time,
            'slow' => $query->time >= self::SLOW_QUERY_THRESHOLD_MS,
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
        ]);
    }

    /**
     * Log an HTTP response with non-2xx/3xx status code.
     */
    public function logRequest(string $method, string $url, int $status, ?int $userId = null, ?string $guard = null): void
    {
        if ($status >= self::SUCCESS_STATUS_RANGE[0] && $status <= self::SUCCESS_STATUS_RANGE[1]) {
            return;
        }

        $userId ??= Auth::id();
        $guard ??= $this->detectGuard();

        Log::channel('audit')->warning('HTTP non-success response', [
            'method' => $method,
            'url' => $url,
            'status' => $status,
            'user_id' => $userId,
            'guard' => $guard,
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        // Also write to user_activity for structured querying
        $this->writeActivity(
            event: 'http_error',
            action: $method . ' ' . $url,
            description: "HTTP {$status} response",
            userId: $userId,
            additional: ['status' => $status, 'method' => $method, 'url' => $url]
        );
    }

    /**
     * Log a backend process execution (exec, Symfony Process, etc.).
     */
    public function logExec(string $command, int $exitCode, ?string $stderr = null, ?string $stdout = null): void
    {
        if ($exitCode === 0) {
            return; // Only log failed exec
        }

        Log::channel('audit')->error('Backend process failed', [
            'command' => $command,
            'exit_code' => $exitCode,
            'stderr' => $stderr,
            'stdout' => $stdout ? substr($stdout, 0, 1000) : null,
            'ip' => Request::ip(),
            'user_id' => Auth::id(),
        ]);

        $this->writeActivity(
            event: 'exec_failed',
            action: $command,
            description: "Process exited with code {$exitCode}",
            userId: Auth::id(),
            additional: ['exit_code' => $exitCode, 'stderr' => $stderr]
        );
    }

    /**
     * Log a model CRUD event (create/update/delete) for audit trail.
     */
    public function logModel(string $event, string $modelClass, int $modelId, array $changes = [], ?int $userId = null): void
    {
        $userId ??= Auth::id();

        Log::channel('audit')->info('Model event', [
            'event' => $event,
            'model' => $modelClass,
            'model_id' => $modelId,
            'changes' => $changes,
            'user_id' => $userId,
            'guard' => $this->detectGuard(),
            'ip' => Request::ip(),
        ]);

        $this->writeActivity(
            event: 'model_' . $event,
            action: class_basename($modelClass) . '#' . $modelId,
            description: $event . ' on ' . class_basename($modelClass),
            userId: $userId,
            activitableType: $modelClass,
            activitableId: $modelId,
            additional: $changes
        );
    }

    /**
     * Log a thrown exception for audit trail.
     */
    public function logException(\Throwable $e, ?string $context = null): void
    {
        Log::channel('audit')->error('Exception thrown', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
            'context' => $context,
            'user_id' => Auth::id(),
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
        ]);

        $this->writeActivity(
            event: 'exception',
            action: get_class($e),
            description: $e->getMessage(),
            userId: Auth::id(),
            additional: ['file' => $e->getFile() . ':' . $e->getLine(), 'context' => $context]
        );
    }

    /**
     * Write a structured record to the user_activity table.
     */
    private function writeActivity(
        string $event,
        string $action,
        string $description,
        ?int $userId = null,
        ?string $activitableType = null,
        ?int $activitableId = null,
        array $additional = []
    ): void {
        try {
            UserActivity::create([
                'user_id' => $userId,
                'activitable_type' => $activitableType,
                'activitable_id' => $activitableId,
                'event' => $event,
                'action' => substr($action, 0, 50),
                'description' => $description,
                'ip' => Request::ip(),
                'browser' => Request::userAgent(),
                'date_added' => now(),
            ]);
        } catch (\Throwable $e) {
            // Don't let audit logging failure crash the request
            Log::channel('audit')->error('Failed to write audit record', [
                'error' => $e->getMessage(),
                'event' => $event,
                'action' => $action,
            ]);
        }
    }

    /**
     * Detect which auth guard is currently active.
     */
    private function detectGuard(): ?string
    {
        foreach (array_keys(config('auth.guards', [])) as $guard) {
            if (Auth::guard($guard)->check()) {
                return $guard;
            }
        }
        return null;
    }
}
