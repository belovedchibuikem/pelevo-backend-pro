<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use App\Models\User;
use App\Services\TaddyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaddyApiController extends Controller
{
    protected $taddyService;

    public function __construct(TaddyService $taddyService)
    {
        $this->taddyService = $taddyService;
    }

    /**
     * Search for podcasts.
     */
    public function searchPodcasts(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        try {
            $query = $request->input('query');
            $result = $this->taddyService->searchPodcasts($query);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get podcast details with episodes.
     */
    public function getPodcastDetailsWithEpisodes(string $taddyId)
    {
        try {
            $podcastDetails = $this->taddyService->getPodcastDetails($taddyId);
            $episodes = $this->taddyService->getPodcastEpisodes($taddyId);

            return response()->json([
                'podcast' => $podcastDetails,
                'episodes' => $episodes,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get categories (placeholder - Taddy API doesn't provide a direct endpoint).
     */
    public function getCategories()
    {
        // Taddy API documentation does not provide a direct endpoint for categories.
        // You might consider implementing categories manually or using a different API for this.
        return response()->json([
            'message' => 'Categories endpoint not directly supported by Taddy API. Consider alternative implementation.',
            'categories' => [
                ['id' => 1, 'name' => 'Technology'],
                ['id' => 2, 'name' => 'Science'],
                ['id' => 3, 'name' => 'Comedy'],
                // Add more categories as needed
            ]
        ]);
    }

    /**
     * Get featured podcasts.
     */
    public function getFeaturedPodcasts()
    {
        try {
            // Fetch featured podcasts from your database first
            $featuredPodcasts = Podcast::where('is_featured', true)->get();

            $taddyData = [];
            foreach ($featuredPodcasts as $podcast) {
                // Optionally fetch full details from Taddy API for each featured podcast
                // if additional data is needed beyond what's stored locally.
                // For now, we'll just use the local data.
                $taddyData[] = $this->taddyService->getPodcastDetails($podcast->taddy_id);
            }

            return response()->json($taddyData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get new episodes (placeholder - will fetch recent from Taddy/local).
     */
    public function getNewEpisodes()
    {
        try {
            // This would typically involve fetching recent episodes from Taddy or your database.
            // As Taddy API doesn't have a direct 'new episodes' endpoint, this might be a custom logic.
            // For now, let's fetch some recent episodes from your database.
            $recentEpisodes = Podcast::with('episodes')
                ->latest('created_at')
                ->take(10)
                ->get()
                ->pluck('episodes')
                ->flatten();

            $taddyData = [];
            foreach ($recentEpisodes as $episode) {
                // You might need a TaddyService method to get individual episode details
                // or ensure your local episode model has enough data.
                // For simplicity, returning local data for now.
                $taddyData[] = [ // Mocking Taddy data structure for episodes
                    'id' => $episode->taddy_id,
                    'title' => $episode->title,
                    'description' => $episode->description,
                    'audio_url' => $episode->audio_url,
                    'podcast_id' => $episode->podcast_id, // Assuming podcast_id is available
                    'published_date' => $episode->published_date,
                ];
            }

            return response()->json($taddyData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get recommended podcasts for a user based on their listening history.
     * This implementation uses local database data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendedPodcasts(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        try {
            // Get podcasts the user has already listened to
            $listenedPodcastIds = $user->listeningHistory()->pluck('podcast_id')->unique()->toArray();

            // Fetch podcasts that the user has NOT listened to
            // For simplicity, let's recommend some random podcasts that are not listened to yet.
            // In a real-world scenario, you'd implement more sophisticated recommendation logic (e.g., based on genres, authors, user ratings).
            $recommendedPodcasts = Podcast::whereNotIn('id', $listenedPodcastIds)
                ->inRandomOrder()
                ->take(10) // Limit to 10 recommendations
                ->get();
            
            $taddyData = [];
            foreach ($recommendedPodcasts as $podcast) {
                // Fetch full details from Taddy API for each recommended podcast
                // You might want to handle cases where Taddy API doesn't have the podcast or it fails
                try {
                    $taddyData[] = $this->taddyService->getPodcastDetails($podcast->taddy_id);
                } catch (\Exception $e) {
                    Log::warning('Could not fetch Taddy details for recommended podcast ' . $podcast->taddy_id . ': ' . $e->getMessage());
                    // Optionally, include local data if Taddy data is not available
                    $taddyData[] = [ // Fallback to local data structure
                        'id' => $podcast->taddy_id,
                        'title' => $podcast->title,
                        'description' => $podcast->description,
                        'image_url' => $podcast->image_url,
                        'author' => $podcast->author,
                    ];
                }
            }

            return response()->json($taddyData);
        } catch (\Exception $e) {
            Log::error('Error getting recommended podcasts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve recommended podcasts.'], 500);
        }
    }

    /**
     * Get podcasts the user is subscribed to.
     */
    public function getSubscribedPodcasts(Request $request)
    {
        $user = $request->user(); // Authenticated user
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        try {
            $subscribedPodcasts = $user->subscriptions()->with('podcast')->get()->pluck('podcast');
            
            $taddyData = [];
            foreach ($subscribedPodcasts as $podcast) {
                $taddyData[] = $this->taddyService->getPodcastDetails($podcast->taddy_id);
            }
            return response()->json($taddyData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 