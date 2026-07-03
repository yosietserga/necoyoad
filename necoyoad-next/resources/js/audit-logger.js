/**
 * Necoyoad — Browser Audit Logger
 *
 * Implements the user mandate: "all browser's console errors and network
 * requests with responses distinct to 200-399 must be tracked and logged
 * for audit."
 *
 * This script runs in the browser and captures:
 *   1. Uncaught JavaScript errors (window.onerror)
 *   2. Unhandled promise rejections (unhandledrejection event)
 *   3. console.error() calls (wrapped)
 *   4. Failed fetch/XHR network requests (status outside 200-399)
 *
 * Captured events are batched and sent to POST /api/audit/browser via
 * navigator.sendBeacon (non-blocking, survives page unload).
 */

(function () {
    'use strict';

    const AUDIT_ENDPOINT = '/api/audit/browser'; // API route (CSRF-exempt, rate-limited)
    const BATCH_SIZE = 10;
    const BATCH_INTERVAL_MS = 5000;

    let batch = [];
    let flushTimer = null;

    /**
     * Queue an audit event for transmission.
     */
    function queue(event) {
        event.timestamp = new Date().toISOString();
        event.url = window.location.href;
        event.userAgent = navigator.userAgent;

        batch.push(event);

        if (batch.length >= BATCH_SIZE) {
            flush();
        } else if (!flushTimer) {
            flushTimer = setTimeout(flush, BATCH_INTERVAL_MS);
        }
    }

    /**
     * Send the batch to the backend audit endpoint.
     * Uses sendBeacon for non-blocking delivery (survives page unload).
     */
    function flush() {
        if (batch.length === 0) return;

        const payload = JSON.stringify({ events: batch });
        const currentBatch = batch;
        batch = [];

        if (flushTimer) {
            clearTimeout(flushTimer);
            flushTimer = null;
        }

        // sendBeacon is non-blocking and survives page navigation
        if (navigator.sendBeacon) {
            const blob = new Blob([payload], { type: 'application/json' });
            const sent = navigator.sendBeacon(AUDIT_ENDPOINT, blob);
            if (sent) return;
        }

        // Fallback to fetch if sendBeacon fails or is unavailable
        fetch(AUDIT_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: payload,
            keepalive: true,
        }).catch(function () {
            // If the audit endpoint itself fails, re-queue the events
            // (but cap to prevent infinite growth)
            if (batch.length < 50) {
                batch = currentBatch.concat(batch);
            }
        });
    }

    // 1. Capture uncaught JavaScript errors
    window.addEventListener('error', function (event) {
        queue({
            type: 'js_error',
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            stack: event.error?.stack?.substring(0, 2000),
        });
    });

    // 2. Capture unhandled promise rejections
    window.addEventListener('unhandledrejection', function (event) {
        queue({
            type: 'promise_rejection',
            message: event.reason?.message || String(event.reason),
            stack: event.reason?.stack?.substring(0, 2000),
        });
    });

    // 3. Wrap console.error to capture explicit error logs
    const originalConsoleError = console.error;
    console.error = function () {
        const args = Array.from(arguments);
        const message = args.map(function (arg) {
            if (typeof arg === 'string') return arg;
            try { return JSON.stringify(arg); } catch (e) { return String(arg); }
        }).join(' ');

        queue({
            type: 'console_error',
            message: message.substring(0, 2000),
        });

        originalConsoleError.apply(console, args);
    };

    // 4. Capture failed fetch requests
    const originalFetch = window.fetch;
    window.fetch = function () {
        const args = arguments;
        const url = typeof args[0] === 'string' ? args[0] : args[0]?.url;

        return originalFetch.apply(this, args).then(function (response) {
            if (response.status < 200 || response.status >= 400) {
                queue({
                    type: 'fetch_error',
                    method: args[1]?.method || 'GET',
                    url: url,
                    status: response.status,
                    statusText: response.statusText,
                });
            }
            return response;
        });
    };

    // 5. Capture failed XMLHttpRequests
    const originalXhrOpen = XMLHttpRequest.prototype.open;
    const originalXhrSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function (method, url) {
        this._auditMethod = method;
        this._auditUrl = url;
        return originalXhrOpen.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function () {
        const self = this;
        this.addEventListener('loadend', function () {
            if (self.status < 200 || self.status >= 400) {
                queue({
                    type: 'xhr_error',
                    method: self._auditMethod,
                    url: self._auditUrl,
                    status: self.status,
                    statusText: self.statusText,
                });
            }
        });
        return originalXhrSend.apply(this, arguments);
    };

    // 6. Flush on page unload (best-effort)
    window.addEventListener('beforeunload', flush);
    window.addEventListener('pagehide', flush);

    // Expose for debugging
    window.__necoyoadAudit = { flush: flush, queue: queue };
})();
