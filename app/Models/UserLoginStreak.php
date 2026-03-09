<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginStreak extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_login_on',
        'current_multiplier',
        'last_reward_points',
        'streak_started_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
            'last_login_on' => 'date',
            'current_multiplier' => 'decimal:2',
            'last_reward_points' => 'integer',
            'streak_started_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
