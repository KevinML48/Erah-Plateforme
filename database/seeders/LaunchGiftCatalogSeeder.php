<?php

namespace Database\Seeders;

use App\Models\Gift;
use App\Support\LaunchGiftCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class LaunchGiftCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = LaunchGiftCatalog::definitions()
            ->map(fn (array $definition): array => $this->normalizeDefinition($definition))
            ->values();

        $this->assertCatalog($catalog);

        foreach ($catalog as $definition) {
            $gift = Gift::query()
                ->where('key', $definition['key'])
                ->orWhere('title', $definition['title'])
                ->first();

            if ($gift) {
                $gift->fill($definition)->save();
            } else {
                Gift::query()->create($definition);
            }
        }

        Gift::query()
            ->where(function ($query) use ($catalog): void {
                $query
                    ->whereNull('key')
                    ->orWhereNotIn('key', $catalog->pluck('key')->all());
            })
            ->update([
                'is_active' => false,
                'is_featured' => false,
                'updated_at' => now(),
            ]);
    }

    /**
     * @param array<string, mixed> $definition
     * @return array<string, mixed>
     */
    private function normalizeDefinition(array $definition): array
    {
        $shortDescription = $this->nullableString($definition['short_description'] ?? null);
        $longDescription = $this->nullableString($definition['long_description'] ?? null);

        $description = collect([$shortDescription, $longDescription])
            ->filter(fn (?string $value): bool => $value !== null)
            ->implode("\n\n");

        return [
            'key' => (string) $definition['key'],
            'title' => (string) $definition['title'],
            'description' => $description !== '' ? $description : 'Recompense membre disponible contre vos points plateforme.',
            'category' => $this->nullableString($definition['category'] ?? null),
            'type' => $this->nullableString($definition['type'] ?? null),
            'delivery_type' => $this->nullableString($definition['delivery_type'] ?? null),
            'image_url' => $this->nullableString($definition['image_url'] ?? null),
            'cost_points' => max(1, (int) ($definition['cost_points'] ?? 0)),
            'stock' => max(0, (int) ($definition['stock'] ?? 0)),
            'is_active' => (bool) ($definition['is_active'] ?? true),
            'is_featured' => (bool) ($definition['is_featured'] ?? false),
            'sort_order' => max(0, (int) ($definition['sort_order'] ?? 0)),
            'requires_admin_validation' => (bool) ($definition['requires_admin_validation'] ?? false),
            'metadata' => [
                'short_description' => $shortDescription,
                'long_description' => $longDescription,
                'is_repeatable' => (bool) ($definition['is_repeatable'] ?? true),
                'profile_unlocks' => array_values((array) ($definition['profile_unlocks'] ?? [])),
            ],
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $catalog
     */
    private function assertCatalog(Collection $catalog): void
    {
        if ($catalog->count() !== 30) {
            throw new RuntimeException('Le catalogue de lancement cadeaux doit contenir exactement 30 cadeaux.');
        }

        $rawDefinitions = LaunchGiftCatalog::definitions();
        $duplicateKeys = $rawDefinitions->pluck('key')->duplicates()->unique()->values();
        if ($duplicateKeys->isNotEmpty()) {
            throw new RuntimeException('Cles cadeaux dupliquees: '.$duplicateKeys->implode(', '));
        }

        $duplicateTitles = $rawDefinitions->pluck('title')->duplicates()->unique()->values();
        if ($duplicateTitles->isNotEmpty()) {
            throw new RuntimeException('Titres cadeaux dupliques: '.$duplicateTitles->implode(', '));
        }

        $invalidCategories = $rawDefinitions
            ->pluck('category')
            ->filter(fn (mixed $category): bool => ! in_array($category, ['profile_digital', 'digital_reward', 'manual_reward', 'physical', 'premium'], true))
            ->unique()
            ->values();

        if ($invalidCategories->isNotEmpty()) {
            throw new RuntimeException('Categories cadeaux invalides: '.$invalidCategories->implode(', '));
        }

        $invalidKeys = $rawDefinitions
            ->pluck('key')
            ->filter(fn (mixed $key): bool => ! is_string($key) || trim($key) === '')
            ->values();

        if ($invalidKeys->isNotEmpty()) {
            throw new RuntimeException('Chaque cadeau doit avoir une cle stable.');
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
