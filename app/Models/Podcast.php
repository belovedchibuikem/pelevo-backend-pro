<?php

// App/Models/Podcast.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_id',
        'title',
        'description',
        'author',
        'image',
        'artwork',
        'feed_url',
        'website_url',
        'language',
        'categories',
        'explicit',
        'episode_count',
        'last_update_time',
        'last_crawl_time',
        'last_parse_time',
        'last_good_http_status_time',
        'last_http_status',
        'content_type',
        'itunesId',
        'originalUrl',
        'link',
        'dead',
        'crawl_errors',
        'parse_errors',
        'locked',
        'image_url_hash',
        'newest_item_pub_date',
        'is_featured',
        'trending_score',
        'popularity_score'
    ];

    protected $casts = [
        'categories' => 'array',
        'explicit' => 'boolean',
        'dead' => 'boolean',
        'locked' => 'boolean',
        'is_featured' => 'boolean',
        'last_update_time' => 'datetime',
        'last_crawl_time' => 'datetime',
        'last_parse_time' => 'datetime',
        'last_good_http_status_time' => 'datetime',
        'newest_item_pub_date' => 'datetime',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class, 'feed_id', 'feed_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(PodcastSubscription::class, 'feed_id', 'feed_id');
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