<?php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\PodcastNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewEpisodeNotificationMail;
use Google\Auth\OAuth2;

class NotificationService
{
    public function createNewEpisodeNotification(User $user, array $episode, string $showId, string $showName): void
    {
        try {
            $notification = PodcastNotification::create([
                'user_id' => $user->id,
                'type' => 'new_episode',
                'title' => 'New Episode Available',
                'message' => "New episode '{$episode['name']}' from {$showName}",
                'data' => [
                    'episode' => $episode,
                    'show_id' => $showId,
                    'show_name' => $showName
                ],
                'show_id' => $showId,
                'episode_id' => $episode['id'],
                'is_read' => false
            ]);
            if ($user->fcm_token) {
                $this->sendFcmPush($user->fcm_token, $notification->title, $notification->message, [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'show_id' => $showId,
                    'episode_id' => $episode['id'],
                ]);
            }
            // Send email notification
            if ($user->email) {
                try {
                    Mail::to($user->email)->queue(new NewEpisodeNotificationMail($user, $episode, $showName));
                } catch (\Exception $e) {
                    Log::error('Failed to send new episode email: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to create new episode notification: " . $e->getMessage());
        }
    }

    public function createFeaturedPodcastNotification(User $user, array $podcast): void
    {
        try {
            PodcastNotification::create([
                'user_id' => $user->id,
                'type' => 'featured',
                'title' => 'Featured Podcast',
                'message' => "Check out the featured podcast: {$podcast['name']}",
                'data' => [
                    'podcast' => $podcast
                ],
                'show_id' => $podcast['id'],
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create featured podcast notification: " . $e->getMessage());
        }
    }

    public function createRecommendedPodcastNotification(User $user, array $podcast): void
    {
        try {
            PodcastNotification::create([
                'user_id' => $user->id,
                'type' => 'recommended',
                'title' => 'Recommended for You',
                'message' => "We think you'll like: {$podcast['name']}",
                'data' => [
                    'podcast' => $podcast
                ],
                'show_id' => $podcast['id'],
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create recommended podcast notification: " . $e->getMessage());
        }
    }

    public function markAllAsRead(User $user): int
    {
        return PodcastNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function getUnreadCount(User $user): int
    {
        return PodcastNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    private function sendFcmPush(string $token, string $title, string $body, array $data = []): void
    {
        $serviceAccountPath = config('services.fcm.service_account_path');
        $projectId = config('services.fcm.project_id');
        if (!file_exists(base_path($serviceAccountPath))) {
            Log::error('FCM service account file not found.');
            return;
        }
        $serviceAccount = json_decode(file_get_contents(base_path($serviceAccountPath)), true);
        // Get OAuth2 access token
        $client = new OAuth2([
            'audience' => 'https://oauth2.googleapis.com/token',
            'issuer' => $serviceAccount['client_email'],
            'signingAlgorithm' => 'RS256',
            'signingKey' => $serviceAccount['private_key'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ]);
        $accessToken = $client->fetchAuthToken()['access_token'] ?? null;
        if (!$accessToken) {
            Log::error('Failed to get FCM OAuth2 access token.');
            return;
        }
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ],
        ];
        $response = Http::withToken($accessToken)
            ->post($url, $payload);
        if ($response->failed()) {
            Log::error('Failed to send FCM v1 push: ' . $response->body());
        }
    }
}
