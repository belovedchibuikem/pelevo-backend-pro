<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\PodcastIndexController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProfileController;


// Authentication routes (these typically don't require auth:sanctum middleware initially as they issue the token)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('auth/google/redirect', [AuthController::class, 'googleRedirect']);
Route::get('auth/google/callback', [AuthController::class, 'googleCallback']);
Route::post('auth/app-signin', [AuthController::class, 'appSignIn']);

// Password Reset Routes
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
//Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Email Verification Routes (moved outside auth middleware)
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware(['signed']);

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->name('verification.resend')
    ->middleware(['auth:sanctum', 'throttle:6,1']);

// Authenticated API routes


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Podcast routes
    Route::prefix('podcasts')->group(function () {
        Route::get('/categories', [PodcastIndexController::class, 'categories']);
        Route::get('/category/search', [PodcastIndexController::class, 'podcastByCategory']);
        Route::get('/new-episodes', [PodcastIndexController::class, 'newEpisodes']);
        Route::get('/by/{feedId}', [PodcastIndexController::class, 'show']);
        Route::get('/search', [PodcastIndexController::class, 'searchPodcasts']);
        Route::get('/trending', [PodcastIndexController::class, 'trending']);
        Route::post('/{feedId}/subscribe', [PodcastIndexController::class, 'subscribe']);
        Route::post('/{feedId}/unsubscribe', [PodcastIndexController::class, 'unsubscribe']);
        Route::get('/notifications', [PodcastIndexController::class, 'notifications']);
        Route::get('/featured', [PodcastIndexController::class, 'featured']);
        Route::get('/new-podcast', [PodcastIndexController::class, 'newPodcasts']);
        Route::get('/recommended', [PodcastIndexController::class, 'getRecommendedPodcasts']);
        Route::get('/true-crime', [PodcastIndexController::class, 'trueCrime']);
        Route::get('/health', [PodcastIndexController::class, 'health']);
    });

    // Earnings
    Route::post('episodes/{episode}/listen', [EarningController::class, 'recordListening']);
    Route::get('earnings/total', [EarningController::class, 'getTotalEarnings']);
    Route::get('earnings/by-date', [EarningController::class, 'getEarningsByDate']);
    Route::get('earnings/by-podcast/{podcastId}', [EarningController::class, 'getEarningsByPodcast']);
    Route::get('listening-history', [EarningController::class, 'getListeningHistory']);
    Route::apiResource('earnings', EarningController::class)->only(['index', 'show']);

    // Withdrawals
    Route::post('withdraw', [WithdrawalController::class, 'withdraw']);
    Route::get('withdrawals', [WithdrawalController::class, 'index']);
    Route::get('withdrawals/{withdrawal}/status', [WithdrawalController::class, 'status']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Admin Dashboard
    Route::prefix('admin')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index']);
        Route::get('payouts', [AdminDashboardController::class, 'payouts']);
        Route::get('blocked-ips', [AdminDashboardController::class, 'blockedIps']);
        Route::get('users-by-country', [AdminDashboardController::class, 'usersByCountry']);
    });

    Route::get('profile', [ProfileController::class, 'show']);
    Route::patch('profile', [ProfileController::class, 'update']);
    Route::delete('profile', [ProfileController::class, 'destroy']);
    Route::post('profile/fcm-token', [ProfileController::class, 'updateFcmToken']);

    // Library routes
    Route::prefix('library')->group(function () {
        // Downloads
        Route::apiResource('downloads', \App\Http\Controllers\Api\DownloadsController::class);
        Route::post('downloads/batch-destroy', [\App\Http\Controllers\Api\DownloadsController::class, 'batchDestroy']);
        Route::delete('downloads/clear-all', [\App\Http\Controllers\Api\DownloadsController::class, 'clearAll']);

        // Subscriptions
        Route::apiResource('subscriptions', \App\Http\Controllers\Api\SubscriptionsController::class);
        Route::post('subscriptions/subscribe', [\App\Http\Controllers\Api\SubscriptionsController::class, 'subscribe']);
        Route::post('subscriptions/unsubscribe', [\App\Http\Controllers\Api\SubscriptionsController::class, 'unsubscribe']);
        Route::post('subscriptions/batch-destroy', [\App\Http\Controllers\Api\SubscriptionsController::class, 'batchDestroy']);

        // Play History
        Route::apiResource('play-history', \App\Http\Controllers\Api\PlayHistoryController::class);
        Route::post('play-history/batch-destroy', [\App\Http\Controllers\Api\PlayHistoryController::class, 'batchDestroy']);
        Route::delete('play-history/clear-all', [\App\Http\Controllers\Api\PlayHistoryController::class, 'clearAll']);
        Route::get('play-history/recent', [\App\Http\Controllers\Api\PlayHistoryController::class, 'recent']);

        // Playlists
        Route::apiResource('playlists', \App\Http\Controllers\Api\PlaylistsController::class);
        Route::post('playlists/{playlist}/add-episode', [\App\Http\Controllers\Api\PlaylistsController::class, 'addEpisode']);
        Route::delete('playlists/{playlist}/remove-episode', [\App\Http\Controllers\Api\PlaylistsController::class, 'removeEpisode']);
        Route::post('playlists/{playlist}/reorder', [\App\Http\Controllers\Api\PlaylistsController::class, 'reorder']);
        Route::post('playlists/batch-destroy', [\App\Http\Controllers\Api\PlaylistsController::class, 'batchDestroy']);

        // Playlist Items
        Route::apiResource('playlist-items', \App\Http\Controllers\Api\PlaylistItemsController::class);
        Route::post('playlist-items/batch-destroy', [\App\Http\Controllers\Api\PlaylistItemsController::class, 'batchDestroy']);
    });
});

