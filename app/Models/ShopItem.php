<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopItem extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'cost_points',
        'stock',
        'payload',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'cost_points' => 'integer',
            'stock' => 'integer',
            'payload' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(UserPurchase::class);
    }
}
