<?php

namespace App\Support;

use App\Models\Gift;
use Illuminate\Support\Collection;

class LaunchGiftCatalog
{
    private static ?Collection $definitions = null;

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function definitions(): Collection
    {
        if (self::$definitions === null) {
            self::$definitions = collect(require database_path('seeders/data/launch_gifts.php'))
                ->map(fn (array $definition): array => $definition)
                ->values();
        }

        return collect(self::$definitions->all());
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function definitionForKey(?string $key): ?array
    {
        if (! is_string($key) || trim($key) === '') {
            return null;
        }

        /** @var array<string, mixed>|null $definition */
        $definition = self::definitions()->firstWhere('key', trim($key));

        return $definition;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function definitionForTitle(?string $title): ?array
    {
        if (! is_string($title) || trim($title) === '') {
            return null;
        }

        /** @var array<string, mixed>|null $definition */
        $definition = self::definitions()->firstWhere('title', trim($title));

        return $definition;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function definitionForGift(?Gift $gift): ?array
    {
        if (! $gift instanceof Gift) {
            return null;
        }

        return self::definitionForKey($gift->key) ?: self::definitionForTitle($gift->title);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function cosmeticDefinitions(): Collection
    {
        return self::definitions()
            ->flatMap(function (array $definition): array {
                return collect((array) ($definition['profile_unlocks'] ?? []))
                    ->map(function (array $unlock) use ($definition): array {
                        return $unlock + [
                            'source_gift_key' => $definition['key'] ?? null,
                            'source_gift_title' => $definition['title'] ?? null,
                        ];
                    })
                    ->all();
            })
            ->values();
    }

    /**
     * @param array<string, mixed> $definition
     * @return Collection<int, array<string, mixed>>
     */
    public static function profileUnlocksForDefinition(array $definition): Collection
    {
        return collect((array) ($definition['profile_unlocks'] ?? []))
            ->map(fn (array $unlock): array => $unlock)
            ->values();
    }

    /**
     * @param array<string, mixed>|null $definition
     */
    public static function isProfileDigitalDefinition(?array $definition): bool
    {
        return is_array($definition)
            && ($definition['category'] ?? null) === 'profile_digital'
            && ($definition['delivery_type'] ?? null) === 'profile';
    }
}
