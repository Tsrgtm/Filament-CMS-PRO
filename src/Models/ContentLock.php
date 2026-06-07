<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ContentLock extends Model
{
    protected $primaryKey = 'post_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'user_id',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    /**
     * Relationship back to parent post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relationship to locking user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
