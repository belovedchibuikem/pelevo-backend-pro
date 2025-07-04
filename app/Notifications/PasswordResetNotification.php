<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            Log::info('Preparing password reset email', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email,
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ]
            ]);

            $frontendUrl = config('app.frontend_url');
            $resetUrl = $frontendUrl . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);

            Log::info('Generated reset URL', [
                'url' => $resetUrl
            ]);

            return (new MailMessage)
                ->subject('Reset Password Notification')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', $resetUrl)
                ->line('This password reset link will expire in 60 minutes.')
                ->line('If you did not request a password reset, no further action is required.')
                ->salutation('Best regards, ' . config('app.name'));

        } catch (\Exception $e) {
            Log::error('Error in password reset toMail method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $notifiable->id,
                'email' => $notifiable->email
            ]);
            throw $e;
        }
    }
} 