<?php
// App/Models/Episode.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'episode_id',
        'feed_id',
        'title',
        'description',
        'link',
        'guid',
        'pub_date',
        'duration',
        'explicit',
        'episode',
        'episode_type',
        'season',
        'image',
        'feed_image',
        'feed_title',
        'feed_language',
        'chaptersUrl',
        'transcriptUrl',
        'enclosureUrl',
        'enclosureType',
        'enclosureLength',
        'is_new'
    ];

    protected $casts = [
        'pub_date' => 'datetime',
        'explicit' => 'boolean',
        'is_new' => 'boolean',
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class, 'feed_id', 'feed_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(EpisodeNotification::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function playHistories()
    {
        return $this->hasMany(PlayHistory::class);
    }

    public function playlistItems()
    {
        return $this->hasMany(PlaylistItem::class);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('pub_date', '>=', now()->subDays($days));
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration) {
            return 'Unknown';
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
    }
}