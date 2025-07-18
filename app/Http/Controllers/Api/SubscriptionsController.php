<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Podcast;
use App\Services\PodcastIndexService;

class SubscriptionsController extends Controller
{
    /**
     * Helper for consistent error responses.
     */
    protected function errorResponse($message, $status = 400, $errors = null)
    {
        $response = ['message' => $message];
        if ($errors) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status);
    }

    /**
     * Display a listing of the user's subscriptions.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $subscriptions = $request->user()
                ->subscriptions()
                ->with(['podcast'])
                ->active()
                ->orderBy('subscribed_at', 'desc')
                ->paginate(20);

            return SubscriptionResource::collection($subscriptions);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch subscriptions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created subscription.
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['subscribed_at'] = now();
            $data['is_active'] = true;

            $subscription = $request->user()->subscriptions()->create($data);

            return response()->json([
                'message' => 'Subscribed successfully',
                'data' => new SubscriptionResource($subscription->load(['podcast']))
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to subscribe: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified subscription.
     */
    public function show(Request $request, Subscription $subscription): JsonResponse
    {
        try {
            if ($subscription->user_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            return response()->json([
                'data' => new SubscriptionResource($subscription->load(['podcast']))
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch subscription: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified subscription.
     */
    public function update(SubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        try {
            if ($subscription->user_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $subscription->update($request->validated());

            return response()->json([
                'message' => 'Subscription updated successfully',
                'data' => new SubscriptionResource($subscription->load(['podcast']))
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update subscription: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified subscription (unsubscribe).
     */
    public function destroy(Request $request, Subscription $subscription): JsonResponse
    {
        try {
            if ($subscription->user_id !== $request->user()->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $subscription->update([
                'is_active' => false,
                'unsubscribed_at' => now()
            ]);

            return response()->json(['message' => 'Unsubscribed successfully']);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to unsubscribe: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Subscribe to a podcast.
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'podcastindex_podcast_id' => 'required|string'
            ]);

            // 1. Check if podcast exists locally
            $podcast = Podcast::where('id', $request->podcastindex_podcast_id)->first();

            // 2. If not, fetch from PodcastIndex and store locally
            if (!$podcast) {
                $podcastData = PodcastIndexService::fetchPodcast($request->podcastindex_podcast_id);

                // Adjust these fields to match your Podcast model
                $podcast = Podcast::create([
                    'id' => $podcastData['id'],
                    'title' => $podcastData['title'] ?? 'Untitled',
                    'url' => $podcastData['url'] ?? null,
                    'original_url' => $podcastData['originalUrl'] ?? null,
                    'link' => $podcastData['link'] ?? null,
                    'description' => $podcastData['description'] ?? '',
                    'author' => $podcastData['author'] ?? '',
                    'owner_name' => $podcastData['ownerName'] ?? null,
                    'image' => $podcastData['image'] ?? null,
                    'artwork' => $podcastData['artwork'] ?? null,
                    'last_update_time' => $podcastData['lastUpdateTime'] ?? null,
                    'last_crawl_time' => $podcastData['lastCrawlTime'] ?? null,
                    'last_parse_time' => $podcastData['lastParseTime'] ?? null,
                    'in_polling_queue' => $podcastData['inPollingQueue'] ?? false,
                    'priority' => $podcastData['priority'] ?? null,
                    'last_good_http_status_time' => $podcastData['lastGoodHttpStatusTime'] ?? null,
                    'last_http_status' => $podcastData['lastHttpStatus'] ?? null,
                    'content_type' => $podcastData['contentType'] ?? null,
                    'itunes_id' => $podcastData['itunesId'] ?? null,
                    'generator' => $podcastData['generator'] ?? null,
                    'language' => $podcastData['language'] ?? null,
                    'type' => $podcastData['type'] ?? null,
                    'dead' => $podcastData['dead'] ?? false,
                    'crawl_errors' => $podcastData['crawlErrors'] ?? 0,
                    'parse_errors' => $podcastData['parseErrors'] ?? 0,
                    'categories' => $podcastData['categories'] ?? [],
                    'locked' => $podcastData['locked'] ?? false,
                    'explicit' => $podcastData['explicit'] ?? false,
                    'podcast_guid' => $podcastData['podcastGuid'] ?? null,
                    'medium' => $podcastData['medium'] ?? null,
                    'episode_count' => $podcastData['episodeCount'] ?? 0,
                    'image_url_hash' => $podcastData['imageUrlHash'] ?? null,
                    'newest_item_pubdate' => $podcastData['newestItemPubdate'] ?? null,
                ]);
            }

            // 3. Create or update the subscription
            $subscription = $request->user()->subscriptions()->updateOrCreate(
                ['podcast_id' => $podcastData['id']],
                ['is_active' => true],
                ['subscribed_at' => now()]
            );

            return response()->json([
                'message' => 'Subscribed successfully',
                'data' => new SubscriptionResource($subscription->load(['podcast']))
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to subscribe: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Unsubscribe from a podcast.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'podcastindex_podcast_id' => 'required|string'
            ]);

            $subscription = $request->user()
                ->subscriptions()
                ->where('podcastindex_podcast_id', $request->podcastindex_podcast_id)
                ->first();

            if (!$subscription) {
                return $this->errorResponse('Subscription not found', 404);
            }

            $subscription->delete();

            return response()->json(['message' => 'Unsubscribed successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to unsubscribe: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove multiple subscriptions.
     */
    public function batchDestroy(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'subscription_ids' => 'required|array',
                'subscription_ids.*' => 'exists:subscriptions,id'
            ]);

            $updatedCount = $request->user()
                ->subscriptions()
                ->whereIn('id', $request->subscription_ids)
                ->update([
                    'is_active' => false,
                    'unsubscribed_at' => now()
                ]);

            return response()->json([
                'message' => "Successfully unsubscribed from {$updatedCount} podcasts"
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to batch unsubscribe: ' . $e->getMessage(), 500);
        }
    }
}
