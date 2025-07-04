<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'episode_id',
        'feed_id',
        'podcast_title',
        'episode_title',
        'is_read',
        'sent_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
