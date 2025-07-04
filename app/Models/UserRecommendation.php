<?php
// App/Models/UserRecommendation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feed_id',
        'podcast_title',
        'podcast_image',
        'recommendation_score',
        'reason',
        'is_dismissed'
    ];

    protected $casts = [
        'is_dismissed' => 'boolean',
        'recommendation_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class, 'feed_id', 'feed_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_dismissed', false);
    }
}