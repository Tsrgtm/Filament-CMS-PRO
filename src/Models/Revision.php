<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Revision extends Model
{
    public $timestamps = false; // Manually handle created_at timestamp

    protected $fillable = [
        'post_id',
        'user_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'created_at',
    ];

    protected $casts = [
        'content' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship back to parent post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relationship to editing user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
