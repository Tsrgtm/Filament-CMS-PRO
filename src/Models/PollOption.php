<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    protected $fillable = [
        'poll_id',
        'option_text',
        'votes_count',
    ];

    /**
     * Relationship back to parent poll.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Relationship to specific votes.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }
}
