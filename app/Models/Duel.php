<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Duel extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_SETTLED = 'settled';
    public const STATUS_REFUSED = 'refused';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'challenger_id',
        'challenged_id',
        'status',
        'idempotency_key',
        'message',
        'requested_at',
        'expires_at',
        'responded_at',
        'accepted_at',
        'refused_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'challenger_id' => 'integer',
            'challenged_id' => 'integer',
            'requested_at' => 'datetime',
            'expires_at' => 'datetime',
            'responded_at' => 'datetime',
            'accepted_at' => 'datetime',
            'refused_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_SETTLED,
            self::STATUS_REFUSED,
            self::STATUS_EXPIRED,
        ];
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $builder) use ($userId) {
            $builder->where('challenger_id', $userId)
                ->orWhere('challenged_id', $userId);
        });
    }

    public function challenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }

    public function challenged(): BelongsTo
    {
        return $this->belongsTo(User::class, 'challenged_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(DuelEvent::class);
    }

    public function result()
    {
        return $this->hasOne(DuelResult::class);
    }
}
