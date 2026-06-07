<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTranslation extends Model
{
    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'social_meta',
        'schema_markup',
        'editorial_notes',
        'publisher_notes',
        'custom_fields',
    ];

    protected $casts = [
        'content' => 'array',
        'seo_keywords' => 'array',
        'social_meta' => 'array',
        'schema_markup' => 'array',
        'custom_fields' => 'array',
    ];

    /**
     * Relationship back to parent post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Booted method to register model hooks.
     */
    protected static function booted(): void
    {
        static::saved(function (PostTranslation $translation) {
            $translation->post->revisions()->create([
                'user_id' => auth()->id() ?? 1,
                'locale' => $translation->locale,
                'title' => $translation->title,
                'excerpt' => $translation->excerpt,
                'content' => $translation->content,
            ]);
        });
    }
}
