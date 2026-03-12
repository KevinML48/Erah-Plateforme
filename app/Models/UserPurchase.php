<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPurchase extends Model
{
    protected $fillable = [
        'shop_item_id',
        'user_id',
        'cost_points',
        'status',
        'idempotency_key',
        'payload',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'shop_item_id' => 'integer',
            'user_id' => 'integer',
            'cost_points' => 'integer',
            'payload' => 'array',
            'purchased_at' => 'datetime',
        ];
    }

    public function shopItem(): BelongsTo
    {
        return $this->belongsTo(ShopItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
