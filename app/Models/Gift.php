<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'cost_points',
        'stock',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'cost_points' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(GiftRedemption::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(GiftCartItem::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(GiftFavorite::class);
    }
}
