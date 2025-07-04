<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListeningHistory extends Model
{
    protected $table = 'listening_history';

    protected $fillable = [
        'user_id',
        'episode_id',
        'duration_listened',
        'earnings',
        'ip_address',
        'is_eligible_for_earnings',
    ];

    protected $casts = [
        'duration_listened' => 'integer',
        'earnings' => 'decimal:2',
        'is_eligible_for_earnings' => 'boolean',
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