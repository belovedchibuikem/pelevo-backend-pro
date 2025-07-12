<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the user's subscriptions.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with(['podcast'])
            ->active()
            ->orderBy('subscribed_at', 'desc')
            ->paginate(20);

        return SubscriptionResource::collection($subscriptions);
    }

    /**
     * Store a newly created subscription.
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['subscribed_at'] = now();
        $data['is_active'] = true;

        $subscription = $request->user()->subscriptions()->create($data);

        return response()->json([
            'message' => 'Subscribed successfully',
            'data' => new SubscriptionResource($subscription->load(['podcast']))
        ], 201);
    }

    /**
     * Display the specified subscription.
     */
    public function show(Request $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'data' => new SubscriptionResource($subscription->load(['podcast']))
        ]);
    }

    /**
     * Update the specified subscription.
     */
    public function update(SubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subscription->update($request->validated());

        return response()->json([
            'message' => 'Subscription updated successfully',
            'data' => new SubscriptionResource($subscription->load(['podcast']))
        ]);
    }

    /**
     * Remove the specified subscription (unsubscribe).
     */
    public function destroy(Request $request, Subscription $subscription): JsonResponse
    {
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subscription->update([
            'is_active' => false,
            'unsubscribed_at' => now()
        ]);

        return response()->json(['message' => 'Unsubscribed successfully']);
    }

    /**
     * Subscribe to a podcast.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'podcast_id' => 'required|exists:podcasts,id'
        ]);

        $subscription = $request->user()->subscriptions()->updateOrCreate(
            ['podcast_id' => $request->podcast_id],
            [
                'subscribed_at' => now(),
                'is_active' => true,
                'unsubscribed_at' => null
            ]
        );

        return response()->json([
            'message' => 'Subscribed successfully',
            'data' => new SubscriptionResource($subscription->load(['podcast']))
        ]);
    }

    /**
     * Unsubscribe from a podcast.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'podcast_id' => 'required|exists:podcasts,id'
        ]);

        $subscription = $request->user()
            ->subscriptions()
            ->where('podcast_id', $request->podcast_id)
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $subscription->update([
            'is_active' => false,
            'unsubscribed_at' => now()
        ]);

        return response()->json(['message' => 'Unsubscribed successfully']);
    }

    /**
     * Remove multiple subscriptions.
     */
    public function batchDestroy(Request $request): JsonResponse
    {
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
    }
}
