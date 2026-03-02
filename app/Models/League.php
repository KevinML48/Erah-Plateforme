<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'key',
        'name',
        'min_rank_points',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_rank_points' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class, 'current_league_id');
    }

    public function promotionsTo(): HasMany
    {
        return $this->hasMany(LeaguePromotion::class, 'to_league_id');
    }

    public function promotionsFrom(): HasMany
    {
        return $this->hasMany(LeaguePromotion::class, 'from_league_id');
    }
}
