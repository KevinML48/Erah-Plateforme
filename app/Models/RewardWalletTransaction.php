<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardWalletTransaction extends Model
{
    use HasFactory;

    public const TYPE_GRANT = 'grant';
    public const TYPE_MISSION_REWARD = 'mission_reward';
    public const TYPE_REDEEM_COST = 'redeem_cost';
    public const TYPE_REDEEM_REFUND = 'redeem_refund';
    public const TYPE_ADJUST = 'adjust';
    public const TYPE_GIFT_PURCHASE = 'gift_purchase';
    public const TYPE_BET_STAKE = 'bet_stake';
    public const TYPE_BET_PAYOUT = 'bet_payout';
    public const TYPE_BET_REFUND = 'bet_refund';
    public const TYPE_DUEL_STAKE = 'duel_stake';
    public const TYPE_DUEL_WIN = 'duel_win';
    public const TYPE_DUEL_REFUND = 'duel_refund';
    public const TYPE_ADMIN_ADJUSTMENT = 'admin_adjustment';
    public const TYPE_STREAK_REWARD = 'streak_reward';
    public const TYPE_SHOP_PURCHASE = 'shop_purchase';
    public const TYPE_ACTIVITY_REWARD = 'activity_reward';

    public const REF_TYPE_MISSION = 'mission';
    public const REF_TYPE_GIFT = 'gift';
    public const REF_TYPE_ADMIN = 'admin';
    public const REF_TYPE_SYSTEM = 'system';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'ref_type',
        'ref_id',
        'unique_key',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'amount' => 'integer',
            'balance_after' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_GRANT,
            self::TYPE_MISSION_REWARD,
            self::TYPE_REDEEM_COST,
            self::TYPE_REDEEM_REFUND,
            self::TYPE_ADJUST,
            self::TYPE_GIFT_PURCHASE,
            self::TYPE_BET_STAKE,
            self::TYPE_BET_PAYOUT,
            self::TYPE_BET_REFUND,
            self::TYPE_DUEL_STAKE,
            self::TYPE_DUEL_WIN,
            self::TYPE_DUEL_REFUND,
            self::TYPE_ADMIN_ADJUSTMENT,
            self::TYPE_STREAK_REWARD,
            self::TYPE_SHOP_PURCHASE,
            self::TYPE_ACTIVITY_REWARD,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
