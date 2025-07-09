<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PodcastIndexService;
use Illuminate\Support\Facades\Auth;
use App\Models\PodcastIndexSubscription;
use App\Notifications\PodcastIndexNewEpisodeNotification;
use App\Services\PodcastRecommendationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PodcastIndexController extends Controller
{
    protected $podcastIndexService;
    protected $recommendationService;

    public function __construct(PodcastIndexService $podcastIndexService, PodcastRecommendationService $recommendationService)
    {
        $this->podcastIndexService = $podcastIndexService;
        $this->recommendationService = $recommendationService;
    }

    // 1. Get podcast categories
    public function categories()
    {
        $categories = $this->podcastIndexService->getCategories();
        
        // If categories already have counts (from the service), return as is
        if (is_array($categories) && !empty($categories) && isset($categories[0]['count'])) {
            return response()->json($categories);
        }
        
        // Fallback to old structure if needed
        if (isset($categories['categories']) && is_array($categories['categories'])) {
            $result = [];
            foreach ($categories['categories'] as $id => $name) {
                $result[] = [
                    'id' => (string)$id,
                    'name' => $name,
                    'count' => 0, // Default count if not available
                ];
            }
            return response()->json($result);
        }
        
        return response()->json($categories);
    }

    // 2. Get podcast details with episodes
    public function show($feedId)
    {
        Log::info("Podcast detail request for feedId: $feedId");
        
        $podcast = $this->podcastIndexService->getPodcastById($feedId);
        Log::info("Podcast data: " . json_encode($podcast));
        
        $episodes = $this->podcastIndexService->getEpisodesByFeedId($feedId, 20);
        Log::info("Raw episodes response: " . json_encode($episodes));
        
        // Handle different episodes response structures
        $episodesList = [];
        if (isset($episodes['items']) && is_array($episodes['items'])) {
            // If episodes are in 'items' field
            Log::info("Episodes found in 'items' field, count: " . count($episodes['items']));
            $episodesList = $episodes['items'];
        } elseif (is_array($episodes)) {
            // If episodes are already an array (direct response)
            Log::info("Episodes are already an array, count: " . count($episodes));
            $episodesList = $episodes;
        } else {
            Log::info("Episodes structure: " . json_encode(array_keys($episodes)));
            $episodesList = [];
        }
        
        $response = [
            'podcast' => $podcast,
            'episodes' => $episodesList,
        ];
        
        Log::info("Final response structure: " . json_encode(array_keys($response)));
        Log::info("Episodes count in response: " . count($episodesList));
        
        return response()->json($response);
    }

    // 3. Featured podcasts (using popular as proxy)
    public function featured()
    {
        $podcasts = $this->podcastIndexService->getPopularPodcasts(10);
        if (isset($podcasts['feeds']) && is_array($podcasts['feeds'])) {
            return response()->json($podcasts['feeds']);
        }
        return response()->json($podcasts);
    }

    // 4. Trending podcasts
    public function trending()
    {
        $podcasts = $this->podcastIndexService->getTrendingPodcasts(1000);
        if (isset($podcasts['feeds']) && is_array($podcasts['feeds'])) {
            return response()->json($podcasts['feeds']);
        }
        return response()->json($podcasts);
    }

    // 5. New episodes
    public function newEpisodes()
    {
        try {
            Log::info('New episodes endpoint called');
            $episodes = $this->podcastIndexService->getRecentEpisodes(1000);
            Log::info('Raw episodes response: ' . json_encode($episodes));
            
            if (isset($episodes['items']) && is_array($episodes['items'])) {
                Log::info('Returning items array with ' . count($episodes['items']) . ' episodes');
                return response()->json($episodes['items']);
            }
            
            Log::info('Returning raw episodes response');
            return response()->json($episodes);
        } catch (\Exception $e) {
            Log::error('Error in newEpisodes endpoint: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch new episodes'], 500);
        }
    }

    // 6. Recommended podcasts (stub, can be improved)
    /**
     * Get recommended podcasts for user
     */
    public function getRecommendedPodcasts()
    {
        try {
            $recommendations = $this->recommendationService->getRecommendationsForUser(Auth::id());
            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search podcasts
     */
    public function searchPodcasts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $results = $this->podcastIndexService->searchPodcasts(
                $request->get('q'),
                $request->get('max', 1000)
            );

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function newPodcasts()
    {
        $podcasts = $this->podcastIndexService->getNewPodcasts(1000);
        if (isset($podcasts['feeds']) && is_array($podcasts['feeds'])) {
            return response()->json($podcasts['feeds']);
        }
        return response()->json($podcasts);
    }

    // 7. Subscribe to podcast
    public function subscribe(Request $request, $feedId)
    {
        $user = $request->user();
        $subscription = PodcastIndexSubscription::firstOrCreate([
            'user_id' => $user->id,
            'feed_id' => $feedId,
        ]);
        return response()->json(['message' => 'Subscribed successfully.', 'subscription' => $subscription]);
    }

    // 8. Unsubscribe from podcast
    public function unsubscribe(Request $request, $feedId)
    {
        $user = $request->user();
        $deleted = PodcastIndexSubscription::where('user_id', $user->id)
            ->where('feed_id', $feedId)
            ->delete();
        return response()->json(['message' => 'Unsubscribed successfully.', 'deleted' => $deleted]);
    }

    // 9. Notifications for new episodes
    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->where('type', PodcastIndexNewEpisodeNotification::class)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Get podcasts for the True Crime category
     */
    public function trueCrime()
    {
        $podcasts = $this->podcastIndexService->getTrueCrimePodcasts(1000);
        if (isset($podcasts['feeds']) && is_array($podcasts['feeds'])) {
            return response()->json($podcasts['feeds']);
        }
        return response()->json($podcasts);
    }

    /**
     * Get podcasts for the Health category
     */
    public function health()
    {
        $podcasts = $this->podcastIndexService->getHealthPodcasts(1000);
        if (isset($podcasts['feeds']) && is_array($podcasts['feeds'])) {
            return response()->json($podcasts['feeds']);
        }
        return response()->json($podcasts);
    }

     /**
     * Get podcasts for the Health category
     */
    public function podcastByCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'catId' => 'required',
            'catName'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $results = $this->podcastIndexService->getSearchPodcastByCategory(
                $request->get('max', 1000),
                $request->get('catId'),
                $request->get('catName'),

            );

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 