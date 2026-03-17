<?php

namespace App\Models;

use App\Support\LaunchGiftCatalog;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'slug',
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
            'slug' => 'string',
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

    public function routeIdentifier(): string
    {
        return $this->slug ?: (string) $this->getKey();
    }

    public function primaryImageUrl(): string
    {
        return $this->image_url ?: '/template/assets/img/logo.png';
    }

    /**
     * @return array<string, mixed>
     */
    public function detailMetadata(): array
    {
        return is_array($this->metadata) ? $this->metadata : [];
    }

    public function shortDescription(): string
    {
        return trim((string) ($this->detailMetadata()['short_description'] ?? $this->launchCatalogDefinition()['short_description'] ?? $this->description ?? ''));
    }

    public function longDescription(): string
    {
        return trim((string) ($this->detailMetadata()['long_description'] ?? $this->launchCatalogDefinition()['long_description'] ?? $this->description ?? ''));
    }

    /**
     * @return array<int, string>
     */
    public function galleryImages(): array
    {
        $gallery = $this->detailMetadata()['gallery'] ?? [];
        $images = collect(is_array($gallery) ? $gallery : [])
            ->map(fn ($image): string => trim((string) $image))
            ->filter()
            ->values();

        if ($images->isEmpty() && $this->image_url) {
            $images = collect([$this->image_url]);
        }

        return $images->values()->all();
    }

    /**
     * @return array<int, string>
     */
    public function conditions(): array
    {
        $rawConditions = $this->detailMetadata()['conditions'] ?? $this->launchCatalogDefinition()['conditions'] ?? [];

        if (is_string($rawConditions)) {
            $rawConditions = preg_split('/\r\n|\r|\n/', $rawConditions) ?: [];
        }

        return collect(is_array($rawConditions) ? $rawConditions : [])
            ->map(fn ($item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    public function deliveryDetails(): ?string
    {
        $value = trim((string) ($this->detailMetadata()['delivery_details'] ?? $this->launchCatalogDefinition()['delivery_details'] ?? ''));

        return $value !== '' ? $value : null;
    }

    public function eligibilityDetails(): ?string
    {
        $value = trim((string) ($this->detailMetadata()['eligibility_details'] ?? $this->launchCatalogDefinition()['eligibility_details'] ?? ''));

        return $value !== '' ? $value : null;
    }

    public function metaTitle(): string
    {
        $custom = trim((string) ($this->detailMetadata()['meta_title'] ?? ''));

        return $custom !== '' ? $custom : $this->title.' | Cadeaux ERAH';
    }

    public function metaDescription(): string
    {
        $custom = trim((string) ($this->detailMetadata()['meta_description'] ?? ''));
        if ($custom !== '') {
            return $custom;
        }

        $base = $this->shortDescription() ?: $this->longDescription();
        if ($base === '') {
            return 'Detail cadeau ERAH, consultation, conditions et achat depuis votre solde points.';
        }

        return Str::limit($base, 155, '');
    }

    public function supporterOnly(): bool
    {
        return (bool) ($this->detailMetadata()['supporter_only'] ?? $this->launchCatalogDefinition()['supporter_only'] ?? false);
    }

    public function isRepeatable(): bool
    {
        return (bool) ($this->detailMetadata()['is_repeatable'] ?? $this->launchCatalogDefinition()['is_repeatable'] ?? true);
    }

    public function publicTypeLabel(): string
    {
        $type = trim((string) ($this->type ?: ($this->launchCatalogDefinition()['type'] ?? '')));

        return match ($type) {
            'badge' => 'Badge',
            'avatar_frame' => 'Contour avatar',
            'banner' => 'Banniere',
            'title', 'profile_title' => 'Titre de profil',
            'profile_style' => 'Style profil',
            'featured_profile' => 'Mise en avant',
            default => $type !== '' ? Str::headline(str_replace('_', ' ', $type)) : 'Cadeau membre',
        };
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
