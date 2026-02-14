<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\RewardRedemptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardRedemption extends Model
{
    protected $fillable = [
        'reward_id',
        'user_id',
        'status',
        'points_cost_snapshot',
        'reward_name_snapshot',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address1',
        'shipping_address2',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'admin_note',
        'tracking_code',
        'debited_points',
        'refunded_points',
        'reserved_stock',
        'approved_at',
        'shipped_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RewardRedemptionStatus::class,
            'points_cost_snapshot' => 'integer',
            'debited_points' => 'boolean',
            'refunded_points' => 'boolean',
            'reserved_stock' => 'boolean',
            'approved_at' => 'datetime',
            'shipped_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canCancel(): bool
    {
        return $this->status === RewardRedemptionStatus::Pending;
    }

    public function canApprove(): bool
    {
        return $this->status === RewardRedemptionStatus::Pending;
    }

    public function canShip(): bool
    {
        return $this->status === RewardRedemptionStatus::Approved;
    }
}

