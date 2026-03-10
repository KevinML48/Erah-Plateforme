<?php

namespace App\Services;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\HelpGlossaryTerm;
use App\Models\HelpTourStep;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HelpCenterService
{
    private const CACHE_VERSION_KEY = 'help-center:version';

    public function __construct(
        private readonly RankService $rankService,
    ) {
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function landingBuckets(): array
    {
        return [
            ['value' => 'getting_started', 'label' => 'Bien demarrer'],
            ['value' => 'understanding_platform', 'label' => 'Comprendre la plateforme'],
            ['value' => 'technical', 'label' => 'Questions techniques'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function publicIndex(
        ?string $search = null,
        ?string $categorySlug = null,
        ?string $articleSlug = null,
        ?User $user = null,
    ): array {
        return $this->hubPayload($search, $categorySlug, $articleSlug, $user, 'public');
    }

    /**
     * @return array<string, mixed>
     */
    public function consoleIndex(
        ?string $search = null,
        ?string $categorySlug = null,
        ?string $articleSlug = null,
        ?User $user = null,
    ): array {
        return $this->hubPayload($search, $categorySlug, $articleSlug, $user, 'console');
    }

    /**
     * @return array<string, mixed>
     */
    public function adminIndex(): array
    {
        return [
            'overview' => [
                'categories' => HelpCategory::query()->count(),
                'published_categories' => HelpCategory::query()->published()->count(),
                'articles' => HelpArticle::query()->count(),
                'published_articles' => HelpArticle::query()->published()->count(),
                'featured_faqs' => HelpArticle::query()->where('is_faq', true)->published()->count(),
                'glossary_terms' => HelpGlossaryTerm::query()->count(),
                'tour_steps' => HelpTourStep::query()->count(),
            ],
            'options' => [
                'statuses' => HelpArticle::statuses(),
                'landingBuckets' => $this->landingBuckets(),
            ],
            'categories' => HelpCategory::query()
                ->withCount(['articles as articles_count'])
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get()
                ->map(fn (HelpCategory $category) => [
                    ...$category->only([
                        'id',
                        'title',
                        'slug',
                        'description',
                        'intro',
                        'icon',
                        'landing_bucket',
                        'tutorial_video_url',
                        'status',
                        'sort_order',
                    ]),
                    'articles_count' => $category->articles_count,
                    'update_url' => route('admin.help.categories.update', $category),
                    'delete_url' => route('admin.help.categories.destroy', $category),
                ])
                ->values()
                ->all(),
            'articles' => HelpArticle::query()
                ->with('category')
                ->orderByDesc('status')
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->get()
                ->map(fn (HelpArticle $article) => [
                    ...$article->only([
                        'id',
                        'help_category_id',
                        'title',
                        'slug',
                        'summary',
                        'body',
                        'short_answer',
                        'tutorial_video_url',
                        'cta_label',
                        'cta_url',
                        'status',
                        'is_featured',
                        'is_faq',
                        'sort_order',
                    ]),
                    'keywords' => implode(', ', $article->keywords ?? []),
                    'category' => $article->category?->only(['id', 'title', 'slug']),
                    'update_url' => route('admin.help.articles.update', $article),
                    'delete_url' => route('admin.help.articles.destroy', $article),
                ])
                ->values()
                ->all(),
            'glossary' => HelpGlossaryTerm::query()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('term')
                ->get()
                ->map(fn (HelpGlossaryTerm $term) => [
                    ...$term->only([
                        'id',
                        'term',
                        'slug',
                        'definition',
                        'short_answer',
                        'status',
                        'is_featured',
                        'sort_order',
                    ]),
                    'update_url' => route('admin.help.glossary.update', $term),
                    'delete_url' => route('admin.help.glossary.destroy', $term),
                ])
                ->values()
                ->all(),
            'tourSteps' => HelpTourStep::query()
                ->orderBy('step_number')
                ->get()
                ->map(fn (HelpTourStep $step) => [
                    ...$step->only([
                        'id',
                        'step_number',
                        'title',
                        'summary',
                        'body',
                        'visual_title',
                        'visual_body',
                        'cta_label',
                        'cta_url',
                        'tutorial_video_url',
                        'status',
                        'sort_order',
                    ]),
                    'update_url' => route('admin.help.tour-steps.update', $step),
                    'delete_url' => route('admin.help.tour-steps.destroy', $step),
                ])
                ->values()
                ->all(),
            'endpoints' => [
                'categories_store' => route('admin.help.categories.store'),
                'articles_store' => route('admin.help.articles.store'),
                'glossary_store' => route('admin.help.glossary.store'),
                'tour_steps_store' => route('admin.help.tour-steps.store'),
            ],
        ];
    }

    public function invalidate(): void
    {
        if (! Cache::has(self::CACHE_VERSION_KEY)) {
            Cache::forever(self::CACHE_VERSION_KEY, 1);
        }

        Cache::increment(self::CACHE_VERSION_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    private function hubPayload(
        ?string $search,
        ?string $categorySlug,
        ?string $articleSlug,
        ?User $user,
        string $mode,
    ): array {
        $search = trim((string) $search);
        $categorySlug = trim((string) $categorySlug);
        $articleSlug = trim((string) $articleSlug);

        $base = $this->cached('hub:'.$mode, fn (): array => $this->buildHubBase($mode));
        $filteredFaqItems = $this->filterFaqItems(
            collect($base['faq']['items']),
            $search,
            $categorySlug,
        );
        $activeArticle = $articleSlug !== ''
            ? collect($base['faq']['items'])->firstWhere('slug', $articleSlug)
            : null;

        if ($activeArticle !== null && ! $filteredFaqItems->contains(fn (array $item): bool => $item['slug'] === $activeArticle['slug'])) {
            $filteredFaqItems = collect([$activeArticle])->merge($filteredFaqItems);
        }

        return [
            ...$base,
            'mode' => $mode,
            'filters' => [
                'search' => $search !== '' ? $search : null,
                'category' => $categorySlug !== '' ? $categorySlug : null,
                'article' => $articleSlug !== '' ? $articleSlug : null,
            ],
            'overview' => $this->buildOverview($base),
            'discovery' => $this->buildDiscoveryCards($mode),
            'sections' => $this->buildSectionGroups($base['categories']),
            'featureHighlights' => $this->buildFeatureHighlights($base['featureGrid']['items']),
            'featuredFaqs' => collect($base['faq']['featured'])->take(4)->values()->all(),
            'starterPaths' => $this->buildStarterPaths($mode),
            'searchResults' => $search !== '' ? $filteredFaqItems->take(6)->values()->all() : [],
            'hero' => [
                ...$base['hero'],
                'tour_url' => '#tour-guide',
            ],
            'video' => [
                ...$base['video'],
                'summary' => $base['video']['description'] ?? null,
            ],
            'faq' => [
                ...$base['faq'],
                'filtered_items' => $filteredFaqItems->values()->all(),
                'active_category' => $categorySlug !== '' ? $categorySlug : null,
                'active_article' => $activeArticle['slug'] ?? null,
            ],
            'assistant' => [
                ...$base['assistant'],
                'page_url' => $mode === 'console'
                    ? route('console.help.assistant')
                    : route('help.assistant.page'),
                'user_preview' => $this->buildUserPreview($user),
            ],
            'footerCta' => [
                ...$base['footerCta'],
                'faq_url' => route('help.index').'#faq-center',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildHubBase(string $mode): array
    {
        $categories = HelpCategory::query()
            ->published()
            ->withCount(['publishedArticles as articles_count'])
            ->with(['articles' => fn ($query) => $query
                ->published()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('title')])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $articles = HelpArticle::query()
            ->published()
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get();

        $tourSteps = HelpTourStep::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('step_number')
            ->get();

        $glossary = HelpGlossaryTerm::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('term')
            ->limit(10)
            ->get();

        $articlesBySlug = $articles->keyBy('slug');
        $videoUrl = $articles->pluck('tutorial_video_url')->filter()->first()
            ?? $categories->pluck('tutorial_video_url')->filter()->first()
            ?? $tourSteps->pluck('tutorial_video_url')->filter()->first();

        return [
            'hero' => $this->buildHero($mode, $categories, $articles, $tourSteps),
            'sectionNav' => $this->buildSectionNav($mode),
            'intro' => $this->buildIntroSection(),
            'starterJourney' => [
                'eyebrow' => 'Comment bien commencer',
                'title' => 'Un parcours simple pour comprendre ERAH sans se disperser.',
                'description' => "Le plus efficace est de suivre un fil logique: comprendre le produit, ouvrir son espace, activer les modules qui vous concernent puis revenir regulierement.",
                'steps' => $tourSteps->map(fn (HelpTourStep $step) => $this->presentTourStep($step))->values()->all(),
            ],
            'featureGrid' => $this->buildFeatureGrid($mode),
            'categories' => $categories
                ->map(fn (HelpCategory $category) => $this->presentCategory($category))
                ->values()
                ->all(),
            'quickQuestions' => $this->buildQuickQuestions($articlesBySlug),
            'video' => [
                'title' => 'Tutoriel video et reperes rapides',
                'description' => "La video doit aider a prendre ses marques sans remplacer le contenu ecrit. Si aucun tutoriel n'est encore branche, le bloc reste utile avec les reperes a retenir.",
                'url' => $videoUrl,
                'embed_url' => $this->toEmbedVideoUrl($videoUrl),
                'fallback' => "Ajoutez un lien YouTube ou Vimeo depuis l'administration pour brancher un vrai tutoriel a cette zone.",
                'highlights' => [
                    'Comprendre ce qui reste public sans compte et ce qui se debloque apres connexion.',
                    'Identifier les modules a ouvrir en premier selon votre usage: matchs, clips, missions, duels ou cadeaux.',
                    'Savoir ou trouver vos points, votre progression, vos notifications et votre profil public.',
                ],
            ],
            'faq' => [
                'eyebrow' => 'FAQ centrale',
                'title' => 'Une seule base de reponses pour expliquer les mecanismes, les usages et les bonnes pratiques.',
                'description' => "Chaque reponse peut rester courte si le sujet est simple ou devenir plus detaillee avec etapes, conseils et liens utiles quand c'est necessaire.",
                'categories' => $categories
                    ->map(fn (HelpCategory $category) => [
                        'slug' => $category->slug,
                        'title' => $category->title,
                        'count' => (int) ($category->articles_count ?? 0),
                    ])
                    ->values()
                    ->all(),
                'featured' => $articles
                    ->where('is_faq', true)
                    ->take(6)
                    ->map(fn (HelpArticle $article) => $this->presentQuestion($article))
                    ->values()
                    ->all(),
                'items' => $articles
                    ->map(fn (HelpArticle $article) => $this->presentQuestion($article))
                    ->values()
                    ->all(),
            ],
            'glossary' => $glossary
                ->map(fn (HelpGlossaryTerm $term) => $this->presentGlossary($term))
                ->values()
                ->all(),
            'assistant' => [
                'title' => 'Assistant ERAH',
                'status' => config('help-center.assistant.mode') === 'knowledge_base'
                    ? 'Assistant knowledge base'
                    : 'Assistant prepare',
                'description' => "L'assistant repond d'abord depuis la base de connaissance du centre d'aide. Il peut ensuite tenir compte de votre situation reelle si vous etes connecte.",
                'placeholder' => (string) config('help-center.assistant.placeholder', 'Posez votre question sur la plateforme, les points, les missions ou votre profil...'),
                'endpoint' => route('help.assistant.ask'),
                'mode' => (string) config('help-center.assistant.mode', 'knowledge_base'),
                'suggested_prompts' => [
                    'Comment fonctionne la plateforme ?',
                    'Comment gagner des points ?',
                    'Comment voir les matchs a venir ?',
                    'Comment marchent les missions ?',
                    'Comment recuperer des cadeaux ?',
                    'Comment ameliorer mon profil ?',
                ],
                'disclaimer' => "L'assistant ne doit pas halluciner. S'il n'a pas de reponse fiable, il vous le dit et vous renvoie vers la meilleure section.",
            ],
            'footerCta' => $this->buildFooterCta($mode),
            'consoleLinks' => $mode === 'console' ? $this->buildConsoleLinks() : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildHero(string $mode, Collection $categories, Collection $articles, Collection $tourSteps): array
    {
        return [
            'eyebrow' => $mode === 'console' ? 'Aide in-app' : 'Centre d aide / FAQ',
            'title' => $mode === 'console'
                ? 'Trouver la bonne reponse sans quitter votre espace.'
                : 'Comprendre ERAH, explorer ses modules et savoir quoi faire ensuite.',
            'subtitle' => $mode === 'console'
                ? "Le hub d'aide central pour retrouver vite une reponse, relancer un parcours et repartir vers la bonne page du produit."
                : "Une seule page pour decouvrir la logique de la plateforme, parcourir la FAQ centrale, comprendre les vrais modules et utiliser un assistant cadre par la base de connaissance.",
            'microcopy' => "La lecture reste publique. Les actions qui modifient votre progression, votre profil ou vos ressources demandent un compte connecte.",
            'search_placeholder' => 'Rechercher une question, un module, une action ou un mot-cle',
            'primary_cta' => [
                'label' => $mode === 'console' ? 'Retour au dashboard' : 'Commencer la visite',
                'href' => $mode === 'console' ? route('dashboard') : '#starter-journey',
            ],
            'secondary_cta' => [
                'label' => 'Aller a la FAQ',
                'href' => '#faq-center',
            ],
            'stats' => [
                ['label' => 'Categories', 'value' => (string) $categories->count()],
                ['label' => 'Questions reelles', 'value' => (string) $articles->where('is_faq', true)->count()],
                ['label' => 'Etapes guidees', 'value' => (string) $tourSteps->count()],
            ],
            'panel' => [
                'title' => 'Ce que vous pouvez faire ici',
                'items' => [
                    'Comprendre la plateforme avant de participer.',
                    'Retrouver une reponse claire sur les matchs, missions, clips, duels, cadeaux, profil et notifications.',
                    "Poser une question a l'assistant en bas de page sans quitter le centre d'aide.",
                ],
            ],
            'search_tags' => [
                'matchs',
                'paris',
                'missions',
                'points',
                'clips',
                'duels',
                'cadeaux',
                'profil',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildSectionNav(string $mode): array
    {
        return [
            ['label' => 'Decouvrir', 'href' => '#discover-erah'],
            ['label' => 'Bien commencer', 'href' => '#starter-journey'],
            ['label' => 'Fonctionnalites', 'href' => '#platform-features'],
            ['label' => 'Questions rapides', 'href' => '#quick-questions'],
            ['label' => 'FAQ', 'href' => '#faq-center'],
            ['label' => 'Video', 'href' => '#video-help'],
            ['label' => 'Assistant', 'href' => '#assistant-panel'],
            ['label' => $mode === 'console' ? 'Dashboard' : 'Matchs', 'href' => $mode === 'console' ? route('dashboard') : route('matches.index')],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildIntroSection(): array
    {
        return [
            'eyebrow' => 'Decouvrir ERAH',
            'title' => "Une plateforme esport communautaire qui relie lecture publique, participation, progression et avantages concrets.",
            'description' => "ERAH n'est pas un simple site vitrine. Le produit relie le suivi du club, l'activite communautaire, la progression par XP, l'usage des points et les recompenses.",
            'panels' => [
                [
                    'title' => "Ce qu'est ERAH",
                    'description' => "Un hub qui permet de suivre les contenus du club, les matchs, les profils publics et la vie communautaire avant meme de creer un compte.",
                    'items' => ['Lecture publique sans friction', 'Contenus et profils visibles', 'Base de connaissance claire'],
                ],
                [
                    'title' => 'Ce que le compte debloque',
                    'description' => "Des que vous voulez interagir, miser, commenter, gagner des points ou personnaliser votre profil, le compte devient indispensable.",
                    'items' => ['Dashboard personnel', 'Missions, duels et paris', 'Progression, cadeaux et notifications'],
                ],
                [
                    'title' => 'Pourquoi la plateforme existe',
                    'description' => "Donner un cadre lisible a l'engagement autour du club: regarder, interagir, progresser, comparer, puis recuperer des avantages utiles.",
                    'items' => ['Points et XP', 'Modules communautaires', 'Recompenses concretes'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFeatureGrid(string $mode): array
    {
        return [
            'eyebrow' => 'Comprendre la plateforme',
            'title' => 'Les grands modules a connaitre pour lire correctement le produit.',
            'description' => "Chaque carte explique une vraie surface du repo. L'objectif n'est pas d'empiler des labels, mais de montrer comment les briques se relient entre elles.",
            'items' => [
                $this->featureItem(
                    'matchs',
                    'Matchs et paris',
                    'Suivre les rencontres, comprendre le lock et lire le reglement des paris.',
                    ['Calendrier et statuts', 'Paris avant verrouillage', 'Historique et reglement'],
                    route('matches.index'),
                    'Voir les matchs',
                    'Public puis membre'
                ),
                $this->featureItem(
                    'missions',
                    'Missions, points et progression',
                    'Le moteur qui oriente l activite et transforme les usages utiles en progression lisible.',
                    ['Daily et activite guidee', 'Points pour les modules', 'XP pour les ligues'],
                    $mode === 'console' ? route('missions.index') : route('login'),
                    $mode === 'console' ? 'Voir les missions' : 'Se connecter',
                    'Compte requis'
                ),
                $this->featureItem(
                    'clips',
                    'Clips et interactions',
                    'Voir les contenus, participer a la communaute et conserver ses favoris.',
                    ['Feed clips', 'Likes, favoris, commentaires', 'Partages et visibilite membre'],
                    route('clips.index'),
                    'Explorer les clips',
                    'Public puis membre'
                ),
                $this->featureItem(
                    'leaderboards',
                    'Classements, duels et profil',
                    'Lire votre place, suivre la competition communautaire et valoriser votre profil public.',
                    ['Ligues et XP', 'Duels personnels', 'Profil public visible'],
                    route('leaderboards.index'),
                    'Voir les classements',
                    'Lecture publique'
                ),
                $this->featureItem(
                    'rewards',
                    'Cadeaux et reward wallet',
                    'Transformer votre activite utile en recompenses concretes et suivre vos redemptions.',
                    ['Reward wallet', 'Catalogue cadeaux', 'Suivi de redemptions'],
                    $mode === 'console' ? route('gifts.index') : route('login'),
                    $mode === 'console' ? 'Voir les cadeaux' : 'Creer un compte',
                    'Compte requis'
                ),
                $this->featureItem(
                    'profile',
                    'Profil, notifications et reglages',
                    'Rester visible, proprement informe et maitriser votre presence sur la plateforme.',
                    ['Profil public', 'Avis membre', 'Preferences de notifications'],
                    $mode === 'console' ? route('profile.show') : route('login'),
                    $mode === 'console' ? 'Ouvrir mon profil' : 'Se connecter',
                    'Compte requis'
                ),
            ],
        ];
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    private function featureItem(
        string $key,
        string $title,
        string $description,
        array $bullets,
        string $href,
        string $ctaLabel,
        string $access
    ): array {
        return [
            'key' => $key,
            'title' => $title,
            'description' => $description,
            'bullets' => $bullets,
            'href' => $href,
            'cta_label' => $ctaLabel,
            'access' => $access,
        ];
    }

    /**
     * @param Collection<string, HelpArticle> $articlesBySlug
     * @return array<int, array<string, mixed>>
     */
    private function buildQuickQuestions(Collection $articlesBySlug): array
    {
        $slugs = [
            'comprendre-le-role-de-la-plateforme',
            'gagner-des-points-avec-les-missions-quotidiennes',
            'placer-un-pari-avant-le-verrouillage',
            'voir-un-clip-liker-et-ajouter-en-favoris',
            'comprendre-les-duels-et-leur-classement',
            'utiliser-le-reward-wallet-et-demander-un-cadeau',
        ];

        return collect($slugs)
            ->map(fn (string $slug) => $articlesBySlug->get($slug))
            ->filter()
            ->map(fn (HelpArticle $article) => [
                'id' => $article->id,
                'title' => $article->title,
                'question' => $article->summary ?: $article->title,
                'answer' => $article->short_answer ?: $article->summary,
                'href' => route('help.index', ['article' => $article->slug]).'#faq-center',
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFooterCta(string $mode): array
    {
        if ($mode === 'console') {
            return [
                'eyebrow' => 'Toujours au meme endroit',
                'title' => "Gardez ce hub d'aide comme point de retour quand un module vous bloque.",
                'description' => "Vous pouvez passer des questions a l'action sans quitter l'ecosysteme ERAH.",
                'primary_label' => 'Retour au dashboard',
                'primary_url' => route('dashboard'),
                'secondary_label' => 'Voir mes notifications',
                'secondary_url' => route('notifications.index'),
            ];
        }

        return [
            'eyebrow' => 'Passer de la lecture a l action',
            'title' => 'Pret a participer vraiment a la plateforme ?',
            'description' => 'Creez un compte pour commenter, miser, suivre vos missions, accumuler des ressources et apparaitre dans les classements.',
            'login_label' => 'Connexion',
            'login_url' => route('login'),
            'register_label' => 'Creer un compte',
            'register_url' => route('register'),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildConsoleLinks(): array
    {
        return [
            ['title' => 'Dashboard', 'description' => 'Revenir a votre vue d ensemble et aux modules prioritaires.', 'url' => route('dashboard')],
            ['title' => 'Matchs', 'description' => 'Suivre les rencontres, les statuts et les paris disponibles.', 'url' => route('matches.index')],
            ['title' => 'Missions', 'description' => 'Verifier vos missions quotidiennes, hebdos et activites utiles.', 'url' => route('missions.index')],
            ['title' => 'Clips', 'description' => 'Continuer vos interactions communautaires et vos favoris.', 'url' => route('clips.index')],
            ['title' => 'Classements', 'description' => 'Voir votre ligue, votre position et les autres membres.', 'url' => route('leaderboards.index')],
            ['title' => 'Duels', 'description' => 'Suivre vos defis en cours et votre competition separee.', 'url' => route('duels.index')],
            ['title' => 'Cadeaux', 'description' => 'Consulter votre reward wallet et le catalogue disponible.', 'url' => route('gifts.index')],
            ['title' => 'Profil', 'description' => 'Completer votre profil, vos liens publics et vos infos utiles.', 'url' => route('profile.show')],
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     * @return Collection<int, array<string, mixed>>
     */
    private function filterFaqItems(Collection $items, string $search, string $categorySlug): Collection
    {
        $query = Str::of($search)->lower()->ascii()->squish()->toString();

        return $items->filter(function (array $item) use ($query, $categorySlug): bool {
            if ($categorySlug !== '' && (($item['category']['slug'] ?? null) !== $categorySlug)) {
                return false;
            }

            if ($query === '') {
                return true;
            }

            $haystack = Str::of(implode(' ', array_filter([
                $item['title'] ?? null,
                $item['summary'] ?? null,
                $item['short_answer'] ?? null,
                $item['body'] ?? null,
                $item['category']['title'] ?? null,
                implode(' ', $item['keywords'] ?? []),
            ])))->lower()->ascii()->squish()->toString();

            return Str::contains($haystack, $query);
        })->values();
    }

    /**
     * @param array<string, mixed> $base
     * @return array<string, int>
     */
    private function buildOverview(array $base): array
    {
        return [
            'categories' => count($base['categories'] ?? []),
            'faqs' => count($base['faq']['items'] ?? []),
            'steps' => count($base['starterJourney']['steps'] ?? []),
            'glossary' => count($base['glossary'] ?? []),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildDiscoveryCards(string $mode): array
    {
        return [
            [
                'eyebrow' => 'Comprendre',
                'title' => 'Lire la plateforme avant meme de participer.',
                'description' => "Le visiteur peut explorer les contenus publics, les profils, les clips, les matchs et les classements sans forcer la creation d'un compte.",
                'items' => ['Lecture publique', 'Profils visibles', 'Rythme de decouverte simple'],
                'cta_label' => 'Voir les clips',
                'cta_url' => route('clips.index'),
            ],
            [
                'eyebrow' => 'Activer',
                'title' => 'Le compte debloque les vraies actions utiles.',
                'description' => "Des que vous voulez commenter, miser, faire des missions, progresser ou utiliser le reward wallet, le compte devient le point de passage naturel.",
                'items' => ['Dashboard personnel', 'Progression par activite', 'Interactions et ressources'],
                'cta_label' => $mode === 'console' ? 'Ouvrir le dashboard' : 'Se connecter',
                'cta_url' => $mode === 'console' ? route('dashboard') : route('login'),
            ],
            [
                'eyebrow' => 'Rester',
                'title' => 'Une boucle complete entre contenu, competition et recompenses.',
                'description' => "ERAH relie les matchs, les paris, les clips, les missions, les duels, les notifications et les cadeaux dans un meme espace coherent.",
                'items' => ['Modules relies', 'XP et points visibles', 'Rewards concrets'],
                'cta_label' => 'Voir l assistant',
                'cta_url' => $mode === 'console' ? route('console.help.assistant') : route('help.assistant.page'),
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @return array<int, array<string, mixed>>
     */
    private function buildSectionGroups(array $categories): array
    {
        return collect($this->landingBuckets())
            ->map(function (array $bucket) use ($categories): array {
                $items = collect($categories)
                    ->filter(fn (array $category): bool => ($category['landing_bucket'] ?? null) === $bucket['value'])
                    ->values()
                    ->all();

                return [
                    'title' => $bucket['label'],
                    'description' => match ($bucket['value']) {
                        'getting_started' => "Tout ce qu'il faut pour creer son compte, comprendre le dashboard et prendre ses premiers repères.",
                        'understanding_platform' => "Les vraies mecaniques produit: matchs, paris, points, progression, clips, duels, classements et cadeaux.",
                        default => "Les zones qui servent quand un utilisateur bloque sur un detail, une notification, un wallet ou un comportement technique.",
                    },
                    'items' => $items,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, string>>
     */
    private function buildFeatureHighlights(array $items): array
    {
        return collect($items)
            ->take(4)
            ->map(fn (array $item): array => [
                'badge' => $item['access'] ?? 'Module',
                'title' => $item['title'],
                'description' => $item['description'],
                'url' => $item['href'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildStarterPaths(string $mode): array
    {
        return [
            [
                'title' => 'Tu decouvres ERAH',
                'description' => "Commence par les zones publiques pour comprendre la logique generale avant de te connecter.",
                'items' => ['Lire les clips', 'Voir les matchs a venir', 'Parcourir les profils publics'],
                'cta_label' => 'Explorer les matchs',
                'cta_url' => route('matches.index'),
            ],
            [
                'title' => 'Tu viens de creer ton compte',
                'description' => "Passe d abord par les modules qui structurent ta progression: dashboard, missions, profil et favoris.",
                'items' => ['Ouvrir le dashboard', 'Lire les missions', 'Completer le profil'],
                'cta_label' => $mode === 'console' ? 'Voir le dashboard' : 'Connexion',
                'cta_url' => $mode === 'console' ? route('dashboard') : route('login'),
            ],
            [
                'title' => 'Tu veux progresser regulierement',
                'description' => "Le bon rythme vient des missions, des interactions sur les clips, des duels, des matchs et du suivi des notifications.",
                'items' => ['Revenir souvent', 'Verifier ses notifications', 'Utiliser le reward wallet'],
                'cta_label' => $mode === 'console' ? 'Voir les cadeaux' : 'Voir l assistant',
                'cta_url' => $mode === 'console' ? route('gifts.index') : route('help.assistant.page'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildUserPreview(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $user->loadMissing(['progress', 'wallet', 'rewardWallet', 'clubReview', 'supportSubscriptions']);

        $progress = $user->progress;
        $league = $this->rankService->currentLeague($user);
        $points = (int) ($user->wallet?->balance ?? 0);
        $rewardBalance = (int) ($user->rewardWallet?->balance ?? 0);
        $suggestions = collect();

        if (blank($user->bio)) {
            $suggestions->push('Ajoutez une bio courte pour rendre votre profil public plus lisible.');
        }

        if (blank($user->avatar_url)) {
            $suggestions->push('Ajoutez un avatar pour etre plus identifiable dans les classements et les profils publics.');
        }

        if ($points < 100) {
            $suggestions->push('Les missions et les interactions sur les clips sont un bon point de depart pour regagner des points.');
        }

        if ((int) ($progress?->total_xp ?? 0) < 1000) {
            $suggestions->push('Votre prochaine ligue se debloque surtout via les missions, les clips et les activites recurrentes.');
        }

        if (! $user->clubReview) {
            $suggestions->push("Vous pouvez publier un avis sur le club pour enrichir votre presence publique.");
        }

        return [
            'name' => $user->name,
            'profile_url' => route('users.public', $user),
            'league' => $league['name'],
            'xp' => (int) ($progress?->total_xp ?? 0),
            'points' => $points,
            'reward_balance' => $rewardBalance,
            'supporter' => $user->isSupporterActive(),
            'suggestions' => $suggestions->take(3)->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCategory(HelpCategory $category): array
    {
        $bucketLabel = collect($this->landingBuckets())
            ->firstWhere('value', $category->landing_bucket)['label'] ?? 'Comprendre la plateforme';

        return [
            'id' => $category->id,
            'title' => $category->title,
            'slug' => $category->slug,
            'description' => $category->description,
            'intro' => $category->intro,
            'icon' => $category->icon,
            'bucket' => $bucketLabel,
            'articles_count' => (int) ($category->articles_count ?? 0),
            'url' => route('help.index', ['category' => $category->slug]).'#faq-center',
            'articles_preview' => $category->articles
                ->take(3)
                ->map(fn (HelpArticle $article) => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'url' => route('help.index', ['article' => $article->slug]).'#faq-center',
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentQuestion(HelpArticle $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'summary' => $article->summary,
            'body' => $article->body,
            'short_answer' => $article->short_answer,
            'keywords' => $article->keywords ?? [],
            'is_featured' => (bool) $article->is_featured,
            'is_faq' => (bool) $article->is_faq,
            'category' => $article->category ? [
                'title' => $article->category->title,
                'slug' => $article->category->slug,
            ] : null,
            'url' => route('help.index', ['article' => $article->slug]).'#faq-center',
            'cta_label' => $article->cta_label,
            'cta_url' => $article->cta_url,
            'support' => $this->articleSupportMeta($article),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function articleSupportMeta(HelpArticle $article): array
    {
        $meta = [
            'steps' => [],
            'tips' => [],
            'note' => null,
        ];

        return match ($article->slug) {
            'creer-son-compte-et-acceder-a-son-espace' => [
                ...$meta,
                'steps' => [
                    'Ouvrez la page d inscription ou de connexion.',
                    'Validez votre acces puis revenez sur votre dashboard.',
                    'Completez votre profil pour activer votre presence publique.',
                ],
                'tips' => ['Vous pouvez d abord explorer les modules publics avant de creer votre compte.'],
            ],
            'placer-un-pari-avant-le-verrouillage' => [
                ...$meta,
                'steps' => [
                    'Ouvrez la fiche match et verifiez le statut du match.',
                    'Choisissez la selection disponible avant le lock.',
                    'Confirmez votre pari tant que le marche est encore ouvert.',
                ],
                'tips' => ['Passe le lock, le pari n est plus modifiable.'],
            ],
            'gagner-des-points-avec-les-missions-quotidiennes' => [
                ...$meta,
                'steps' => [
                    'Consultez votre liste daily depuis la page missions.',
                    'Realisez les actions demandees sur les modules concernes.',
                    'Revenez verifier les recompenses et le bonus de completion.',
                ],
                'tips' => ['Les missions servent de guide: commencez par elles si vous ne savez pas quoi faire ensuite.'],
            ],
            'voir-un-clip-liker-et-ajouter-en-favoris' => [
                ...$meta,
                'steps' => [
                    'Parcourez le feed clips publiquement ou depuis votre console.',
                    'Connectez-vous si vous voulez liker, commenter ou ajouter en favoris.',
                    'Retrouvez vos clips sauvegardes depuis la page favoris.',
                ],
                'tips' => ['Le mode visiteur sert a decouvrir sans friction, pas a participer.'],
            ],
            'utiliser-le-reward-wallet-et-demander-un-cadeau' => [
                ...$meta,
                'steps' => [
                    'Consultez votre reward wallet pour verifier votre reserve.',
                    'Ouvrez la fiche detail du cadeau qui vous interesse.',
                    'Lancez la redemption si votre solde et le stock le permettent.',
                ],
                'tips' => ['Le suivi continue ensuite dans votre historique de redemptions.'],
            ],
            default => $meta,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function presentTourStep(HelpTourStep $step): array
    {
        return [
            'id' => $step->id,
            'step_number' => (int) $step->step_number,
            'title' => $step->title,
            'summary' => $step->summary,
            'body' => $step->body,
            'visual_title' => $step->visual_title ?: $step->title,
            'visual_body' => $step->visual_body ?: $step->summary,
            'cta_label' => $step->cta_label,
            'cta_url' => $step->cta_url,
            'progress_label' => sprintf('%d/6', (int) $step->step_number),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentGlossary(HelpGlossaryTerm $term): array
    {
        return [
            'id' => $term->id,
            'term' => $term->term,
            'slug' => $term->slug,
            'definition' => $term->definition,
            'short_answer' => $term->short_answer,
        ];
    }

    private function cached(string $key, callable $resolver): mixed
    {
        $version = (int) Cache::get(self::CACHE_VERSION_KEY, 1);

        return Cache::remember(
            sprintf('help-center:v%d:%s', $version, $key),
            now()->addMinutes(10),
            $resolver,
        );
    }

    private function toEmbedVideoUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $value = trim((string) $url);

        if (Str::contains($value, 'youtube.com/watch?v=')) {
            return str_replace('watch?v=', 'embed/', $value);
        }

        if (Str::contains($value, 'youtu.be/')) {
            return str_replace('youtu.be/', 'youtube.com/embed/', $value);
        }

        if (Str::contains($value, 'vimeo.com/')) {
            $segments = array_values(array_filter(explode('/', parse_url($value, PHP_URL_PATH) ?: '')));

            if ($segments !== []) {
                return 'https://player.vimeo.com/video/'.$segments[array_key_last($segments)];
            }
        }

        return $value;
    }
}
