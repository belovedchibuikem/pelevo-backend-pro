<?php
/// App/Models/PodcastSubscription.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PodcastSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feed_id',
        'podcast_title',
        'podcast_image',
        'subscribed_at',
        'last_episode_check',
        'notification_enabled'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'last_episode_check' => 'datetime',
        'notification_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class, 'feed_id', 'feed_id');
    }
}