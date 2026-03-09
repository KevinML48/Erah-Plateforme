<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveCodeRedemption extends Model
{
    protected $fillable = [
        'live_code_id',
        'user_id',
        'reward_points',
        'bet_points',
        'xp_reward',
        'meta',
        'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'live_code_id' => 'integer',
            'user_id' => 'integer',
            'reward_points' => 'integer',
            'bet_points' => 'integer',
            'xp_reward' => 'integer',
            'meta' => 'array',
            'redeemed_at' => 'datetime',
        ];
    }

    public function liveCode(): BelongsTo
    {
        return $this->belongsTo(LiveCode::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
