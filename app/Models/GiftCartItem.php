<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gift_id',
        'quantity',
        'added_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'gift_id' => 'integer',
            'quantity' => 'integer',
            'added_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }
}

