<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\FileOperationException;
use App\Services\ThemeEditorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ThemeEditorController — REST API for the theme code editor.
 *
 * All endpoints are auth-protected and audit-logged via ThemeEditorService.
 * Returns JSON responses for AJAX/Livewire consumption.
 */
class ThemeEditorController
{
    public function __construct(
        private readonly ThemeEditorService $themeEditor,
    ) {}

    public function files(Request $request): JsonResponse
    {
        $validated = $request->validate(['theme' => 'required|string|max:50']);
        try {
            return response()->json([
                'files' => $this->themeEditor->listFiles($validated['theme']),
            ]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function read(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => 'required|string|max:50',
            'path' => 'required|string|max:255',
        ]);
        try {
            return response()->json([
                'content' => $this->themeEditor->readFile($validated['theme'], $validated['path']),
            ]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => 'required|string|max:50',
            'path' => 'required|string|max:255',
            'content' => 'required|string|max:1048576', // 1MB
        ]);
        try {
            $this->themeEditor->saveFile(
                $validated['theme'],
                $validated['path'],
                $validated['content'],
            );
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function versions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => 'required|string|max:50',
            'path' => 'required|string|max:255',
        ]);
        try {
            $versions = $this->themeEditor->getVersions($validated['theme'], $validated['path']);
            return response()->json([
                'versions' => $versions->map(fn ($v) => [
                    'id' => $v->id,
                    'checksum' => substr($v->checksum, 0, 12),
                    'user_id' => $v->user_id,
                    'created_at' => $v->created_at?->toISOString(),
                ]),
            ]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function restore(Request $request): JsonResponse
    {
        $validated = $request->validate(['version_id' => 'required|integer|exists:theme_file_versions,id']);
        try {
            $this->themeEditor->restoreVersion($validated['version_id']);
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }
}
