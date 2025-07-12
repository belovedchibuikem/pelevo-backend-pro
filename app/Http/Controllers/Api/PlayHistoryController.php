<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayHistoryResource;
use App\Models\PlayHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlayHistoryController extends Controller
{
    /**
     * Display a listing of the user's play history.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $playHistories = $request->user()
            ->playHistories()
            ->with(['episode.podcast'])
            ->orderBy('last_played_at', 'desc')
            ->paginate(20);

        return PlayHistoryResource::collection($playHistories);
    }

    /**
     * Store a newly created play history entry.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'episode_id' => 'required|exists:episodes,id',
            'progress_seconds' => 'required|integer|min:0',
            'status' => 'required|in:played,paused,completed'
        ]);

        $playHistory = $request->user()->playHistories()->updateOrCreate(
            ['episode_id' => $request->episode_id],
            [
                'progress_seconds' => $request->progress_seconds,
                'status' => $request->status,
                'last_played_at' => now()
            ]
        );

        return response()->json([
            'message' => 'Play history updated successfully',
            'data' => new PlayHistoryResource($playHistory->load(['episode.podcast']))
        ], 201);
    }

    /**
     * Display the specified play history entry.
     */
    public function show(Request $request, PlayHistory $playHistory): JsonResponse
    {
        if ($playHistory->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => new PlayHistoryResource($playHistory->load(['episode.podcast']))
        ]);
    }

    /**
     * Update the specified play history entry.
     */
    public function update(Request $request, PlayHistory $playHistory): JsonResponse
    {
        if ($playHistory->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'progress_seconds' => 'required|integer|min:0',
            'status' => 'required|in:played,paused,completed'
        ]);

        $playHistory->update([
            'progress_seconds' => $request->progress_seconds,
            'status' => $request->status,
            'last_played_at' => now()
        ]);

        return response()->json([
            'message' => 'Play history updated successfully',
            'data' => new PlayHistoryResource($playHistory->load(['episode.podcast']))
        ]);
    }

    /**
     * Remove the specified play history entry.
     */
    public function destroy(Request $request, PlayHistory $playHistory): JsonResponse
    {
        if ($playHistory->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $playHistory->delete();

        return response()->json(['message' => 'Play history entry removed successfully']);
    }

    /**
     * Remove multiple play history entries.
     */
    public function batchDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'play_history_ids' => 'required|array',
            'play_history_ids.*' => 'exists:play_history,id'
        ]);

        $deletedCount = $request->user()
            ->playHistories()
            ->whereIn('id', $request->play_history_ids)
            ->delete();

        return response()->json([
            'message' => "Successfully removed {$deletedCount} play history entries"
        ]);
    }

    /**
     * Clear all play history for the user.
     */
    public function clearAll(Request $request): JsonResponse
    {
        $deletedCount = $request->user()->playHistories()->delete();

        return response()->json([
            'message' => "Successfully cleared {$deletedCount} play history entries"
        ]);
    }

    /**
     * Get recently played episodes.
     */
    public function recent(Request $request): AnonymousResourceCollection
    {
        $recent = $request->user()
            ->playHistories()
            ->with(['episode.podcast'])
            ->orderBy('last_played_at', 'desc')
            ->limit(10)
            ->get();

        return PlayHistoryResource::collection($recent);
    }
}
