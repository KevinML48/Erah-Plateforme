<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchSelection extends Model
{
    use HasFactory;

    public const KEY_TEAM_A = 'team_a';
    public const KEY_TEAM_B = 'team_b';
    public const KEY_DRAW = 'draw';

    protected $fillable = [
        'market_id',
        'key',
        'label',
        'odds',
    ];

    protected function casts(): array
    {
        return [
            'market_id' => 'integer',
            'odds' => 'decimal:3',
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(MatchMarket::class, 'market_id');
    }
}

