<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            Log::info('Preparing verification email', [
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

            $verificationUrl = $this->verificationUrl($notifiable);

            Log::info('Generated verification URL', [
                'url' => $verificationUrl
            ]);

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Thank you for registering with us. Please verify your email address to continue.')
                ->action('Verify Email Address', $verificationUrl)
                ->line('This verification link will expire in 60 minutes.')
                ->line('If you did not create an account, no further action is required.')
                ->salutation('Best regards, ' . config('app.name'));

        } catch (\Exception $e) {
            Log::error('Error in toMail method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $notifiable->id,
                'email' => $notifiable->email
            ]);
            throw $e;
        }
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        try {
            $frontendUrl = config('app.frontend_url');
            
            Log::info('Generating verification URL', [
                'frontend_url' => $frontendUrl,
                'user_id' => $notifiable->id,
                'email' => $notifiable->email
            ]);

            $token = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // Extract the query string from the signed URL
            $queryString = parse_url($token, PHP_URL_QUERY);
            
            // Combine with frontend URL
            $finalUrl = $frontendUrl . '/verify-email?' . $queryString;

            Log::info('Generated verification URL successfully', [
                'final_url' => $finalUrl
            ]);

            return $finalUrl;

        } catch (\Exception $e) {
            Log::error('Error generating verification URL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $notifiable->id,
                'email' => $notifiable->email
            ]);
            throw $e;
        }
    }
} 