<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchMarket extends Model
{
    use HasFactory;

    public const KEY_WINNER = 'WINNER';

    protected $fillable = [
        'match_id',
        'key',
        'title',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'match_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(EsportMatch::class, 'match_id');
    }

    public function selections(): HasMany
    {
        return $this->hasMany(MatchSelection::class, 'market_id');
    }
}

