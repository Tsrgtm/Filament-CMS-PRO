<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEngagement extends Model
{
    protected $table = 'analytics_engagement';

    protected $fillable = [
        'page_view_id',
        'scroll_depth_percentage',
        'time_on_page_seconds',
    ];

    /**
     * Relationship back to parent page view log.
     */
    public function pageView(): BelongsTo
    {
        return $this->belongsTo(AnalyticsPageView::class, 'page_view_id');
    }
}