// Social Authentication Routes
Route::prefix('auth')->group(function () {
    // Google
    Route::get('google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    // Apple
    Route::get('apple', [SocialAuthController::class, 'redirectToApple']);
    Route::get('apple/callback', [SocialAuthController::class, 'handleAppleCallback']);

    // Spotify
    Route::get('spotify', [SocialAuthController::class, 'redirectToSpotify']);
    Route::get('spotify/callback', [SocialAuthController::class, 'handleSpotifyCallback']);
});

// Mail Test Route
Route::get('/test-mail', function () {
    try {
        $mailConfig = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];

        // Test sending a simple email
        \Illuminate\Support\Facades\Mail::raw('Test email from Laravel', function($message) {
            $message->to('johnchibuikem20@gmail.com')
                   ->subject('Test Email');
        });

        return response()->json([
            'message' => 'Mail configuration test completed',
            'config' => $mailConfig,
            'status' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Mail configuration test failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'config' => $mailConfig ?? [],
            'status' => 'error'
        ], 500);
    }
});

// Queue Test Route
Route::get('/test-queue', function () {
    try {
        $queueConfig = [
            'QUEUE_CONNECTION' => config('queue.default'),
            'QUEUE_DRIVER' => config('queue.default'),
            'QUEUE_NAME' => config('queue.connections.sync.queue'),
        ];

        // Test queue dispatch
        \Illuminate\Support\Facades\Queue::push(function() {
            \Illuminate\Support\Facades\Log::info('Test queue job executed');
        });

        return response()->json([
            'message' => 'Queue configuration test completed',
            'config' => $queueConfig,
            'status' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Queue configuration test failed',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'config' => $queueConfig ?? [],
            'status' => 'error'
        ], 500);
    }
});

// Test endpoint for new episodes (temporary, remove after debugging)
Route::get('/test/new-episodes', [PodcastIndexController::class, 'newEpisodes']);

// Test endpoint for podcast details (temporary, remove after debugging)
Route::get('/test/podcast-details/{feedId}', [PodcastIndexController::class, 'show']);

// Public test endpoint for podcast details (no auth required)
Route::get('/public/test/podcast-details/{feedId}', function($feedId) {
    try {
        $controller = app(\App\Http\Controllers\Api\PodcastIndexController::class);
        return $controller->show($feedId);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Test failed',
            'message' => $e->getMessage(),
            'feedId' => $feedId
        ], 500);
    }
});

