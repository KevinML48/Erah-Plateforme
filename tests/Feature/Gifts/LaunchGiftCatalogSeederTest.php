<?php

namespace Tests\Feature\Gifts;

use App\Models\Gift;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Support\LaunchGiftCatalog;
use Database\Seeders\LaunchGiftCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaunchGiftCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_launch_gift_catalog_source_contains_thirty_unique_entries_with_valid_costs(): void
    {
        $catalog = LaunchGiftCatalog::definitions();

        $this->assertCount(30, $catalog);
        $this->assertCount(30, $catalog->pluck('key')->unique());
        $this->assertCount(30, $catalog->pluck('title')->unique());

        $catalog->each(function (array $definition): void {
            $this->assertGreaterThan(0, (int) ($definition['cost_points'] ?? 0));
            $this->assertContains($definition['category'] ?? null, ['profile_digital', 'digital_reward', 'manual_reward', 'physical', 'premium']);
        });
    }

    public function test_launch_gift_catalog_seeder_injects_thirty_active_gifts_and_is_idempotent(): void
    {
        $this->seed(LaunchGiftCatalogSeeder::class);
        $this->seed(LaunchGiftCatalogSeeder::class);

        $activeGifts = Gift::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(30, $activeGifts);
        $this->assertCount(30, $activeGifts->pluck('key')->filter()->unique());
        $this->assertSame(
            LaunchGiftCatalog::definitions()->pluck('key')->all(),
            $activeGifts->pluck('key')->all()
        );
    }

    public function test_launch_gift_catalog_updates_existing_titles_without_creating_duplicates(): void
    {
        Gift::query()->create([
            'title' => 'Souris gaming',
            'description' => 'Ancienne version',
            'cost_points' => 999,
            'stock' => 99,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 999,
        ]);

        $this->seed(LaunchGiftCatalogSeeder::class);

        $this->assertSame(1, Gift::query()->where('title', 'Souris gaming')->count());

        $gift = Gift::query()->where('title', 'Souris gaming')->firstOrFail();
        $this->assertSame('launch-gaming-mouse', $gift->key);
        $this->assertSame(15000, (int) $gift->cost_points);
        $this->assertSame(3, (int) $gift->stock);
        $this->assertTrue((bool) $gift->is_active);
        $this->assertTrue((bool) $gift->requires_admin_validation);
    }

    public function test_launch_gift_catalog_preserves_supported_categories_for_ui(): void
    {
        $this->seed(LaunchGiftCatalogSeeder::class);

        $user = User::factory()->create();
        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);

        $response = $this->actingAs($user)->get(route('gifts.index'));

        $response->assertOk()
            ->assertSee('Profil numerique')
            ->assertSee('Digital')
            ->assertSee('Recompense manuelle')
            ->assertSee('Physique')
            ->assertSee('Premium');
    }

    public function test_premium_launch_gifts_are_long_term_low_stock_rewards(): void
    {
        $premiumDefinitions = LaunchGiftCatalog::definitions()
            ->where('category', 'premium')
            ->values();

        $this->assertCount(5, $premiumDefinitions);

        $premiumDefinitions->each(function (array $definition): void {
            $this->assertSame('premium', $definition['delivery_type'] ?? null);
            $this->assertTrue((bool) ($definition['requires_admin_validation'] ?? false));
            $this->assertLessThanOrEqual(3, (int) ($definition['stock'] ?? 0));
            $this->assertGreaterThanOrEqual(15000, (int) ($definition['cost_points'] ?? 0));
        });
    }

    public function test_only_maillot_erah_is_a_real_erah_physical_product(): void
    {
        $physicalErahProducts = LaunchGiftCatalog::definitions()
            ->where('category', 'physical')
            ->pluck('title')
            ->filter(fn (string $title): bool => str_contains($title, 'ERAH'))
            ->values()
            ->all();

        $this->assertSame(['Maillot ERAH officiel'], $physicalErahProducts);
    }
}
