<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityRewardGrant extends Model
{
    protected $fillable = [
        'user_id',
        'domain',
        'action',
        'dedupe_key',
        'subject_type',
        'subject_id',
        'xp_amount',
        'rank_points_amount',
        'reward_points_amount',
        'bet_points_amount',
        'duel_score_amount',
        'meta',
        'granted_on',
        'granted_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'xp_amount' => 'integer',
            'rank_points_amount' => 'integer',
            'reward_points_amount' => 'integer',
            'bet_points_amount' => 'integer',
            'duel_score_amount' => 'integer',
            'meta' => 'array',
            'granted_on' => 'date',
            'granted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
