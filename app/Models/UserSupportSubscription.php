<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSupportSubscription extends Model
{
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PENDING_CHECKOUT = 'pending_checkout';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'supporter_plan_id',
        'status',
        'provider',
        'provider_customer_id',
        'provider_subscription_id',
        'provider_price_id',
        'checkout_session_id',
        'started_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ended_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'supporter_plan_id' => 'integer',
            'started_at' => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
            'canceled_at' => 'datetime',
            'ended_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_INACTIVE,
            self::STATUS_PENDING_CHECKOUT,
            self::STATUS_ACTIVE,
            self::STATUS_PAST_DUE,
            self::STATUS_CANCELED,
            self::STATUS_EXPIRED,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(function (Builder $builder) {
                $builder->whereNull('current_period_end')
                    ->orWhere('current_period_end', '>=', now());
            })
            ->whereNull('ended_at');
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->orderByDesc('started_at')->orderByDesc('id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SupporterPlan::class, 'supporter_plan_id');
    }
}
