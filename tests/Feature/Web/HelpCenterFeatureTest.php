<?php

namespace Tests\Feature\Web;

use App\Models\HelpCategory;
use App\Models\User;
use Database\Seeders\HelpCenterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpCenterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_help_center_page_is_available(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->get(route('help.index'))
            ->assertOk()
            ->assertViewIs('pages.help.index')
            ->assertSee('Comprendre ERAH, explorer ses modules et savoir quoi faire ensuite.')
            ->assertSee('FAQ detaillee')
            ->assertSee('Assistant ERAH');
    }

    public function test_faq_route_redirects_to_canonical_help_page(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->get(route('marketing.faq'))
            ->assertRedirect(route('help.index').'#faq-center');
    }

    public function test_help_category_and_article_routes_redirect_to_the_single_page_faq(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->get(route('help.categories.show', 'matchs-et-paris'))
            ->assertRedirect(route('help.index', ['category' => 'matchs-et-paris']).'#faq-center');

        $this->get(route('help.articles.show', 'placer-un-pari-avant-le-verrouillage'))
            ->assertRedirect(route('help.index', ['article' => 'placer-un-pari-avant-le-verrouillage']).'#faq-center');
    }

    public function test_console_help_is_available_without_auth(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->get(route('console.help'))
            ->assertOk()
            ->assertViewIs('pages.help.index')
            ->assertSee('Trouver la bonne reponse sans quitter votre espace.')
            ->assertSee('Dashboard')
            ->assertSee('Missions');
    }

    public function test_help_assistant_page_is_available(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->get(route('help.assistant.page'))
            ->assertOk()
            ->assertViewIs('pages.help.assistant')
            ->assertSee('Posez une question a la plateforme.')
            ->assertSee('Assistant ERAH');
    }

    public function test_help_assistant_returns_a_knowledge_base_answer(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->postJson(route('help.assistant.ask'), [
            'message' => 'Comment gagner des points avec les missions ?',
        ])
            ->assertOk()
            ->assertJsonPath('data.answer', 'Les missions quotidiennes structurent l activite et donnent des recompenses directes en points et progression.')
            ->assertJsonPath('data.sources.0.type', 'article');
    }

    public function test_help_assistant_can_add_user_context_when_authenticated(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $user = User::factory()->create([
            'role' => User::ROLE_USER,
            'bio' => null,
        ]);

        $this->actingAs($user)
            ->postJson(route('help.assistant.ask'), [
                'message' => 'Comment améliorer mon profil ?',
            ])
            ->assertOk()
            ->assertJsonPath('data.user_context.league', 'Bronze');
    }

    public function test_admin_can_manage_help_content(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $category = HelpCategory::query()->where('slug', 'commencer-sur-erah')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.help.articles.store'), [
                'help_category_id' => $category->id,
                'title' => 'Article admin test',
                'slug' => 'article-admin-test',
                'summary' => 'Resume court pour le test.',
                'body' => "Contenu assez long pour valider le formulaire admin du centre d'aide.",
                'short_answer' => 'Reponse courte test.',
                'keywords' => 'admin, help, test',
                'status' => 'published',
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 120,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('help_articles', [
            'slug' => 'article-admin-test',
            'title' => 'Article admin test',
            'status' => 'published',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.help.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Help/Admin/Index')
                ->where('page.overview.categories', 9));
    }

    public function test_help_center_seeder_populates_realistic_core_content(): void
    {
        $this->seed(HelpCenterSeeder::class);

        $this->assertDatabaseCount('help_categories', 9);
        $this->assertDatabaseCount('help_tour_steps', 6);
        $this->assertDatabaseHas('help_glossary_terms', [
            'slug' => 'reward-wallet',
        ]);
        $this->assertDatabaseHas('help_articles', [
            'slug' => 'comprendre-le-role-de-la-plateforme',
        ]);
    }
}
