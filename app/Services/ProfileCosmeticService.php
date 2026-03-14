<?php

namespace App\Services;

use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\User;
use App\Models\UserProfileCosmetic;
use App\Support\LaunchGiftCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProfileCosmeticService
{
    /**
     * @return array<string, string>
     */
    public function slotLabels(): array
    {
        return [
            'badge' => 'Badge',
            'avatar_frame' => 'Contour avatar',
            'banner' => 'Banniere',
            'profile_title' => 'Titre',
            'username_color' => 'Couleur pseudo',
            'profile_theme' => 'Theme',
            'profile_featured' => 'Mise en avant',
        ];
    }

    public function blocksRepurchase(User $user, Gift $gift): ?string
    {
        $definition = LaunchGiftCatalog::definitionForGift($gift);
        if (! is_array($definition) || ! LaunchGiftCatalog::isProfileDigitalDefinition($definition)) {
            return null;
        }

        if ((bool) ($definition['is_repeatable'] ?? true)) {
            return null;
        }

        $unlocks = LaunchGiftCatalog::profileUnlocksForDefinition($definition);
        if ($unlocks->isEmpty()) {
            return null;
        }

        $ownedKeys = UserProfileCosmetic::query()
            ->where('user_id', $user->id)
            ->whereIn('cosmetic_key', $unlocks->pluck('cosmetic_key')->all())
            ->pluck('cosmetic_key')
            ->all();

        if (count($ownedKeys) === $unlocks->count()) {
            return 'Vous possedez deja cet objet de profil.';
        }

        return null;
    }

    /**
     * @return array{applied: bool, granted: Collection<int, array<string, mixed>>, auto_equipped_slots: array<int, string>}
     */
    public function grantFromRedemption(User $user, Gift $gift, GiftRedemption $redemption): array
    {
        $definition = LaunchGiftCatalog::definitionForGift($gift);
        if (! is_array($definition) || ! LaunchGiftCatalog::isProfileDigitalDefinition($definition)) {
            return [
                'applied' => false,
                'granted' => collect(),
                'auto_equipped_slots' => [],
            ];
        }

        $granted = collect();
        $autoEquippedSlots = [];
        $now = CarbonImmutable::now();

        foreach (LaunchGiftCatalog::profileUnlocksForDefinition($definition) as $unlock) {
            $slot = (string) $unlock['slot'];
            $cosmeticKey = (string) $unlock['cosmetic_key'];
            $expiresAt = $this->resolveExpiry(
                unlock: $unlock,
                existingCosmetic: UserProfileCosmetic::query()
                    ->where('user_id', $user->id)
                    ->where('cosmetic_key', $cosmeticKey)
                    ->first(),
                now: $now
            );

            $cosmetic = UserProfileCosmetic::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'cosmetic_key' => $cosmeticKey,
                ],
                [
                    'gift_id' => $gift->id,
                    'gift_redemption_id' => $redemption->id,
                    'slot' => $slot,
                    'expires_at' => $expiresAt,
                    'metadata' => [
                        'label' => $unlock['label'] ?? null,
                        'description' => $unlock['description'] ?? null,
                        'preview' => $unlock['preview'] ?? [],
                        'source_gift_key' => $definition['key'] ?? null,
                        'source_gift_title' => $definition['title'] ?? null,
                    ],
                ]
            );

            if ($slot === 'profile_featured') {
                $user->forceFill([
                    'profile_featured_until' => $expiresAt,
                ])->save();
            } elseif ((bool) ($unlock['auto_equip'] ?? true)) {
                $this->equip($user, $cosmetic);
                $autoEquippedSlots[] = $slot;
            }

            $granted->push([
                'slot' => $slot,
                'cosmetic_key' => $cosmeticKey,
                'label' => $unlock['label'] ?? $cosmeticKey,
                'expires_at' => $expiresAt,
            ]);
        }

        return [
            'applied' => true,
            'granted' => $granted,
            'auto_equipped_slots' => array_values(array_unique($autoEquippedSlots)),
        ];
    }

    public function equip(User $user, UserProfileCosmetic $cosmetic): void
    {
        if ((int) $cosmetic->user_id !== (int) $user->id) {
            throw new RuntimeException('Cet objet de profil n appartient pas a cet utilisateur.');
        }

        if ($cosmetic->expires_at !== null && $cosmetic->expires_at->isPast()) {
            throw new RuntimeException('Cet objet de profil a expire.');
        }

        $column = match ($cosmetic->slot) {
            'badge' => 'equipped_profile_badge',
            'avatar_frame' => 'equipped_avatar_frame',
            'banner' => 'equipped_profile_banner',
            'profile_title' => 'equipped_profile_title',
            'username_color' => 'equipped_username_color',
            'profile_theme' => 'equipped_profile_theme',
            default => null,
        };

        if ($column === null) {
            return;
        }

        $user->forceFill([$column => $cosmetic->cosmetic_key])->save();
    }

    /**
     * @return array{
     *   active: array<string, mixed>,
     *   owned_by_slot: array<string, array<int, array<string, mixed>>>,
     *   slot_labels: array<string, string>
     * }
     */
    public function snapshotFor(User $user): array
    {
        $user->loadMissing('profileCosmetics');

        $slotLabels = $this->slotLabels();
        $now = CarbonImmutable::now();
        $grouped = [];

        foreach ($slotLabels as $slot => $slotLabel) {
            $items = $user->profileCosmetics
                ->where('slot', $slot)
                ->sortBy('created_at')
                ->map(function (UserProfileCosmetic $cosmetic) use ($user, $slotLabel, $now): array {
                    $preview = is_array($cosmetic->metadata) ? Arr::get($cosmetic->metadata, 'preview', []) : [];
                    $label = Arr::get($cosmetic->metadata, 'label', $cosmetic->cosmetic_key);
                    $column = $this->equippedColumnForSlot($cosmetic->slot);
                    $equippedKey = $column ? (string) ($user->{$column} ?? '') : '';
                    $isExpired = $cosmetic->expires_at !== null && $cosmetic->expires_at->lessThanOrEqualTo($now);

                    return [
                        'id' => $cosmetic->id,
                        'slot' => $cosmetic->slot,
                        'slot_label' => $slotLabel,
                        'cosmetic_key' => $cosmetic->cosmetic_key,
                        'label' => $label,
                        'description' => Arr::get($cosmetic->metadata, 'description'),
                        'expires_at' => $cosmetic->expires_at,
                        'expires_label' => $cosmetic->expires_at?->format('d/m/Y H:i'),
                        'is_expired' => $isExpired,
                        'is_equipped' => ! $isExpired && $equippedKey !== '' && $equippedKey === $cosmetic->cosmetic_key,
                        'preview' => is_array($preview) ? $preview : [],
                    ];
                })
                ->values()
                ->all();

            $grouped[$slot] = $items;
        }

        return [
            'active' => [
                'badge' => $this->activeDefinitionForKey((string) ($user->equipped_profile_badge ?? ''), $grouped['badge'] ?? []),
                'avatar_frame' => $this->activeDefinitionForKey((string) ($user->equipped_avatar_frame ?? ''), $grouped['avatar_frame'] ?? []),
                'banner' => $this->activeDefinitionForKey((string) ($user->equipped_profile_banner ?? ''), $grouped['banner'] ?? []),
                'profile_title' => $this->activeDefinitionForKey((string) ($user->equipped_profile_title ?? ''), $grouped['profile_title'] ?? []),
                'username_color' => $this->activeDefinitionForKey((string) ($user->equipped_username_color ?? ''), $grouped['username_color'] ?? []),
                'profile_theme' => $this->activeDefinitionForKey((string) ($user->equipped_profile_theme ?? ''), $grouped['profile_theme'] ?? []),
                'profile_featured_until' => $user->profile_featured_until,
                'is_featured' => $user->profile_featured_until !== null && $user->profile_featured_until->isFuture(),
            ],
            'owned_by_slot' => $grouped,
            'slot_labels' => $slotLabels,
        ];
    }

    private function resolveExpiry(array $unlock, ?UserProfileCosmetic $existingCosmetic, CarbonImmutable $now): ?CarbonImmutable
    {
        $durationDays = (int) ($unlock['duration_days'] ?? 0);
        if ($durationDays <= 0) {
            return null;
        }

        $base = $existingCosmetic?->expires_at && $existingCosmetic->expires_at->isFuture()
            ? CarbonImmutable::instance($existingCosmetic->expires_at)
            : $now;

        return $base->addDays($durationDays);
    }

    /**
     * @param array<int, array<string, mixed>> $slotItems
     * @return array<string, mixed>|null
     */
    private function activeDefinitionForKey(string $equippedKey, array $slotItems): ?array
    {
        if ($equippedKey === '') {
            return null;
        }

        foreach ($slotItems as $item) {
            if (($item['cosmetic_key'] ?? null) === $equippedKey && ! ($item['is_expired'] ?? false)) {
                return $item;
            }
        }

        return null;
    }

    private function equippedColumnForSlot(string $slot): ?string
    {
        return match ($slot) {
            'badge' => 'equipped_profile_badge',
            'avatar_frame' => 'equipped_avatar_frame',
            'banner' => 'equipped_profile_banner',
            'profile_title' => 'equipped_profile_title',
            'username_color' => 'equipped_username_color',
            'profile_theme' => 'equipped_profile_theme',
            default => null,
        };
    }
}
