<?php

namespace App\Notifications;

use App\Models\Podcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPodcastEpisode extends Notification implements ShouldQueue
{
    use Queueable;

    protected $podcast;
    protected $episode;

    public function __construct(Podcast $podcast, array $episode)
    {
        $this->podcast = $podcast;
        $this->episode = $episode;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Episode: {$this->podcast->title}")
            ->line("A new episode of {$this->podcast->title} is available!")
            ->line("Episode: {$this->episode['title']}")
            ->line($this->episode['description'])
            ->action('Listen Now', $this->episode['enclosureUrl'])
            ->line('Thank you for using our podcast platform!');
    }

    public function toArray($notifiable): array
    {
        return [
            'podcast_id' => $this->podcast->id,
            'podcast_title' => $this->podcast->title,
            'episode_title' => $this->episode['title'],
            'episode_description' => $this->episode['description'],
            'episode_url' => $this->episode['enclosureUrl'],
            'published_at' => $this->episode['datePublished'],
        ];
    }
} 