<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'metric',
        'threshold',
        'badge_label',
        'rewards',
        'meta',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'threshold' => 'integer',
            'rewards' => 'array',
            'meta' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
}
