<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GiftRedemption extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'user_id',
        'gift_id',
        'cost_points_snapshot',
        'status',
        'reason',
        'tracking_code',
        'tracking_carrier',
        'shipping_note',
        'internal_note',
        'requested_at',
        'approved_at',
        'rejected_at',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'gift_id' => 'integer',
            'cost_points_snapshot' => 'integer',
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_APPROVED => 'Approuvee',
            self::STATUS_REJECTED => 'Rejetee',
            self::STATUS_SHIPPED => 'Expediee',
            self::STATUS_DELIVERED => 'Livree',
            self::STATUS_CANCELLED => 'Annulee',
            self::STATUS_REFUNDED => 'Remboursee',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        if (! is_string($status) || trim($status) === '') {
            return 'Inconnu';
        }

        $labels = self::statusLabels();

        return $labels[$status] ?? Str::headline($status);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(GiftRedemptionEvent::class, 'redemption_id');
    }
}
