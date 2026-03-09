<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuelResult extends Model
{
    protected $fillable = [
        'duel_id',
        'winner_user_id',
        'loser_user_id',
        'actor_id',
        'challenger_score',
        'challenged_score',
        'note',
        'meta',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'duel_id' => 'integer',
            'winner_user_id' => 'integer',
            'loser_user_id' => 'integer',
            'actor_id' => 'integer',
            'challenger_score' => 'integer',
            'challenged_score' => 'integer',
            'meta' => 'array',
            'settled_at' => 'datetime',
        ];
    }

    public function duel(): BelongsTo
    {
        return $this->belongsTo(Duel::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function loser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'loser_user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
