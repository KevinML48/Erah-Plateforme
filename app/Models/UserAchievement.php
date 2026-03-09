<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    protected $fillable = [
        'achievement_id',
        'user_id',
        'progress_value',
        'meta',
        'unlocked_at',
    ];

    protected function casts(): array
    {
        return [
            'achievement_id' => 'integer',
            'user_id' => 'integer',
            'progress_value' => 'integer',
            'meta' => 'array',
            'unlocked_at' => 'datetime',
        ];
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
