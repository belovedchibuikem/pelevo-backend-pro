<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadRequest;
use App\Http\Resources\DownloadResource;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DownloadsController extends Controller
{
    /**
     * Display a listing of the user's downloads.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $downloads = $request->user()
            ->downloads()
            ->with(['episode.podcast'])
            ->orderBy('downloaded_at', 'desc')
            ->paginate(20);

        return DownloadResource::collection($downloads);
    }

    /**
     * Store a newly created download.
     */
    public function store(DownloadRequest $request): JsonResponse
    {
        $download = $request->user()->downloads()->create($request->validated());

        return response()->json([
            'message' => 'Download added successfully',
            'data' => new DownloadResource($download->load(['episode.podcast']))
        ], 201);
    }

    /**
     * Display the specified download.
     */
    public function show(Request $request, Download $download): JsonResponse
    {
        if ($download->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => new DownloadResource($download->load(['episode.podcast']))
        ]);
    }

    /**
     * Remove the specified download.
     */
    public function destroy(Request $request, Download $download): JsonResponse
    {
        if ($download->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $download->delete();

        return response()->json(['message' => 'Download removed successfully']);
    }

    /**
     * Remove multiple downloads.
     */
    public function batchDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'download_ids' => 'required|array',
            'download_ids.*' => 'exists:downloads,id'
        ]);

        $deletedCount = $request->user()
            ->downloads()
            ->whereIn('id', $request->download_ids)
            ->delete();

        return response()->json([
            'message' => "Successfully removed {$deletedCount} downloads"
        ]);
    }

    /**
     * Clear all downloads for the user.
     */
    public function clearAll(Request $request): JsonResponse
    {
        $deletedCount = $request->user()->downloads()->delete();

        return response()->json([
            'message' => "Successfully removed {$deletedCount} downloads"
        ]);
    }
}
