<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AnalyticsPageView extends Model
{
    public $timestamps = false; // Disable default updated_at/created_at, manually handle viewed_at

    protected $fillable = [
        'post_id',
        'path',
        'referrer_host',
        'visitor_hash',
        'is_robot',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'viewed_at',
    ];

    protected $casts = [
        'is_robot' => 'boolean',
        'viewed_at' => 'datetime',
    ];

    /**
     * Relationship to parent post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relationship to associated user engagement details.
     */
    public function engagement(): HasOne
    {
        return $this->hasOne(AnalyticsEngagement::class, 'page_view_id');
    }
}
