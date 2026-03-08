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
    public const KEY_CHAMPION = 'champion';
    public const KEY_FINALE = 'finale';
    public const KEY_TOP_4 = 'top_4';
    public const KEY_TOP_8 = 'top_8';
    public const KEY_TOP_16 = 'top_16';
    public const KEY_OUTSIDE_TOP_16 = 'outside_top_16';

    protected $fillable = [
        'market_id',
        'key',
        'label',
        'odds',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'market_id' => 'integer',
            'odds' => 'decimal:3',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function rocketLeagueTournamentSelectionKeys(): array
    {
        return [
            self::KEY_CHAMPION,
            self::KEY_FINALE,
            self::KEY_TOP_4,
            self::KEY_TOP_8,
            self::KEY_TOP_16,
            self::KEY_OUTSIDE_TOP_16,
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(MatchMarket::class, 'market_id');
    }
}
