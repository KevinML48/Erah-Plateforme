<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gift_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'gift_id' => 'integer',
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

