<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoSurfaceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_uses_current_app_url_and_blocks_private_areas(): void
    {
        config(['app.url' => 'https://erah-esport.fr']);

        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Disallow: /console/');
        $response->assertSee('Sitemap: https://erah-esport.fr/sitemap.xml');
        $response->assertDontSee('127.0.0.1');
    }

    public function test_sitemap_xml_uses_current_app_url_and_only_canonical_paths(): void
    {
        config(['app.url' => 'https://erah-esport.fr']);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<loc>https://erah-esport.fr/aide</loc>', false);
        $response->assertSee('<loc>https://erah-esport.fr/app/clips</loc>', false);
        $response->assertDontSee('127.0.0.1');
        $response->assertDontSee('/console/clips');
        $response->assertDontSee('/faq');
    }

    public function test_html_aliases_redirect_to_canonical_routes(): void
    {
        $this->get('/index.html')
            ->assertRedirect(route('marketing.index'))
            ->assertStatus(301);

        $this->get('/boutique.html')
            ->assertRedirect('/boutique')
            ->assertStatus(301);

        $this->get('/about.html')
            ->assertRedirect(route('marketing.page', ['slug' => 'about']))
            ->assertStatus(301);
    }

    public function test_security_headers_are_applied_and_private_pages_are_not_cacheable(): void
    {
        $publicResponse = $this->get(route('marketing.index'));

        $publicResponse->assertOk();
        $publicResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $publicResponse->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $publicResponse->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');

        $user = User::factory()->create();

        $privateResponse = $this->actingAs($user)->get(route('profile.show'));

        $privateResponse->assertOk();
        $privateResponse->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');

        $cacheControl = (string) $privateResponse->headers->get('Cache-Control', '');

        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
    }
}