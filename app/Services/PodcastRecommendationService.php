<?php

namespace App\Services;

use App\Models\PodcastSubscription;
use App\Models\UserRecommendation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PodcastRecommendationService
{
    protected $podcastService;

    public function __construct(PodcastIndexService $podcastService)
    {
        $this->podcastService = $podcastService;
    }

    /**
     * Generate recommendations for a user
     */
    public function getRecommendationsForUser(int $userId, int $limit = 10): array
    {
        return Cache::remember("user_recommendations_{$userId}", 3600, function () use ($userId, $limit) {
            $recommendations = [];

            // Get user's subscriptions to understand preferences
            $userSubscriptions = PodcastSubscription::where('user_id', $userId)
                ->with('podcast')
                ->get();

            if ($userSubscriptions->isEmpty()) {
                // For new users, return popular/trending podcasts
                return $this->getPopularRecommendations($limit);
            }

            // Extract categories from user's subscriptions
            $userCategories = $this->extractUserCategories($userSubscriptions);

            // Get category-based recommendations
            $categoryRecommendations = $this->getCategoryBasedRecommendations($userId, $userCategories, $limit);
            $recommendations = array_merge($recommendations, $categoryRecommendations);

            // Get collaborative filtering recommendations
            $collaborativeRecommendations = $this->getCollaborativeRecommendations($userId, $limit);
            $recommendations = array_merge($recommendations, $collaborativeRecommendations);

            // Remove duplicates and podcasts user is already subscribed to
            $recommendations = $this->filterRecommendations($recommendations, $userSubscriptions, $limit);

            return $recommendations;
        });
    }

    /**
     * Get popular recommendations for new users
     */
    private function getPopularRecommendations(int $limit): array
    {
        try {
            $popular = $this->podcastService->getPopularPodcasts($limit);
            
            return array_map(function ($podcast) {
                return [
                    'feed_id' => $podcast['id'],
                    'title' => $podcast['title'],
                    'description' => $podcast['description'],
                    'image' => $podcast['image'],
                    'author' => $podcast['author'],
                    'categories' => $podcast['categories'] ?? [],
                    'recommendation_score' => 0.8,
                    'reason' => 'Popular podcast'
                ];
            }, $popular['feeds'] ?? []);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Extract categories from user subscriptions
     */
    private function extractUserCategories(Collection $subscriptions): array
    {
        $categories = [];
        
        foreach ($subscriptions as $subscription) {
            if ($subscription->podcast && $subscription->podcast->categories) {
                foreach ($subscription->podcast->categories as $category) {
                    $categories[$category] = ($categories[$category] ?? 0) + 1;
                }
            }
        }

        // Sort by frequency and return top categories
        arsort($categories);
        return array_keys(array_slice($categories, 0, 5));
    }

    /**
     * Get category-based recommendations
     */
    private function getCategoryBasedRecommendations(int $userId, array $categories, int $limit): array
    {
        $recommendations = [];
        
        foreach ($categories as $category) {
            try {
                $categoryPodcasts = $this->podcastService->getPodcastsByCategory($category, 5,'');
                
                foreach ($categoryPodcasts['feeds'] ?? [] as $podcast) {
                    $recommendations[] = [
                        'feed_id' => $podcast['id'],
                        'title' => $podcast['title'],
                        'description' => $podcast['description'],
                        'image' => $podcast['image'],
                        'author' => $podcast['author'],
                        'categories' => $podcast['categories'] ?? [],
                        'recommendation_score' => 0.7,
                        'reason' => "Based on your interest in {$category}"
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $recommendations;
    }

    /**
     * Get collaborative filtering recommendations
     */
    private function getCollaborativeRecommendations(int $userId, int $limit): array
    {
        // Find users with similar subscriptions
        $userSubscriptionIds = PodcastSubscription::where('user_id', $userId)
            ->pluck('feed_id')
            ->toArray();

        if (empty($userSubscriptionIds)) {
            return [];
        }

        // Find users who have subscribed to similar podcasts
        $similarUsers = PodcastSubscription::whereIn('feed_id', $userSubscriptionIds)
            ->where('user_id', '!=', $userId)
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= ?', [min(2, count($userSubscriptionIds) / 2)])
            ->pluck('user_id')
            ->take(10);

        $recommendations = [];

        foreach ($similarUsers as $similarUserId) {
            $otherUserSubscriptions = PodcastSubscription::where('user_id', $similarUserId)
                ->whereNotIn('feed_id', $userSubscriptionIds)
                ->take(3)
                ->get();

            foreach ($otherUserSubscriptions as $subscription) {
                $recommendations[] = [
                    'feed_id' => $subscription->feed_id,
                    'title' => $subscription->podcast_title,
                    'image' => $subscription->podcast_image,
                    'recommendation_score' => 0.6,
                    'reason' => 'Users with similar interests also listen to this'
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Filter recommendations to remove duplicates and subscribed podcasts
     */
    private function filterRecommendations(array $recommendations, Collection $userSubscriptions, int $limit): array
    {
        $subscribedFeedIds = $userSubscriptions->pluck('feed_id')->toArray();
        $seen = [];
        $filtered = [];

        foreach ($recommendations as $recommendation) {
            $feedId = $recommendation['feed_id'];
            
            // Skip if already subscribed or already in recommendations
            if (in_array($feedId, $subscribedFeedIds) || isset($seen[$feedId])) {
                continue;
            }

            $seen[$feedId] = true;
            $filtered[] = $recommendation;

            if (count($filtered) >= $limit) {
                break;
            }
        }

        // Sort by recommendation score
        usort($filtered, function ($a, $b) {
            return $b['recommendation_score'] <=> $a['recommendation_score'];
        });

        return $filtered;
    }

    /**
     * Update recommendations for a user
     */
    public function updateUserRecommendations(int $userId): void
    {
        // Clear cache
        Cache::forget("user_recommendations_{$userId}");

        // Get fresh recommendations
        $recommendations = $this->getRecommendationsForUser($userId, 20);

        // Delete old recommendations
        UserRecommendation::where('user_id', $userId)->delete();

        // Store new recommendations
        foreach ($recommendations as $recommendation) {
            UserRecommendation::create([
                'user_id' => $userId,
                'feed_id' => $recommendation['feed_id'],
                'podcast_title' => $recommendation['title'],
                'podcast_image' => $recommendation['image'] ?? null,
                'recommendation_score' => $recommendation['recommendation_score'],
                'reason' => $recommendation['reason'],
                'is_dismissed' => false
            ]);
        }
    }

    /**
     * Dismiss a recommendation
     */
    public function dismissRecommendation(int $userId, int $feedId): bool
    {
        $recommendation = UserRecommendation::where('user_id', $userId)
            ->where('feed_id', $feedId)
            ->first();

        if ($recommendation) {
            $recommendation->update(['is_dismissed' => true]);
            return true;
        }

        return false;
    }

    /**
     * Get trending podcasts in user's preferred categories
     */
    public function getTrendingInUserCategories(int $userId, int $limit = 10): array
    {
        $userSubscriptions = PodcastSubscription::where('user_id', $userId)
            ->with('podcast')
            ->get();

        if ($userSubscriptions->isEmpty()) {
            return $this->podcastService->getTrendingPodcasts($limit);
        }

        $userCategories = $this->extractUserCategories($userSubscriptions);
        $trending = [];

        foreach ($userCategories as $category) {
            try {
                $categoryTrending = $this->podcastService->getTrendingPodcasts(5, null, $category);
                if (isset($categoryTrending['feeds'])) {
                    $trending = array_merge($trending, $categoryTrending['feeds']);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Remove duplicates and limit results
        $seen = [];
        $filtered = [];
        foreach ($trending as $podcast) {
            if (!isset($seen[$podcast['id']])) {
                $seen[$podcast['id']] = true;
                $filtered[] = $podcast;
                if (count($filtered) >= $limit) {
                    break;
                }
            }
        }

        return ['feeds' => $filtered];
    }
}