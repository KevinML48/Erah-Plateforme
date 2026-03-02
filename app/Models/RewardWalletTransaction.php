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
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
