<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketOption extends Model
{
    protected $fillable = [
        'market_id',
        'label',
        'key',
        'odds_decimal',
        'popularity_weight',
        'is_winner',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'odds_decimal' => 'decimal:2',
            'popularity_weight' => 'decimal:4',
            'is_winner' => 'boolean',
            'settled_at' => 'datetime',
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function selections(): HasMany
    {
        return $this->hasMany(TicketSelection::class, 'option_id');
    }
}

