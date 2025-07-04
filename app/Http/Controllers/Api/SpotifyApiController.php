<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpotifyApiController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * Search for podcasts on Spotify.
     *
     * @param Request $request The incoming request with 'query' and optional 'limit'.
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPodcasts(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $query = $request->input('query');
            $limit = $request->input('limit', 10);
            $results = $this->spotifyService->searchPodcasts($query, $limit);
            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error searching Spotify podcasts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search podcasts.'], 500);
        }
    }

    /**
     * Get details for a specific podcast (show) from Spotify.
     *
     * @param string $showId The Spotify ID of the show.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPodcastDetails(string $showId)
    {
        try {
            $details = $this->spotifyService->getPodcastDetails($showId);
            return response()->json($details);
        } catch (\Exception $e) {
            Log::error('Error getting Spotify podcast details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve podcast details.'], 500);
        }
    }

    /**
     * Get episodes for a specific podcast (show) from Spotify.
     *
     * @param string $showId The Spotify ID of the show.
     * @param Request $request The incoming request with optional 'limit'.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPodcastEpisodes(string $showId, Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $limit = $request->input('limit', 10);
            $episodes = $this->spotifyService->getPodcastEpisodes($showId, $limit);
            return response()->json($episodes);
        } catch (\Exception $e) {
            Log::error('Error getting Spotify podcast episodes: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve podcast episodes.'], 500);
        }
    }

    /**
     * Get a list of Spotify browse categories.
     *
     * @param Request $request The incoming request with optional 'country'.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request)
    {
        try {
            $country = $request->input('country', 'US');
            $categories = $this->spotifyService->getCategories($country);
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Error getting Spotify categories: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve categories.'], 500);
        }
    }

    /**
     * Get a combined feed of general Spotify podcasts, featured podcasts, and new episodes.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCombinedPodcastFeed(Request $request)
    {
        try {
            // 1. General Spotify Podcasts (e.g., a popular or trending search)
            // You might want to make this configurable or based on user preference
            $generalPodcasts = $this->spotifyService->searchPodcasts('daily news', 5); // Example search

            // 2. Featured Podcasts from your local database
            $featuredPodcasts = \App\Models\Podcast::where('is_featured', true)->take(5)->get();
            $featuredSpotifyPodcasts = [];
            foreach ($featuredPodcasts as $podcast) {
                try {
                    $spotifyDetails = $this->spotifyService->getPodcastDetails($podcast->taddy_id); // Assuming taddy_id is Spotify ID
                    $featuredSpotifyPodcasts[] = $spotifyDetails;
                } catch (\Exception $e) {
                    Log::warning("Could not fetch Spotify details for featured podcast ID {$podcast->taddy_id}: " . $e->getMessage());
                    // Fallback to local data or skip if Spotify data is crucial
                }
            }

            // 3. New Episodes from your local database
            $newLocalEpisodes = \App\Models\Episode::latest('published_at')->take(5)->get();
            $newSpotifyEpisodes = [];
            foreach ($newLocalEpisodes as $episode) {
                if ($episode->podcast && $episode->podcast->taddy_id) { // Assuming taddy_id is Spotify ID for the show
                    try {
                        // Fetch episodes for the show, then find the specific episode if needed
                        $showEpisodes = $this->spotifyService->getPodcastEpisodes($episode->podcast->taddy_id, 1);
                        // In a real scenario, you'd match by episode ID or title more precisely
                        if (!empty($showEpisodes['items'])) {
                            $newSpotifyEpisodes[] = $showEpisodes['items'][0]; // Just taking the first for simplicity
                        }
                    } catch (\Exception $e) {
                        Log::warning("Could not fetch Spotify episode details for episode ID {$episode->taddy_episode_id}: " . $e->getMessage());
                    }
                }
            }

            return response()->json([
                'general_podcasts' => $generalPodcasts['shows']['items'] ?? [],
                'featured_podcasts' => $featuredSpotifyPodcasts,
                'new_episodes' => $newSpotifyEpisodes,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in combined podcast feed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve combined podcast feed.'], 500);
        }
    }

    /**
     * Placeholder for user-specific features requiring Spotify user authentication.
     * This would typically involve the Authorization Code Flow.
     */
    public function subscribeToPodcast(Request $request)
    {
        // This requires Spotify user authentication (e.g., Authorization Code Flow)
        // and saving the subscription status in your database linked to the user.
        return response()->json(['message' => 'Subscription feature requires Spotify user authentication and implementation.'], 400);
    }

    public function getNewEpisodes(Request $request)
    {
        // This would require Spotify user authentication to get personalized new episodes
        // from podcasts the user follows, or fetching new episodes from your local database.
        return response()->json(['message' => 'New episodes feature requires Spotify user authentication or local data processing.'], 400);
    }

    public function getFeaturedPodcasts()
    {
        // This would involve fetching featured podcasts either from Spotify (if a relevant endpoint exists)
        // or from your own database's `is_featured` flag, and then fetching details from Spotify.
        return response()->json(['message' => 'Featured podcasts feature needs specific logic based on criteria.'], 400);
    }

    public function getRecommendedPodcasts(Request $request)
    {
        // This requires Spotify user authentication to get personalized recommendations
        // or implementing custom recommendation logic based on user listening history from your database.
        return response()->json(['message' => 'Recommended podcasts feature requires Spotify user authentication or custom recommendation logic.'], 400);
    }
} 