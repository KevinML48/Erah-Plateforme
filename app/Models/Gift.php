<?php

namespace App\Models;

use App\Support\LaunchGiftCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'category',
        'type',
        'delivery_type',
        'image_url',
        'cost_points',
        'stock',
        'is_active',
        'is_featured',
        'sort_order',
        'requires_admin_validation',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'key' => 'string',
            'cost_points' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'requires_admin_validation' => 'boolean',
            'metadata' => 'array',
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

    /**
     * @return array<string, mixed>|null
     */
    public function launchCatalogDefinition(): ?array
    {
        return LaunchGiftCatalog::definitionForGift($this);
    }

    public function launchCatalogKey(): ?string
    {
        return $this->key ?: ($this->launchCatalogDefinition()['key'] ?? null);
    }

    public function launchCatalogCategory(): ?string
    {
        return $this->category ?: ($this->launchCatalogDefinition()['category'] ?? null);
    }

    public function launchCatalogDeliveryType(): ?string
    {
        return $this->delivery_type ?: ($this->launchCatalogDefinition()['delivery_type'] ?? null);
    }

    public function launchCatalogCategoryLabel(): ?string
    {
        return match ($this->launchCatalogCategory()) {
            'profile_digital' => 'Profil numerique',
            'digital_reward' => 'Digital',
            'manual_reward' => 'Recompense manuelle',
            'physical' => 'Physique',
            'premium' => 'Premium',
            default => null,
        };
    }

    public function launchCatalogDeliveryTypeLabel(): ?string
    {
        return match ($this->launchCatalogDeliveryType()) {
            'profile' => 'Profil',
            'digital' => 'Digital',
            'manual' => 'Manuel',
            'physical' => 'Physique',
            'premium' => 'Premium',
            default => null,
        };
    }

    public function launchCatalogRequiresAdminValidation(): bool
    {
        return (bool) ($this->requires_admin_validation || ($this->launchCatalogDefinition()['requires_admin_validation'] ?? false));
    }
}
