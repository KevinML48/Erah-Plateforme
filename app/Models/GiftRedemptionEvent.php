<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftRedemptionEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'redemption_id',
        'actor_user_id',
        'type',
        'data',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'redemption_id' => 'integer',
            'actor_user_id' => 'integer',
            'data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(GiftRedemption::class, 'redemption_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
