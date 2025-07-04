<?php
// app/Jobs/CheckNewEpisodesJob.php
namespace App\Jobs;

use App\Models\PodcastSubscription;
use App\Models\PodcastNotification;
use App\Services\SpotifyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckNewEpisodesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SpotifyService $spotifyService): void
    {
        try {
            $subscriptions = PodcastSubscription::where('is_active', true)
                ->where('notify_new_episodes', true)
                ->groupBy('show_id')
                ->get();

            foreach ($subscriptions as $subscription) {
                $this->checkForNewEpisodes($subscription, $spotifyService);
            }
        } catch (\Exception $e) {
            Log::error('Error checking for new episodes: ' . $e->getMessage());
        }
    }

    private function checkForNewEpisodes(PodcastSubscription $subscription, SpotifyService $spotifyService): void
    {
        try {
            $episodes = $spotifyService->getShowEpisodes($subscription->show_id, 5, 0);
            
            if (!isset($episodes['items'])) {
                return;
            }

            $latestEpisode = $episodes['items'][0] ?? null;
            if (!$latestEpisode) {
                return;
            }

            $releaseDate = $latestEpisode['release_date'];
            $oneDayAgo = now()->subDay();

            // Check if episode was released in the last 24 hours
            if (strtotime($releaseDate) > $oneDayAgo->timestamp) {
                $this->createNewEpisodeNotifications($subscription, $latestEpisode);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to check episodes for show {$subscription->show_id}: " . $e->getMessage());
        }
    }

    private function createNewEpisodeNotifications(PodcastSubscription $subscription, array $episode): void
    {
        $userIds = PodcastSubscription::where('show_id', $subscription->show_id)
            ->where('is_active', true)
            ->where('notify_new_episodes', true)
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            // Check if notification already exists
            $exists = PodcastNotification::where('user_id', $userId)
                ->where('episode_id', $episode['id'])
                ->where('type', 'new_episode')
                ->exists();

            if (!$exists) {
                PodcastNotification::create([
                    'user_id' => $userId,
                    'type' => 'new_episode',
                    'title' => 'New Episode Available',
                    'message' => "New episode '{$episode['name']}' from {$subscription->show_name}",
                    'data' => [
                        'episode' => $episode,
                        'show_id' => $subscription->show_id
                    ],
                    'show_id' => $subscription->show_id,
                    'episode_id' => $episode['id'],
                    'is_read' => false
                ]);
            }
        }
    }
}
