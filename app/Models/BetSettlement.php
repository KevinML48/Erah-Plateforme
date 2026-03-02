<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'bet_id',
        'outcome',
        'payout',
        'settled_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'bet_id' => 'integer',
            'payout' => 'integer',
            'settled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class);
    }
}

