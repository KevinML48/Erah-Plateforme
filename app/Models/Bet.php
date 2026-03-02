<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bet extends Model
{
    use HasFactory;

    public const STATUS_PLACED = 'placed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_PENDING = 'pending';
    public const STATUS_WON = 'won';
    public const STATUS_LOST = 'lost';
    public const STATUS_VOID = 'void';

    public const PREDICTION_HOME = 'home';
    public const PREDICTION_AWAY = 'away';
    public const PREDICTION_DRAW = 'draw';

    public const SELECTION_TEAM_A = 'team_a';
    public const SELECTION_TEAM_B = 'team_b';
    public const SELECTION_DRAW = 'draw';

    protected $fillable = [
        'user_id',
        'match_id',
        'market_key',
        'selection_key',
        'stake',
        'odds_snapshot',
        'prediction',
        'stake_points',
        'potential_payout',
        'settlement_points',
        'status',
        'idempotency_key',
        'placed_at',
        'cancelled_at',
        'settled_at',
        'payout',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'match_id' => 'integer',
            'stake' => 'integer',
            'odds_snapshot' => 'decimal:3',
            'stake_points' => 'integer',
            'potential_payout' => 'integer',
            'settlement_points' => 'integer',
            'placed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'settled_at' => 'datetime',
            'payout' => 'integer',
            'meta' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PLACED,
            self::STATUS_CANCELLED,
            self::STATUS_PENDING,
            self::STATUS_WON,
            self::STATUS_LOST,
            self::STATUS_VOID,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function predictions(): array
    {
        return [
            self::PREDICTION_HOME,
            self::PREDICTION_AWAY,
            self::PREDICTION_DRAW,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(EsportMatch::class, 'match_id');
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(BetSettlement::class, 'bet_id');
    }
}
