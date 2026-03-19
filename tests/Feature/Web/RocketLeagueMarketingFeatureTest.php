<?php

namespace Tests\Feature\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RocketLeagueMarketingFeatureTest extends TestCase
{
    use RefreshDatabase;

    private const ROCKET_LEAGUE_VIDEO_ASSET = 'template/assets/vids/rocket-league/rendu-final.mp4';

    public function test_canonical_rocket_league_page_displays_team_content(): void
    {
        $this->get(route('marketing.rocket-league'))
            ->assertOk()
            ->assertSeeText('Rocket League')
            ->assertSee('Notre &eacute;quipe Rocket League', false)
            ->assertSeeText('Joueurs et staff')
            ->assertSeeText('Rejoins')
            ->assertSeeText('17Saizen')
            ->assertSeeText('AnoriQK')
            ->assertSeeText('MayKooRL')
            ->assertSeeText('BeastBound')
            ->assertSeeText('Zhin')
            ->assertSee('https://x.com/17saizen', false)
            ->assertSee(asset(self::ROCKET_LEAGUE_VIDEO_ASSET), false)
            ->assertSee('/template/assets/img/rocket-league/saizen.jpg', false)
            ->assertSee('/template/assets/img/rocket-league/anoriq.jpg', false)
            ->assertDontSeeText('18/03')
            ->assertDontSee('valorant-VCL.html')
            ->assertDontSeeText('Izana')
            ->assertDontSeeText('Drazix');
    }

    public function test_homepage_surfaces_rocket_league_entry_with_canonical_link(): void
    {
        $this->get(route('marketing.index'))
            ->assertOk()
            ->assertSee('Rocket League')
            ->assertSee(route('marketing.rocket-league'), false)
            ->assertSee(asset(self::ROCKET_LEAGUE_VIDEO_ASSET), false)
            ->assertDontSee('Annonce live 18/03')
            ->assertDontSee('>18/03<', false);
    }

    public function test_legacy_rocket_league_urls_redirect_to_canonical_route(): void
    {
        foreach ([
            '/18-03-live',
            '/valorant-vcl',
            '/valorant-VCL',
            '/valorant-vcl.html',
            '/valorant-VCL.html',
            '/valorant-vcl-18-03-live',
            '/rocket-league.html',
        ] as $legacyUrl) {
            $this->get($legacyUrl)
                ->assertRedirect(route('marketing.rocket-league'))
                ->assertStatus(301);
        }
    }
}