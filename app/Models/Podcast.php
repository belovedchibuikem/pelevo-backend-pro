<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Podcast extends Model
{
    protected $table = 'podcasts';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'title',
        'url',
        'original_url',
        'link',
        'description',
        'author',
        'owner_name',
        'image',
        'artwork',
        'last_update_time',
        'last_crawl_time',
        'last_parse_time',
        'in_polling_queue',
        'priority',
        'last_good_http_status_time',
        'last_http_status',
        'content_type',
        'itunes_id',
        'generator',
        'language',
        'type',
        'dead',
        'crawl_errors',
        'parse_errors',
        'categories',
        'locked',
        'explicit',
        'podcast_guid',
        'medium',
        'episode_count',
        'image_url_hash',
        'newest_item_pubdate',
    ];

    protected $casts = [
        'id' => 'integer',
        'last_update_time' => 'integer',
        'last_crawl_time' => 'integer',
        'last_parse_time' => 'integer',
        'in_polling_queue' => 'boolean',
        'priority' => 'integer',
        'last_good_http_status_time' => 'integer',
        'last_http_status' => 'integer',
        'itunes_id' => 'integer',
        'type' => 'integer',
        'dead' => 'boolean',
        'crawl_errors' => 'integer',
        'parse_errors' => 'integer',
        'categories' => 'array',
        'locked' => 'boolean',
        'explicit' => 'boolean',
        'episode_count' => 'integer',
        'image_url_hash' => 'integer',
        'newest_item_pubdate' => 'integer',
    ];
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class, 'feed_id', 'feed_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_items', 'episode_id', 'playlist_id');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'podcast_subscriptions', 'feed_id', 'user_id', 'feed_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->orderBy('trending_score', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('popularity_score', 'desc');
    }

    public function getSubscriberCountAttribute(): int
    {
        return $this->subscriptions()->count();
    }
}