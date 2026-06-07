<?php

namespace Nepal360\FilamentCmsPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PollVote extends Model
{
    public $timestamps = false; // Disable updated_at, manually handle created_at

    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'session_id',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relationship back to parent poll.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Relationship back to option target.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    /**
     * Relationship to voting user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
