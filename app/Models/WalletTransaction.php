<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    public const TYPE_STAKE = 'stake';
    public const TYPE_REFUND = 'refund';
    public const TYPE_PAYOUT = 'payout';
    public const TYPE_GRANT = 'grant';
    public const TYPE_ADJUST = 'adjust';
    public const TYPE_VOID_REFUND = 'void_refund';

    public const REF_TYPE_BET = 'bet';
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
