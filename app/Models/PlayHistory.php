<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'episode_id',
        'progress_seconds',
        'status',
        'last_played_at',
    ];

    protected $casts = [
        'progress_seconds' => 'integer',
        'last_played_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
