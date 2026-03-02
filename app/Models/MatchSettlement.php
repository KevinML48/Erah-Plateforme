<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'idempotency_key',
        'result',
        'bets_total',
        'won_count',
        'lost_count',
        'void_count',
        'payout_total',
        'processed_by',
        'processed_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'match_id' => 'integer',
            'bets_total' => 'integer',
            'won_count' => 'integer',
            'lost_count' => 'integer',
            'void_count' => 'integer',
            'payout_total' => 'integer',
            'processed_by' => 'integer',
            'processed_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(EsportMatch::class, 'match_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
