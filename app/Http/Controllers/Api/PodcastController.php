<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PodcastIndexService;
use App\Services\PodcastRecommendationService;
use App\Models\Podcast;
use App\Models\Episode;
use App\Models\PodcastSubscription;
use App\Models\EpisodeNotification;
use App\Models\UserRecommendation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PodcastController extends Controller
{
    protected $podcastService;
    protected $recommendationService;

    public function __construct(PodcastIndexService $podcastService, PodcastRecommendationService $recommendationService)
    {
        $this->podcastService = $podcastService;
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get all podcast categories
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = $this->podcastService->getCategories();
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search podcasts
     */
    public function searchPodcasts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
            'max' => 'integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $results = $this->podcastService->searchPodcasts(
                $request->get('q'),
                $request->get('max', 10)
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

    /**
     * Get podcast details with episodes
     */
    public function getPodcastDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feed_id' => 'required_without:feed_url|integer',
            'feed_url' => 'required_without:feed_id|url',
            'episodes_max' => 'integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get podcast details
            if ($request->has('feed_id')) {
                $podcast = $this->podcastService->getPodcastById($request->get('feed_id'));
                $feedId = $request->get('feed_id');
            } else {
                $podcast = $this->podcastService->getPodcastByUrl($request->get('feed_url'));
                $feedId = $podcast['feed']['id'] ?? null;
            }

            // Get episodes if feed_id is available
            $episodes = [];
            if ($feedId) {
                $episodesResponse = $this->podcastService->getEpisodesByFeedId(
                    $feedId,
                    $request->get('episodes_max', 20)
                );
                $episodes = $episodesResponse['items'] ?? [];
            }

            // Check if user is subscribed (if authenticated)
            $isSubscribed = false;
            if (Auth::check() && $feedId) {
                $isSubscribed = PodcastSubscription::where('user_id', Auth::id())
                    ->where('feed_id', $feedId)
                    ->exists();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'podcast' => $podcast,
                    'episodes' => $episodes,
                    'is_subscribed' => $isSubscribed
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch podcast details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trending podcasts
     */
    public function getTrendingPodcasts(Request $request): JsonResponse
    {
        try {
            $trending = $this->podcastService->getTrendingPodcasts(
                $request->get('max', 20),
                $request->get('since'),
                $request->get('category')
            );

            return response()->json([
                'success' => true,
                'data' => $trending
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trending podcasts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured podcasts
     */
    public function getFeaturedPodcasts(Request $request): JsonResponse
    {
        try {
            // Get popular podcasts as featured
            $featured = $this->podcastService->getPopularPodcasts(
                $request->get('max', 10)
            );

            return response()->json([
                'success' => true,
                'data' => $featured
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured podcasts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get new podcasts
     */
    public function getNewPodcasts(Request $request): JsonResponse
    {
        try {
            $newPodcasts = $this->podcastService->getNewPodcasts(
                $request->get('max', 20),
                $request->get('since')
            );

            return response()->json([
                'success' => true,
                'data' => $newPodcasts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch new podcasts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get new episodes
     */
    public function getNewEpisodes(Request $request): JsonResponse
    {
        try {
            $newEpisodes = $this->podcastService->getRecentEpisodes(
                $request->get('max', 20),
                $request->get('exclude_string', [])
            );

            return response()->json([
                'success' => true,
                'data' => $newEpisodes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch new episodes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subscribe to podcast
     */
    public function subscribeToPodcast(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feed_id' => 'required|integer',
            'podcast_title' => 'required|string',
            'podcast_image' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $feedId = $request->get('feed_id');

            // Check if already subscribed
            $existingSubscription = PodcastSubscription::where('user_id', $userId)
                ->where('feed_id', $feedId)
                ->first();

            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already subscribed to this podcast'
                ], 409);
            }

            // Create subscription
            $subscription = PodcastSubscription::create([
                'user_id' => $userId,
                'feed_id' => $feedId,
                'podcast_title' => $request->get('podcast_title'),
                'podcast_image' => $request->get('podcast_image'),
                'subscribed_at' => now(),
                'notification_enabled' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to podcast',
                'data' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe to podcast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unsubscribe from podcast
     */
    public function unsubscribeFromPodcast(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feed_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $feedId = $request->get('feed_id');

            $subscription = PodcastSubscription::where('user_id', $userId)
                ->where('feed_id', $feedId)
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found'
                ], 404);
            }

            $subscription->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unsubscribed from podcast'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unsubscribe from podcast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user subscriptions
     */
    public function getUserSubscriptions(): JsonResponse
    {
        try {
            $subscriptions = PodcastSubscription::where('user_id', Auth::id())
                ->orderBy('subscribed_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subscriptions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notifications for new episodes
     */
    public function getEpisodeNotifications(): JsonResponse
    {
        try {
            $notifications = EpisodeNotification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $notification = EpisodeNotification::where('id', $request->get('notification_id'))
                ->where('user_id', Auth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recommended podcasts for user
     */
    public function getRecommendedPodcasts(): JsonResponse
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
     * Get podcasts by category
     */
    public function getPodcastsByCategory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string',
            'max' => 'integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $podcasts = $this->podcastService->getPodcastsByCategory(
                $request->get('category'),
                $request->get('max', 20),
                ''
            );

            return response()->json([
                'success' => true,
                'data' => $podcasts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch podcasts by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle notification settings for subscription
     */
    public function toggleNotifications(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'feed_id' => 'required|integer',
            'enabled' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscription = PodcastSubscription::where('user_id', Auth::id())
                ->where('feed_id', $request->get('feed_id'))
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found'
                ], 404);
            }

            $subscription->update(['notification_enabled' => $request->get('enabled')]);

            return response()->json([
                'success' => true,
                'message' => 'Notification settings updated',
                'data' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get episode details
     */
    public function getEpisodeDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string',
            'feed_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $episode = $this->podcastService->getEpisodeByGuid(
                $request->get('guid'),
                $request->get('feed_id')
            );

            return response()->json([
                'success' => true,
                'data' => $episode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch episode details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data
     */
    public function getDashboard(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            // Get user subscriptions count
            $subscriptionCount = PodcastSubscription::where('user_id', $userId)->count();
            
            // Get unread notifications count
            $unreadNotifications = EpisodeNotification::where('user_id', $userId)
                ->where('is_read', false)
                ->count();
            
            // Get recent episodes from subscribed podcasts
            $subscribedFeedIds = PodcastSubscription::where('user_id', $userId)
                ->pluck('feed_id')
                ->toArray();
            
            $recentEpisodes = [];
            if (!empty($subscribedFeedIds)) {
                // Get recent episodes for each subscribed podcast
                foreach (array_slice($subscribedFeedIds, 0, 5) as $feedId) {
                    try {
                        $episodes = $this->podcastService->getEpisodesByFeedId($feedId, 3);
                        if (isset($episodes['items'])) {
                            $recentEpisodes = array_merge($recentEpisodes, $episodes['items']);
                        }
                    } catch (\Exception $e) {
                        // Continue if one feed fails
                        continue;
                    }
                }
            }
            
            // Sort by pub date and limit
            usort($recentEpisodes, function($a, $b) {
                return strtotime($b['datePublished']) - strtotime($a['datePublished']);
            });
            $recentEpisodes = array_slice($recentEpisodes, 0, 10);
            
            // Get recommendations
            $recommendations = $this->recommendationService->getRecommendationsForUser($userId, 5);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'subscription_count' => $subscriptionCount,
                    'unread_notifications' => $unreadNotifications,
                    'recent_episodes' => $recentEpisodes,
                    'recommendations' => $recommendations
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}