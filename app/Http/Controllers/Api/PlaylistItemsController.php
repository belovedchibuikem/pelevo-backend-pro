<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaylistItemResource;
use App\Models\PlaylistItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlaylistItemsController extends Controller
{
    /**
     * Display a listing of playlist items.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'playlist_id' => 'required|exists:playlists,id'
        ]);

        $playlistItems = PlaylistItem::where('playlist_id', $request->playlist_id)
            ->with(['episode.podcast'])
            ->orderBy('order')
            ->paginate(20);

        return PlaylistItemResource::collection($playlistItems);
    }

    /**
     * Store a newly created playlist item.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'playlist_id' => 'required|exists:playlists,id',
            'episode_id' => 'required|exists:episodes,id'
        ]);

        // Check if episode is already in playlist
        $existingItem = PlaylistItem::where('playlist_id', $request->playlist_id)
            ->where('episode_id', $request->episode_id)
            ->first();

        if ($existingItem) {
            return response()->json(['message' => 'Episode already in playlist'], 422);
        }

        $order = PlaylistItem::where('playlist_id', $request->playlist_id)->max('order') + 1;
        
        $playlistItem = PlaylistItem::create([
            'playlist_id' => $request->playlist_id,
            'episode_id' => $request->episode_id,
            'order' => $order,
            'added_at' => now()
        ]);

        return response()->json([
            'message' => 'Playlist item added successfully',
            'data' => new PlaylistItemResource($playlistItem->load(['episode.podcast']))
        ], 201);
    }

    /**
     * Display the specified playlist item.
     */
    public function show(PlaylistItem $playlistItem): JsonResponse
    {
        return response()->json([
            'data' => new PlaylistItemResource($playlistItem->load(['episode.podcast']))
        ]);
    }

    /**
     * Update the specified playlist item.
     */
    public function update(Request $request, PlaylistItem $playlistItem): JsonResponse
    {
        $request->validate([
            'order' => 'sometimes|integer|min:0'
        ]);

        $playlistItem->update($request->only(['order']));

        return response()->json([
            'message' => 'Playlist item updated successfully',
            'data' => new PlaylistItemResource($playlistItem->load(['episode.podcast']))
        ]);
    }

    /**
     * Remove the specified playlist item.
     */
    public function destroy(PlaylistItem $playlistItem): JsonResponse
    {
        $playlistItem->delete();

        return response()->json(['message' => 'Playlist item removed successfully']);
    }

    /**
     * Remove multiple playlist items.
     */
    public function batchDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'playlist_item_ids' => 'required|array',
            'playlist_item_ids.*' => 'exists:playlist_items,id'
        ]);

        $deletedCount = PlaylistItem::whereIn('id', $request->playlist_item_ids)->delete();

        return response()->json([
            'message' => "Successfully removed {$deletedCount} playlist items"
        ]);
    }
}
