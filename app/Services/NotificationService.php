<?php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\PodcastNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function createNewEpisodeNotification(User $user, array $episode, string $showId, string $showName): void
    {
        try {
            PodcastNotification::create([
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
}
