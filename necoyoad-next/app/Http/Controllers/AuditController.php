<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AuditController — receives browser-side audit events and logs them.
 *
 * Implements the user mandate: "all browser's console errors and network
 * requests with responses distinct to 200-399 must be tracked and logged
 * for audit."
 *
 * The browser sends batched events via navigator.sendBeacon to
 * POST /api/audit/browser. This controller validates, logs to the
 * 'audit' channel, and writes structured records to user_activity.
 */
class AuditController extends Controller
{
    /**
     * Receive a batch of browser audit events.
     */
    public function browser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'events' => 'required|array|max:50',
            'events.*.type' => 'required|string|max:50',
            'events.*.message' => 'nullable|string|max:2000',
            'events.*.timestamp' => 'nullable|string|max:50',
            'events.*.url' => 'nullable|string|max:500',
            'events.*.userAgent' => 'nullable|string|max:500',
            'events.*.filename' => 'nullable|string|max:500',
            'events.*.lineno' => 'nullable|integer',
            'events.*.colno' => 'nullable|integer',
            'events.*.stack' => 'nullable|string|max:2000',
            'events.*.method' => 'nullable|string|max:10',
            'events.*.status' => 'nullable|integer',
            'events.*.statusText' => 'nullable|string|max:100',
        ]);

        $logged = 0;

        foreach ($validated['events'] as $event) {
            $type = $event['type'] ?? 'unknown';
            $message = $event['message'] ?? '';

            // Log to the audit channel
            Log::channel('audit')->warning('Browser audit event', [
                'type' => $type,
                'message' => $message,
                'url' => $event['url'] ?? null,
                'filename' => $event['filename'] ?? null,
                'lineno' => $event['lineno'] ?? null,
                'method' => $event['method'] ?? null,
                'status' => $event['status'] ?? null,
                'user_agent' => $event['userAgent'] ?? null,
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);

            // Write structured record to user_activity
            try {
                UserActivity::create([
                    'user_id' => auth()->id(),
                    'event' => 'browser_' . $type,
                    'action' => substr($event['method'] . ' ' . ($event['url'] ?? ''), 0, 50),
                    'description' => substr($message, 0, 65535),
                    'ip' => $request->ip(),
                    'browser' => $event['userAgent'] ?? $request->userAgent(),
                    'date_added' => now(),
                ]);
                $logged++;
            } catch (\Throwable $e) {
                // Don't let one failed record stop the batch
                Log::channel('audit')->error('Failed to write browser audit record', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['logged' => $logged], 200);
    }
}
