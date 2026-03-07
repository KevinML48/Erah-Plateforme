<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupporterMonthlyReward extends Model
{
    protected $fillable = [
        'user_id',
        'reward_month',
        'reward_key',
        'granted_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'reward_month' => 'date',
            'granted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
