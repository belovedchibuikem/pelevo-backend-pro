<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaylistRequest;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlaylistsController extends Controller
{
    /**
     * Display a listing of the user's playlists.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $playlists = $request->user()
            ->playlists()
            ->with(['items.episode.podcast'])
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return PlaylistResource::collection($playlists);
    }

    /**
     * Store a newly created playlist.
     */
    public function store(PlaylistRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['order'] = $request->user()->playlists()->max('order') + 1;

        $playlist = $request->user()->playlists()->create($data);

        return response()->json([
            'message' => 'Playlist created successfully',
            'data' => new PlaylistResource($playlist->load(['items.episode.podcast']))
        ], 201);
    }

    /**
     * Display the specified playlist.
     */
    public function show(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => new PlaylistResource($playlist->load(['items.episode.podcast']))
        ]);
    }

    /**
     * Update the specified playlist.
     */
    public function update(PlaylistRequest $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $playlist->update($request->validated());

        return response()->json([
            'message' => 'Playlist updated successfully',
            'data' => new PlaylistResource($playlist->load(['items.episode.podcast']))
        ]);
    }

    /**
     * Remove the specified playlist.
     */
    public function destroy(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $playlist->delete();

        return response()->json(['message' => 'Playlist deleted successfully']);
    }

    /**
     * Add an episode to a playlist.
     */
    public function addEpisode(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'episode_id' => 'required|exists:episodes,id'
        ]);

        // Check if episode is already in playlist
        $existingItem = $playlist->items()->where('episode_id', $request->episode_id)->first();
        if ($existingItem) {
            return response()->json(['message' => 'Episode already in playlist'], 422);
        }

        $order = $playlist->items()->max('order') + 1;
        $playlist->items()->create([
            'episode_id' => $request->episode_id,
            'order' => $order,
            'added_at' => now()
        ]);

        return response()->json([
            'message' => 'Episode added to playlist successfully',
            'data' => new PlaylistResource($playlist->load(['items.episode.podcast']))
        ]);
    }

    /**
     * Remove an episode from a playlist.
     */
    public function removeEpisode(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'episode_id' => 'required|exists:episodes,id'
        ]);

        $deletedCount = $playlist->items()->where('episode_id', $request->episode_id)->delete();

        if ($deletedCount === 0) {
            return response()->json(['message' => 'Episode not found in playlist'], 404);
        }

        return response()->json(['message' => 'Episode removed from playlist successfully']);
    }

    /**
     * Reorder playlist items.
     */
    public function reorder(Request $request, Playlist $playlist): JsonResponse
    {
        if ($playlist->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'item_orders' => 'required|array',
            'item_orders.*.id' => 'required|exists:playlist_items,id',
            'item_orders.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->item_orders as $itemOrder) {
            $playlist->items()
                ->where('id', $itemOrder['id'])
                ->update(['order' => $itemOrder['order']]);
        }

        return response()->json([
            'message' => 'Playlist reordered successfully',
            'data' => new PlaylistResource($playlist->load(['items.episode.podcast']))
        ]);
    }

    /**
     * Remove multiple playlists.
     */
    public function batchDestroy(Request $request): JsonResponse
    {
        $request->validate([
            'playlist_ids' => 'required|array',
            'playlist_ids.*' => 'exists:playlists,id'
        ]);

        $deletedCount = $request->user()
            ->playlists()
            ->whereIn('id', $request->playlist_ids)
            ->delete();

        return response()->json([
            'message' => "Successfully deleted {$deletedCount} playlists"
        ]);
    }
}
