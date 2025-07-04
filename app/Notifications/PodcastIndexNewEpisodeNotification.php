<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PodcastIndexNewEpisodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $episode;

    public function __construct(array $episode)
    {
        $this->episode = $episode;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'episode' => $this->episode,
        ];
    }
} 