<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuelEvent extends Model
{
    protected $fillable = [
        'duel_id',
        'actor_id',
        'event_type',
        'meta',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'duel_id' => 'integer',
            'actor_id' => 'integer',
            'meta' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function duel(): BelongsTo
    {
        return $this->belongsTo(Duel::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
