<?php

namespace App\Console\Commands;

use App\Models\PodcastIndexSubscription;
use App\Services\PodcastIndexService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PodcastIndexNewEpisodeNotification;
use Illuminate\Support\Facades\Cache;

class PodcastIndexCheckNewEpisodes extends Command
{
    protected $signature = 'podcastindex:check-new-episodes';
    protected $description = 'Check for new PodcastIndex episodes and send notifications to subscribers';

    protected $podcastIndexService;

    public function __construct(PodcastIndexService $podcastIndexService)
    {
        parent::__construct();
        $this->podcastIndexService = $podcastIndexService;
    }

    public function handle()
    {
        $subscriptions = PodcastIndexSubscription::with('user')->get();
        $notified = 0;
        foreach ($subscriptions as $subscription) {
            $feedId = $subscription->feed_id;
            $user = $subscription->user;
            if (!$user) continue;

            // Use cache to store last episode pubdate per user/feed
            $cacheKey = 'podcastindex_last_episode_' . $user->id . '_' . $feedId;
            $lastPubDate = Cache::get($cacheKey);

            $episodes = $this->podcastIndexService->getEpisodesByFeedId($feedId, 1);
            if (!isset($episodes['items']) || empty($episodes['items'])) {
                continue;
            }
            $latestEpisode = $episodes['items'][0];
            $episodePubDate = $latestEpisode['datePublished'] ?? $latestEpisode['datePublishedPretty'] ?? null;
            if (!$episodePubDate) continue;

            // Only notify if this episode is new
            if ($lastPubDate !== $episodePubDate) {
                $user->notify(new PodcastIndexNewEpisodeNotification($latestEpisode));
                Cache::put($cacheKey, $episodePubDate, 86400); // 1 day
                $notified++;
            }
        }
        $this->info("Finished checking PodcastIndex subscriptions. Notified: $notified");
    }
} 