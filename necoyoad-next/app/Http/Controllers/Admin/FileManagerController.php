<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\FileOperationException;
use App\Services\FileManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * FileManagerController — REST API for the file manager.
 *
 * All endpoints are auth-protected and audit-logged via FileManagerService.
 * Returns JSON responses for AJAX/Livewire consumption.
 */
class FileManagerController
{
    public function __construct(
        private readonly FileManagerService $fileManager,
    ) {}

    public function directories(Request $request): JsonResponse
    {
        try {
            $path = $request->query('path', '/');
            return response()->json([
                'directories' => $this->fileManager->listDirectories($path),
            ]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function files(Request $request): JsonResponse
    {
        try {
            $path = $request->query('path', '/');
            return response()->json([
                'files' => $this->fileManager->listFiles($path),
            ]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function createDirectory(Request $request): JsonResponse
    {
        $validated = $request->validate(['path' => 'required|string|max:255']);
        try {
            $this->fileManager->createDirectory($validated['path']);
            return response()->json(['success' => true], 201);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function deleteFile(Request $request): JsonResponse
    {
        $validated = $request->validate(['path' => 'required|string|max:255']);
        try {
            $this->fileManager->deleteFile($validated['path']);
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function deleteDirectory(Request $request): JsonResponse
    {
        $validated = $request->validate(['path' => 'required|string|max:255']);
        try {
            $this->fileManager->deleteDirectory($validated['path']);
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function move(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
        ]);
        try {
            $this->fileManager->moveFile($validated['from'], $validated['to']);
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function copy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
        ]);
        try {
            $this->fileManager->copyFile($validated['from'], $validated['to']);
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function rename(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
        ]);
        try {
            $this->fileManager->renameFile($validated['from'], $validated['to']);
            return response()->json(['success' => true]);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file',
            'directory' => 'nullable|string|max:255',
        ]);
        try {
            $path = $this->fileManager->uploadFile(
                $request->file('file'),
                $validated['directory'] ?? '/',
            );
            return response()->json(['success' => true, 'path' => $path], 201);
        } catch (FileOperationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function thumbnail(Request $request): Response
    {
        $validated = $request->validate([
            'path' => 'required|string|max:255',
            'w' => 'nullable|integer|min:1|max:2000',
            'h' => 'nullable|integer|min:1|max:2000',
        ]);
        try {
            $url = $this->fileManager->getThumbnail(
                $validated['path'],
                $validated['w'] ?? 150,
                $validated['h'] ?? 150,
            );
            return redirect($url)->setStatusCode(302);
        } catch (FileOperationException $e) {
            return response($e->getMessage(), $e->getStatusCode());
        }
    }
}
