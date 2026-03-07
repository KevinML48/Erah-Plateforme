<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommunitySupportGoal extends Model
{
    protected $fillable = [
        'goal_count',
        'title',
        'description',
        'is_unlocked',
        'unlocked_at',
    ];

    protected function casts(): array
    {
        return [
            'goal_count' => 'integer',
            'is_unlocked' => 'boolean',
            'unlocked_at' => 'datetime',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('goal_count');
    }
}
