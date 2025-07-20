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
        'id',
        'title',
        'link',
        'description',
        'guid',
        'date_published',
        'date_published_pretty',
        'date_crawled',
        'enclosure_url',
        'enclosure_type',
        'enclosure_length',
        'duration',
        'explicit',
        'episode',
        'episode_type',
        'season',
        'image',
        'feed_itunes_id',
        'feed_url',
        'feed_image',
        'feed_id',
        'podcast_guid',
        'feed_language',
        'feed_dead',
        'feed_duplicate_of',
        'chapters_url',
        'transcript_url',
    ];

    protected $casts = [
        'id' => 'integer',
        'date_published' => 'integer',
        'date_crawled' => 'integer',
        'enclosure_length' => 'integer',
        'duration' => 'integer',
        'explicit' => 'boolean',
        'episode' => 'integer',
        'season' => 'integer',
        'feed_itunes_id' => 'integer',
        'feed_id' => 'integer',
        'feed_dead' => 'boolean',
        'feed_duplicate_of' => 'integer',
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