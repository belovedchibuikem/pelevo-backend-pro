<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PodcastIndexSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'feed_id',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 