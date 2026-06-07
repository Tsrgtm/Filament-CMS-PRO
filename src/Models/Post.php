<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

class Post extends Model
{
    protected $fillable = [
        'featured_image',
        'layout_template',
        'trending_score',
        'status',
        'comment_rules',
        'sitemap_enabled',
        'fact_check_status',
        'read_time_minutes',
        'published_at',
        'custom_fields',
    ];

    protected $casts = [
        'comment_rules' => 'array',
        'sitemap_enabled' => 'boolean',
        'published_at' => 'datetime',
        'trending_score' => 'float',
        'custom_fields' => 'array',
    ];

    /**
     * Relationship to post translations.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class);
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
     * Relationship to co-authors.
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_author')
            ->withPivot(['author_type', 'display_order'])
            ->withTimestamps()
            ->orderByPivot('display_order', 'asc');
    }

    /**
     * Relationship to categories.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    /**
     * Relationship to tags.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    /**
     * Relationship to comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relationship to revisions.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relationship to content lock info.
     */
    public function lock(): HasOne
    {
        return $this->hasOne(ContentLock::class);
    }
}
