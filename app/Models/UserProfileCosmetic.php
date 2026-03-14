<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfileCosmetic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gift_id',
        'gift_redemption_id',
        'slot',
        'cosmetic_key',
        'expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'gift_id' => 'integer',
            'gift_redemption_id' => 'integer',
            'expires_at' => 'datetime',
            'metadata' => 'array',
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

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(GiftRedemption::class, 'gift_redemption_id');
    }
}
