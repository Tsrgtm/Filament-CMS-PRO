<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagTranslation extends Model
{
    protected $fillable = [
        'tag_id',
        'locale',
        'name',
        'slug',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    /**
     * Relationship back to parent tag.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
