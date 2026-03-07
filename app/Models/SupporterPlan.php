<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupporterPlan extends Model
{
    protected $fillable = [
        'key',
        'name',
        'price_cents',
        'currency',
        'billing_interval',
        'billing_months',
        'discount_percent',
        'sort_order',
        'description',
        'stripe_price_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'billing_months' => 'integer',
            'discount_percent' => 'float',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSupportSubscription::class);
    }
}
