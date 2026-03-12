<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityEvent extends Model
{
    use HasFactory;

    public const TYPE_CLIP_LIKE = 'clip_like';
    public const TYPE_CLIP_COMMENT = 'clip_comment';
    public const TYPE_CLIP_FAVORITE = 'clip_favorite';
    public const TYPE_CLIP_SHARE = 'clip_share';
    public const TYPE_BET_PLACED = 'bet_placed';
    public const TYPE_BET_WON = 'bet_won';
    public const TYPE_DUEL_SENT = 'duel_sent';
    public const TYPE_DUEL_ACCEPTED = 'duel_accepted';
    public const TYPE_LOGIN_DAILY = 'login_daily';
    public const TYPE_GIFT_CART_ADD = 'gift_cart_add';
    public const TYPE_GIFT_CART_UPDATE = 'gift_cart_update';
    public const TYPE_GIFT_CART_REMOVE = 'gift_cart_remove';
    public const TYPE_GIFT_CART_CHECKOUT = 'gift_cart_checkout';
    public const TYPE_GIFT_FAVORITE_ADD = 'gift_favorite_add';
    public const TYPE_GIFT_FAVORITE_REMOVE = 'gift_favorite_remove';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event_type',
        'ref_type',
        'ref_id',
        'occurred_at',
        'unique_key',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'occurred_at' => 'datetime',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function eventTypes(): array
    {
        return [
            self::TYPE_CLIP_LIKE,
            self::TYPE_CLIP_COMMENT,
            self::TYPE_CLIP_FAVORITE,
            self::TYPE_CLIP_SHARE,
            self::TYPE_BET_PLACED,
            self::TYPE_BET_WON,
            self::TYPE_DUEL_SENT,
            self::TYPE_DUEL_ACCEPTED,
            self::TYPE_LOGIN_DAILY,
            self::TYPE_GIFT_CART_ADD,
            self::TYPE_GIFT_CART_UPDATE,
            self::TYPE_GIFT_CART_REMOVE,
            self::TYPE_GIFT_CART_CHECKOUT,
            self::TYPE_GIFT_FAVORITE_ADD,
            self::TYPE_GIFT_FAVORITE_REMOVE,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
