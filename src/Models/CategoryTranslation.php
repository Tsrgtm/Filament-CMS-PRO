<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryTranslation extends Model
{
    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'slug',
        'description',
    ];

    /**
     * Relationship back to parent category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
