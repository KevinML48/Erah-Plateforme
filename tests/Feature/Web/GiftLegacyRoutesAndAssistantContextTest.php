<?php

namespace Tests\Feature\Web;

use App\Models\Gift;
use App\Models\User;
use App\Models\UserRewardWallet;
use App\Services\AI\AssistantContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftLegacyRoutesAndAssistantContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_legacy_gift_routes_redirect_to_canonical_slug_route(): void
    {
        $gift = Gift::query()->create([
            'slug' => 'sticker-pack-erah',
            'title' => 'Sticker Pack ERAH',
            'description' => 'Pack officiel.',
            'cost_points' => 180,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->get('/gift/'.$gift->routeIdentifier())
            ->assertRedirect('/app/cadeaux/'.$gift->routeIdentifier());

        $this->get('/app/gift/'.$gift->routeIdentifier())
            ->assertRedirect('/app/cadeaux/'.$gift->routeIdentifier());
    }

    public function test_authenticated_console_legacy_gift_route_redirects_to_canonical_console_route(): void
    {
        $user = User::factory()->create();
        $gift = Gift::query()->create([
            'slug' => 'hoodie-erah',
            'title' => 'Hoodie ERAH',
            'description' => 'Edition equipe.',
            'cost_points' => 950,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/console/gift/'.$gift->routeIdentifier())
            ->assertRedirect('/console/gifts/'.$gift->routeIdentifier());
    }

    public function test_assistant_context_uses_canonical_gift_routes_with_slug_identifiers(): void
    {
        $user = User::factory()->create();

        UserRewardWallet::query()->create([
            'user_id' => $user->id,
            'balance' => 500,
        ]);

        Gift::query()->create([
            'slug' => 'badge-erah',
            'title' => 'Badge ERAH',
            'description' => 'Badge membre.',
            'cost_points' => 120,
            'stock' => 5,
            'is_active' => true,
        ]);

        Gift::query()->create([
            'slug' => 'mug-erah',
            'title' => 'Mug ERAH',
            'description' => 'Mug officiel.',
            'cost_points' => 220,
            'stock' => 3,
            'is_active' => true,
        ]);

        $context = app(AssistantContextService::class)->build($user);

        $giftLinks = collect($context['user']['gift_highlights'] ?? [])->pluck('url')->all();
        $navigationLinks = collect($context['links'] ?? [])->pluck('url', 'label')->all();

        $this->assertContains('/console/gifts/badge-erah', $giftLinks);
        $this->assertContains('/console/gifts/mug-erah', $giftLinks);
        $this->assertSame('/console/gifts', $navigationLinks['Cadeaux'] ?? null);
    }
}