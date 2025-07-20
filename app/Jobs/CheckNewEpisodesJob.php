<?php
// app/Jobs/CheckNewEpisodesJob.php
namespace App\Jobs;

use App\Models\Subscription;
use App\Models\Notification;
use App\Services\PodcastIndexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckNewEpisodesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(PodcastIndexService $podcastIndexService): void
    {
        try {
            $subscriptions = Subscription::query()
                ->select('podcastindex_podcast_id')
                ->distinct()
                ->get();

            foreach ($subscriptions as $subscription) {
                $this->checkForNewEpisodes($subscription->podcastindex_podcast_id, $podcastIndexService);
            }
        } catch (\Exception $e) {
            Log::error('Error checking for new episodes: ' . $e->getMessage());
        }
    }

    private function checkForNewEpisodes(string $podcastindex_podcast_id, PodcastIndexService $podcastIndexService): void
    {
        try {
            $episodes = $podcastIndexService->getEpisodesByFeedId($podcastindex_podcast_id, 5);
            if (!isset($episodes['items'])) {
                return;
            }
            $latestEpisode = $episodes['items'][0] ?? null;
            if (!$latestEpisode) {
                return;
            }
            $releaseDate = $latestEpisode['datePublished'] ?? $latestEpisode['datePublishedPretty'] ?? null;
            $oneDayAgo = now()->subDay();
            if ($releaseDate && strtotime($releaseDate) > $oneDayAgo->timestamp) {
                $this->createNewEpisodeNotifications($podcastindex_podcast_id, $latestEpisode);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to check episodes for podcast {$podcastindex_podcast_id}: " . $e->getMessage());
        }
    }

    private function createNewEpisodeNotifications(string $podcastindex_podcast_id, array $episode): void
    {
        $userIds = Subscription::where('podcastindex_podcast_id', $podcastindex_podcast_id)
            ->pluck('user_id');
        foreach ($userIds as $userId) {
            $exists = Notification::where('user_id', $userId)
                ->where('podcastindex_episode_id', $episode['id'])
                ->where('type', 'new_episode')
                ->exists();
            if (!$exists) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'new_episode',
                    'title' => 'New Episode Available',
                    'message' => "A new episode '{$episode['title']}' is available!",
                    'data' => [
                        'episode' => $episode,
                        'podcastindex_podcast_id' => $podcastindex_podcast_id
                    ],
                    'podcastindex_podcast_id' => $podcastindex_podcast_id,
                    'podcastindex_episode_id' => $episode['id'],
                    'is_read' => false
                ]);
            }
        }
    }
}
