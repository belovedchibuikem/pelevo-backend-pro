<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\PasswordResetNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'apple_id',
        'spotify_id',
        'device_id',
        'device_name',
        'is_active',
        'registered_at',
        'registration_ip',
        'last_login_at',
        'profile_image_url',
        'subscribed_categories',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'registered_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function subscribedPodcasts()
    {
        return $this->belongsToMany(Podcast::class, 'podcast_subscriptions')
            ->withPivot('notify_new_episodes')
            ->withTimestamps();
    }

    public function podcastIndexSubscriptions()
    {
        return $this->hasMany(PodcastIndexSubscription::class);
    }

    // Accessor for profile_image_url
    public function getProfileImageUrlAttribute($value)
    {
        if ($value) {
            return url($value);
        }
        return null;
    }
}
