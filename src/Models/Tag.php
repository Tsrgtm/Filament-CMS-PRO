<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    /**
     * Relationship to translations.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TagTranslation::class);
    }

    /**
     * Get active translation helper attribute.
     */
    public function getTranslationAttribute()
    {
        $locale = app()->getLocale();
        return $this->translations()->where('locale', $locale)->first() 
            ?? $this->translations()->first();
    }

    /**
     * Relationship to posts.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
