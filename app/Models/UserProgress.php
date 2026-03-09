<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'current_league_id',
        'total_xp',
        'total_rank_points',
        'duel_score',
        'duel_wins',
        'duel_losses',
        'duel_current_streak',
        'duel_best_streak',
        'last_points_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'current_league_id' => 'integer',
            'total_xp' => 'integer',
            'total_rank_points' => 'integer',
            'duel_score' => 'integer',
            'duel_wins' => 'integer',
            'duel_losses' => 'integer',
            'duel_current_streak' => 'integer',
            'duel_best_streak' => 'integer',
            'last_points_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class, 'current_league_id');
    }
}
