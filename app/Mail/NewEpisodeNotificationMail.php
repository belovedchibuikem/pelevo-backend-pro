<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class NewEpisodeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $episode;
    public $showName;

    public function __construct(User $user, array $episode, string $showName)
    {
        $this->user = $user;
        $this->episode = $episode;
        $this->showName = $showName;
    }

    public function build()
    {
        $subject = 'New Episode Available: ' . ($this->episode['title'] ?? $this->episode['name'] ?? '');
        return $this->subject($subject)
            ->view('emails.new_episode_notification')
            ->with([
                'user' => $this->user,
                'episode' => $this->episode,
                'showName' => $this->showName,
            ]);
    }
} 