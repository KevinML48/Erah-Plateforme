<?php

namespace App\Services\AI;

use App\Models\EsportMatch;
use App\Models\Gift;
use App\Models\HelpArticle;
use App\Models\HelpGlossaryTerm;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserMission;
use App\Services\RankService;
use Illuminate\Support\Facades\Cache;

class AssistantContextService
{
    public function __construct(
        private readonly RankService $rankService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(User $user): array
    {
        $user->loadMissing(['progress.league', 'wallet', 'rewardWallet', 'supportSubscriptions']);
        $league = $this->rankService->currentLeague($user);
        $walletBalance = (int) ($user->wallet?->balance ?? config('betting.wallet.initial_balance', 1000));
        $rewardBalance = (int) ($user->rewardWallet?->balance ?? 0);
        $missions = $this->activeMissions($user);
        $upcomingMatches = $this->upcomingMatches();
        $recommendedActions = $this->recommendedActions($user, $missions, $upcomingMatches, $walletBalance, $rewardBalance);

        return [
            'generated_at' => now()->toIso8601String(),
            'links' => [
                ['label' => 'Dashboard', 'url' => $this->relativeRoute('dashboard')],
                ['label' => 'Missions', 'url' => $this->relativeRoute('missions.index')],
                ['label' => 'Matchs', 'url' => $this->relativeRoute('matches.index')],
                ['label' => 'Paris', 'url' => $this->relativeRoute('bets.index')],
                ['label' => 'Profil', 'url' => $this->relativeRoute('profile.show')],
                ['label' => 'Notifications', 'url' => $this->relativeRoute('notifications.index')],
                ['label' => 'Cadeaux', 'url' => $this->relativeRoute('gifts.index')],
                ['label' => 'Aide', 'url' => $this->relativeRoute('console.help')],
            ],
            'knowledge' => $this->knowledgeSnapshot(),
            'user' => [
                'name' => $user->name,
                'profile' => [
                    'bio_present' => filled($user->bio),
                    'avatar_present' => filled($user->avatar_url),
                    'social_links_count' => count(array_filter([
                        $user->twitter_url,
                        $user->instagram_url,
                        $user->tiktok_url,
                        $user->discord_url,
                    ])),
                ],
                'progress' => [
                    'league' => $league['name'],
                    'xp' => (int) ($user->progress?->total_xp ?? 0),
                    'rank_points' => (int) ($user->progress?->total_rank_points ?? 0),
                ],
                'wallets' => [
                    'bet_points' => $walletBalance,
                    'reward_points' => $rewardBalance,
                ],
                'supporter_active' => $user->isSupporterActive(),
                'notifications_unread' => Notification::query()
                    ->where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
                'missions' => $missions,
                'upcoming_matches' => $upcomingMatches,
                'gift_highlights' => $this->giftHighlights($rewardBalance),
                'profile_suggestions' => $this->profileSuggestions($user),
                'recommended_actions' => $recommendedActions,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function pageSidebar(User $user): array
    {
        $context = $this->build($user);
        $userContext = $context['user'];

        return [
            'league' => data_get($userContext, 'progress.league'),
            'xp' => data_get($userContext, 'progress.xp'),
            'rank_points' => data_get($userContext, 'progress.rank_points'),
            'bet_points' => data_get($userContext, 'wallets.bet_points'),
            'reward_points' => data_get($userContext, 'wallets.reward_points'),
            'unread_notifications' => data_get($userContext, 'notifications_unread'),
            'recommended_actions' => data_get($userContext, 'recommended_actions', []),
            'upcoming_matches' => data_get($userContext, 'upcoming_matches', []),
            'profile_suggestions' => data_get($userContext, 'profile_suggestions', []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function knowledgeSnapshot(): array
    {
        return Cache::remember('assistant:knowledge:snapshot', now()->addMinutes(10), function (): array {
            $articles = HelpArticle::query()
                ->published()
                ->with('category')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit((int) config('assistant.knowledge.article_limit', 8))
                ->get()
                ->map(fn (HelpArticle $article) => [
                    'title' => $article->title,
                    'summary' => $article->short_answer ?: $article->summary,
                    'category' => $article->category?->title,
                    'url' => $this->relativeRoute('help.index', ['article' => $article->slug]).'#faq-center',
                ])
                ->values()
                ->all();

            $glossary = HelpGlossaryTerm::query()
                ->published()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit((int) config('assistant.knowledge.glossary_limit', 8))
                ->get()
                ->map(fn (HelpGlossaryTerm $term) => [
                    'term' => $term->term,
                    'definition' => $term->short_answer ?: $term->definition,
                ])
                ->values()
                ->all();

            return [
                'articles' => $articles,
                'glossary' => $glossary,
            ];
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function activeMissions(User $user): array
    {
        return UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', fn ($query) => $query
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now()))
            ->with('instance.template')
            ->orderByRaw('CASE WHEN completed_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->map(function (UserMission $mission): array {
                $template = $mission->instance?->template;

                return [
                    'title' => $template?->title ?? 'Mission',
                    'scope' => $template?->scope,
                    'progress' => (int) $mission->progress_count,
                    'target' => (int) ($template?->target_count ?? 0),
                    'completed' => $mission->completed_at !== null,
                    'url' => $this->relativeRoute('missions.index'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function upcomingMatches(): array
    {
        return EsportMatch::query()
            ->whereIn('status', [
                EsportMatch::STATUS_SCHEDULED,
                EsportMatch::STATUS_LOCKED,
                EsportMatch::STATUS_LIVE,
            ])
            ->orderBy('starts_at')
            ->limit(3)
            ->get()
            ->map(fn (EsportMatch $match) => [
                'title' => $match->displayTitle(),
                'subtitle' => $match->displaySubtitle(),
                'status' => $match->status,
                'starts_at' => optional($match->starts_at)?->toIso8601String(),
                'url' => $this->relativeRoute('matches.show', $match->id),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function giftHighlights(int $rewardBalance): array
    {
        return Gift::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->orderBy('cost_points')
            ->limit(3)
            ->get()
            ->map(fn (Gift $gift) => [
                'title' => $gift->title,
                'cost_points' => (int) $gift->cost_points,
                'reachable' => $rewardBalance >= (int) $gift->cost_points,
                'url' => $this->relativeRoute('gifts.show', $gift->id),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function profileSuggestions(User $user): array
    {
        $suggestions = collect();

        if (blank($user->bio)) {
            $suggestions->push('Ajoutez une bio courte pour rendre votre profil plus clair.');
        }

        if (blank($user->avatar_url)) {
            $suggestions->push('Ajoutez un avatar pour etre plus identifiable dans les espaces publics.');
        }

        if (blank($user->twitter_url) && blank($user->instagram_url) && blank($user->tiktok_url) && blank($user->discord_url)) {
            $suggestions->push('Ajoutez au moins un lien social si vous voulez enrichir votre presence.');
        }

        return $suggestions->take(3)->values()->all();
    }

    /**
     * @param array<int, array<string, mixed>> $missions
     * @param array<int, array<string, mixed>> $upcomingMatches
     * @return array<int, array<string, string>>
     */
    private function recommendedActions(User $user, array $missions, array $upcomingMatches, int $walletBalance, int $rewardBalance): array
    {
        $actions = collect();

        if (collect($missions)->contains(fn (array $mission): bool => ! $mission['completed'])) {
            $actions->push([
                'label' => 'Continuer les missions',
                'description' => 'Vos missions actives sont le meilleur levier pour progresser tout de suite.',
                'url' => $this->relativeRoute('missions.index'),
            ]);
        }

        if ($upcomingMatches !== []) {
            $actions->push([
                'label' => 'Verifier les matchs',
                'description' => 'Un tour sur les rencontres a venir aide a preparer vos paris et votre suivi.',
                'url' => $this->relativeRoute('matches.index'),
            ]);
        }

        if ($walletBalance < 200) {
            $actions->push([
                'label' => 'Regagner des points',
                'description' => 'Passez par les missions et les interactions communautaires pour relancer votre solde.',
                'url' => $this->relativeRoute('missions.index'),
            ]);
        }

        if ($rewardBalance > 0) {
            $actions->push([
                'label' => 'Explorer les cadeaux',
                'description' => 'Votre reward wallet peut deja vous ouvrir des recompenses.',
                'url' => $this->relativeRoute('gifts.index'),
            ]);
        }

        if (blank($user->bio) || blank($user->avatar_url)) {
            $actions->push([
                'label' => 'Ameliorer le profil',
                'description' => 'Quelques details en plus rendent votre profil plus solide et lisible.',
                'url' => $this->relativeRoute('profile.show'),
            ]);
        }

        return $actions
            ->unique(fn (array $item): string => $item['label'])
            ->take(4)
            ->values()
            ->all();
    }

    /**
     * @param mixed $parameters
     */
    private function relativeRoute(string $name, mixed $parameters = []): string
    {
        return route($name, $parameters, false);
    }
}
