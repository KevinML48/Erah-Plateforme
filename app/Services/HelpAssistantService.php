<?php

namespace App\Services;

use App\Models\HelpArticle;
use App\Models\HelpGlossaryTerm;
use App\Models\User;
use App\Services\AI\AssistantQueryClassifier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HelpAssistantService
{
    public function __construct(
        private readonly RankService $rankService,
        private readonly AssistantQueryClassifier $assistantQueryClassifier,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function ask(string $message, ?User $user = null): array
    {
        $userContext = $this->userContext($user);

        if ($payload = $this->dedicatedPromptPayload($message, $user, $userContext)) {
            return $payload;
        }

        $classification = $this->assistantQueryClassifier->classify($message);

        if ($classification->isOutOfScope()) {
            return $this->guardPayload(
                answer: (string) $classification->fallbackMessage,
                confidence: 'out_of_scope',
                userContext: $userContext,
                nextSteps: [
                    'Reformule ta question autour des points, missions, matchs, paris, recompenses ou profil.',
                ],
            );
        }

        if ($classification->needsClarification()) {
            return $this->guardPayload(
                answer: (string) $classification->fallbackMessage,
                confidence: 'clarification',
                userContext: $userContext,
                nextSteps: [
                    'Tu peux me demander par exemple comment gagner des points, suivre les matchs ou ameliorer ton profil.',
                ],
            );
        }

        $normalized = $classification->normalized;
        $tokens = collect($classification->tokens)
            ->filter(fn (string $token): bool => mb_strlen($token) >= 3)
            ->unique()
            ->values();

        if (in_array('supporter', $classification->matchedTopics, true)) {
            return $this->supporterPayload($user, $userContext);
        }

        $matches = $this->knowledgeMatches($normalized, $tokens);
        $bestArticle = $matches['article'];
        $bestGlossary = $matches['glossary'];
        $bestScore = max((int) ($bestArticle['score'] ?? 0), (int) ($bestGlossary['score'] ?? 0));
        $minimumKnowledgeScore = (int) config('assistant.qualification.knowledge_min_score', 6);
        $strongKnowledgeScore = (int) config('assistant.qualification.knowledge_strong_score', 10);

        if ($bestScore < $minimumKnowledgeScore) {
            if (in_array('next_step', $classification->matchedTopics, true)) {
                return $this->nextStepPayload($userContext);
            }

            if (in_array('overview', $classification->matchedTopics, true)) {
                return $this->overviewPayload($userContext);
            }

            return $this->guardPayload(
                answer: 'Je vois le sujet, mais je n ai pas trouve de reponse assez fiable pour te repondre au hasard.',
                confidence: 'fallback',
                userContext: $userContext,
                nextSteps: [
                    'Essaie une question plus precise avec un mot-cle comme points, missions, matchs, paris, recompenses ou profil.',
                    'Tu peux aussi parcourir la FAQ centrale pour retrouver la bonne categorie.',
                ],
            );
        }

        if (($bestGlossary['score'] ?? 0) > ($bestArticle['score'] ?? 0)) {
            /** @var HelpGlossaryTerm $term */
            $term = $bestGlossary['term'];
            $answer = trim(implode(' ', array_filter([
                'Sur ERAH, l idee est simple :',
                $term->short_answer ?: $term->definition,
            ])));

            return [
                'mode' => config('help-center.assistant.mode', 'knowledge_base'),
                'answer' => $answer,
                'confidence' => ($bestGlossary['score'] ?? 0) >= $strongKnowledgeScore ? 'high' : 'medium',
                'sources' => [[
                    'type' => 'glossary',
                    'title' => $term->term,
                    'url' => $this->relativeRoute('help.index').'#faq-center',
                ]],
                'next_steps' => ['Si tu veux aller plus loin, ouvre la FAQ centrale pour lire le terme dans son contexte.'],
                'user_context' => $userContext,
            ];
        }

        /** @var HelpArticle $article */
        $article = $bestArticle['article'];
        $paragraphs = $this->paragraphs($article->body);
        $answer = trim(implode(' ', array_filter([
            'Oui, je peux t expliquer ca simplement.',
            $article->short_answer ?: ($article->summary ?: ($paragraphs[0] ?? '')),
        ])));

        return [
            'mode' => config('help-center.assistant.mode', 'knowledge_base'),
            'answer' => $answer,
            'confidence' => ($bestArticle['score'] ?? 0) >= $strongKnowledgeScore ? 'high' : 'medium',
            'details' => array_slice($paragraphs, 0, 2),
            'sources' => [[
                'type' => 'article',
                'title' => $article->title,
                'category' => $article->category?->title,
                'url' => $this->relativeRoute('help.index', ['article' => $article->slug]).'#faq-center',
            ]],
            'next_steps' => array_values(array_filter([
                $article->cta_label ?: null,
                'Ouvre la section FAQ si tu veux plus de contexte ou une categorie voisine.',
            ])),
            'user_context' => $userContext,
        ];
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>|null
     */
    public function dedicatedPromptPayload(string $message, ?User $user = null, ?array $userContext = null): ?array
    {
        $intent = $this->matchDedicatedPromptIntent($message);

        if ($intent === null) {
            return null;
        }

        $userContext ??= $this->userContext($user);

        return match ($intent) {
            'overview' => $this->overviewPromptPayload($user, $userContext),
            'points' => $this->pointsPromptPayload($user, $userContext),
            'upcoming_matches' => $this->upcomingMatchesPromptPayload($user, $userContext),
            'missions' => $this->missionsPromptPayload($user, $userContext),
            'gifts' => $this->giftsPromptPayload($user, $userContext),
            'profile' => $this->profilePromptPayload($user, $userContext),
            'betting' => $this->bettingPromptPayload($user, $userContext),
            'supporter' => $this->supporterPayload($user, $userContext),
            'next_step' => $this->nextStepPromptPayload($user, $userContext),
            'duels' => $this->duelsPromptPayload($user, $userContext),
            'leaderboards' => $this->leaderboardsPromptPayload($user, $userContext),
            'community' => $this->communityPromptPayload($user, $userContext),
            'clips' => $this->clipsPromptPayload($user, $userContext),
            'security' => $this->securityPromptPayload($user, $userContext),
            'events' => $this->eventsPromptPayload($user, $userContext),
            'bug_report' => $this->bugReportPromptPayload($user, $userContext),
            default => null,
        };
    }

    public function matchDedicatedPromptIntent(string $message): ?string
    {
        $normalized = $this->normalizeMessage($message);

        if ($normalized === '') {
            return null;
        }

        return match (true) {
            $this->containsAny($normalized, [
                'comment gagner des points',
                'gagner des points sans perdre de temps',
                'gagner des points rapidement',
                'comment avoir des points',
            ]) => 'points',
            $this->containsAny($normalized, [
                'comment voir les matchs a venir',
                'ou voir les matchs',
                'voir les matchs a venir',
                'quels matchs dois je surveiller bientot',
                'quels matchs surveiller bientot',
                'matchs a venir',
                'match a venir',
            ]) => 'upcoming_matches',
            $this->containsAny($normalized, [
                'comment marchent les missions',
                'comment fonctionnent les missions',
                'comment marche les missions',
            ]) => 'missions',
            $this->containsAny($normalized, [
                'comment recuperer des cadeaux',
                'comment recuperer un cadeau',
                'comment obtenir des cadeaux',
                'comment avoir des cadeaux',
            ]) => 'gifts',
            $this->containsAny($normalized, [
                'comment ameliorer mon profil',
                'comment renforcer mon profil',
                'ameliorer mon compte',
                'ameliorer mon profil erah',
            ]) => 'profile',
            $this->containsAny($normalized, [
                'peut on parier sur un match',
                'peut on faire des paris',
                'comment fonctionnent les bets sur erah',
                'comment fonctionne les bets sur erah',
                'placer un pari',
            ]) => 'betting',
            $this->containsAny($normalized, [
                'comment devenir supporter erah',
                'comment devenir supporter',
                'devenir supporter erah',
            ]) => 'supporter',
            $this->containsAny($normalized, [
                'que me conseilles tu comme prochaine action',
                'que me conseilles tu',
                'quelle est ma prochaine action',
                'que faire ensuite',
                'que faire maintenant',
                'quoi faire maintenant',
                'prochaine action',
            ]) => 'next_step',
            $this->containsAny($normalized, [
                'qu est ce que les duels et comment y participer',
                'comment participer aux duels',
                'comment marchent les duels',
                'fonctionnement des duels',
            ]) => 'duels',
            $this->containsAny($normalized, [
                'comment progresser dans les classements',
                'comment monter dans les classements',
                'comment progresser dans le leaderboard',
                'comment monter dans le leaderboard',
            ]) => 'leaderboards',
            $this->containsAny($normalized, [
                'quel est l esprit erah et nos valeurs',
                'quel est l esprit erah',
                'quelles sont les valeurs d erah',
                'valeurs erah',
            ]) => 'community',
            $this->containsAny($normalized, [
                'je veux partager un clip comment faire',
                'comment partager un clip',
                'envoyer un clip',
                'poster un clip',
            ]) => 'clips',
            $this->containsAny($normalized, [
                'mes donnees et securite du compte c est comment',
                'securite du compte',
                'donnees du compte',
                'proteger mon compte',
            ]) => 'security',
            $this->containsAny($normalized, [
                'quels evenements arrivent prochainement',
                'quels evenements arrivent bientot',
                'evenements a venir',
                'events a venir',
            ]) => 'events',
            $this->containsAny($normalized, [
                'j ai trouve un bug comment le signaler',
                'signaler un bug',
                'remonter un bug',
                'comment signaler un probleme',
            ]) => 'bug_report',
            $this->containsAny($normalized, [
                'comment fonctionne la plateforme',
                'comment fonctionne erah',
                'comment marche erah',
                'comment ca marche',
                'je debute sur erah',
                'par quoi commencer',
                'premiers pas sur erah',
                'c est quoi erah',
            ]) => 'overview',
            default => null,
        };
    }

    /**
     * @return Collection<int, HelpArticle>
     */
    private function articles(): Collection
    {
        $version = (int) Cache::get('help-center:version', 1);

        return Cache::remember(sprintf('help-assistant:v%d:articles', $version), now()->addMinutes(10), fn () => HelpArticle::query()
            ->published()
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @return Collection<int, HelpGlossaryTerm>
     */
    private function glossary(): Collection
    {
        $version = (int) Cache::get('help-center:version', 1);

        return Cache::remember(sprintf('help-assistant:v%d:glossary', $version), now()->addMinutes(10), fn () => HelpGlossaryTerm::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get());
    }

    private function scoreArticle(HelpArticle $article, string $normalized, Collection $tokens): int
    {
        $score = 0;
        $haystack = Str::of(implode(' ', array_filter([
            $article->title,
            $article->summary,
            $article->short_answer,
            implode(' ', $article->keywords ?? []),
            Str::limit($article->body, 1000, ''),
        ])))->lower()->ascii()->toString();

        if (Str::contains($haystack, $normalized)) {
            $score += 8;
        }

        foreach ($tokens as $token) {
            if (Str::contains(Str::lower(Str::ascii($article->title)), $token)) {
                $score += 4;
                continue;
            }

            if (Str::contains($haystack, $token)) {
                $score += 2;
            }
        }

        if ($article->is_featured) {
            $score += 1;
        }

        return $score;
    }

    private function scoreGlossary(HelpGlossaryTerm $term, string $normalized, Collection $tokens): int
    {
        $score = 0;
        $haystack = Str::of(implode(' ', [$term->term, $term->definition, $term->short_answer]))
            ->lower()
            ->ascii()
            ->toString();

        if (Str::contains($haystack, $normalized)) {
            $score += 6;
        }

        foreach ($tokens as $token) {
            if (Str::contains($haystack, $token)) {
                $score += 2;
            }
        }

        if ($term->is_featured) {
            $score += 1;
        }

        return $score;
    }

    /**
     * @param Collection<int, string> $tokens
     * @return array{
     *     article: array{article: HelpArticle, score: int}|null,
     *     glossary: array{term: HelpGlossaryTerm, score: int}|null
     * }
     */
    private function knowledgeMatches(string $normalized, Collection $tokens): array
    {
        $articles = $this->articles();
        $glossary = $this->glossary();

        $bestArticle = $articles
            ->map(fn (HelpArticle $article) => [
                'article' => $article,
                'score' => $this->scoreArticle($article, $normalized, $tokens),
            ])
            ->sortByDesc('score')
            ->first();

        $bestGlossary = $glossary
            ->map(fn (HelpGlossaryTerm $term) => [
                'term' => $term,
                'score' => $this->scoreGlossary($term, $normalized, $tokens),
            ])
            ->sortByDesc('score')
            ->first();

        return [
            'article' => $bestArticle,
            'glossary' => $bestGlossary,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function paragraphs(?string $body): array
    {
        return collect(preg_split('/\n{2,}/', (string) $body) ?: [])
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function userContext(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $user->loadMissing(['progress', 'wallet', 'rewardWallet']);
        $progress = $user->progress;
        $league = $this->rankService->currentLeague($user);
        $points = (int) ($user->rewardWallet?->balance ?? $user->wallet?->balance ?? 0);

        return [
            'points' => $points,
            'xp' => (int) ($progress?->total_xp ?? 0),
            'league' => $league['name'],
            'supporter_active' => $user->isSupporterActive(),
        ];
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @param array<int, string> $nextSteps
     * @return array<string, mixed>
     */
    private function guardPayload(string $answer, string $confidence, ?array $userContext, array $nextSteps = []): array
    {
        return [
            'mode' => config('help-center.assistant.mode', 'knowledge_base'),
            'answer' => $answer,
            'confidence' => $confidence,
            'sources' => [],
            'next_steps' => $nextSteps,
            'user_context' => $userContext,
        ];
    }

    /**
     * @param array<int, array<string, string>> $sources
     * @param array<int, string> $nextSteps
     * @param array<int, string> $details
     * @return array<string, mixed>
     */
    private function directPayload(
        string $answer,
        ?array $userContext,
        array $sources = [],
        array $nextSteps = [],
        array $details = [],
        string $confidence = 'high',
    ): array {
        return [
            'mode' => config('help-center.assistant.mode', 'knowledge_base'),
            'answer' => $answer,
            'confidence' => $confidence,
            'details' => $details,
            'sources' => $sources,
            'next_steps' => $nextSteps,
            'user_context' => $userContext,
        ];
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function overviewPayload(?array $userContext): array
    {
        $answer = 'Sur ERAH, l idee est de centraliser la progression, les missions, les matchs, les paris, le profil, les notifications et les recompenses dans un meme espace.';

        if ($userContext) {
            $answer .= sprintf(
                ' En ce moment, ton compte visible remonte %d points, %d XP et la ligue %s.',
                (int) ($userContext['points'] ?? 0),
                (int) ($userContext['xp'] ?? 0),
                (string) ($userContext['league'] ?? 'Bronze')
            );
        }

        return $this->guardPayload(
            answer: $answer,
            confidence: 'medium',
            userContext: $userContext,
            nextSteps: [
                'Commence par la FAQ centrale si tu veux la vue d ensemble.',
                'Ensuite, pose une question plus precise sur les points, les missions, les matchs, les paris ou le profil.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function overviewPromptPayload(?User $user, ?array $userContext): array
    {
        $answer = 'ERAH regroupe la progression, les missions, les matchs, les paris, le profil, les notifications, les cadeaux et les modules communautaires dans un meme espace. Le plus simple pour commencer est de comprendre le role de la plateforme, puis d ouvrir Missions et Matchs pour voir ce qui est disponible.';

        if ($userContext) {
            $answer .= sprintf(
                ' Sur ton compte, le contexte visible remonte %d points, %d XP et la ligue %s.',
                (int) ($userContext['points'] ?? 0),
                (int) ($userContext['xp'] ?? 0),
                (string) ($userContext['league'] ?? 'Bronze')
            );
        }

        return $this->directPayload(
            answer: $answer,
            userContext: $userContext,
            sources: [[
                'type' => 'article',
                'title' => 'Comprendre le role de la plateforme',
                'url' => $this->relativeRoute('help.index', ['article' => 'comprendre-le-role-de-la-plateforme']).'#faq-center',
            ]],
            nextSteps: [
                $user ? 'Ouvre Missions pour voir les objectifs actifs.' : 'Lis la FAQ centrale pour voir la logique generale de la plateforme.',
                $user ? 'Passe ensuite sur Matchs pour reperer les rencontres a suivre.' : 'Connecte-toi ensuite pour ouvrir les modules membres utiles.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function nextStepPayload(?array $userContext): array
    {
        $answer = 'En general, le plus utile est de completer ton profil, verifier les missions actives, puis regarder les matchs et les recompenses selon ton objectif.';

        if ($userContext) {
            $answer .= sprintf(
                ' Pour toi, le contexte visible remonte %d points, %d XP et la ligue %s.',
                (int) ($userContext['points'] ?? 0),
                (int) ($userContext['xp'] ?? 0),
                (string) ($userContext['league'] ?? 'Bronze')
            );
        }

        return $this->guardPayload(
            answer: $answer,
            confidence: 'medium',
            userContext: $userContext,
            nextSteps: [
                'Si tu veux, demande-moi ensuite quoi faire pour gagner des points ou progresser plus vite.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function nextStepPromptPayload(?User $user, ?array $userContext): array
    {
        $answer = 'Si tu veux une prochaine action claire sur ERAH, commence par verifier ton profil, ouvrir les missions actives, puis regarder les matchs et les recompenses. Cet ordre evite de te disperser et te remet vite dans une boucle utile.';

        if ($userContext) {
            $answer .= sprintf(
                ' Le contexte visible remonte %d points, %d XP et la ligue %s.',
                (int) ($userContext['points'] ?? 0),
                (int) ($userContext['xp'] ?? 0),
                (string) ($userContext['league'] ?? 'Bronze')
            );
        }

        return $this->directPayload(
            answer: $answer,
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Centre d aide',
                'url' => $user ? $this->relativeRoute('console.help') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                $user ? 'Ouvre ton Profil et corrige ce qui manque.' : 'Commence par la FAQ centrale pour reperer le module qui t interesse.',
                $user ? 'Ensuite, ouvre Missions pour relancer ta progression.' : 'Puis connecte-toi pour ouvrir tes modules membres.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function pointsPromptPayload(?User $user, ?array $userContext): array
    {
        $answer = 'Le levier le plus fiable pour gagner des points sur ERAH, ce sont les missions actives. Selon les modules ouverts, certaines actions communautaires et le suivi de l activite peuvent aussi alimenter ta progression, mais les missions restent le meilleur point de depart.';

        if ($userContext) {
            $answer .= sprintf(' Ton compte affiche actuellement %d points et %d XP.', (int) ($userContext['points'] ?? 0), (int) ($userContext['xp'] ?? 0));
        }

        return $this->directPayload(
            answer: $answer,
            userContext: $userContext,
            sources: [[
                'type' => 'article',
                'title' => 'Gagner des points avec les missions quotidiennes',
                'url' => $this->relativeRoute('help.index', ['article' => 'gagner-des-points-avec-les-missions-quotidiennes']).'#faq-center',
            ]],
            nextSteps: [
                $user ? 'Ouvre Missions pour voir les objectifs les plus rentables.' : 'Lis la FAQ sur les missions pour comprendre le cycle de progression.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function upcomingMatchesPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Pour voir les matchs a venir, ouvre la page Matchs. Tu y retrouves les rencontres programmees, leur statut et, quand un marche est ouvert, les paris disponibles avant le verrouillage.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Matchs',
                'url' => $user ? $this->relativeRoute('matches.index') : $this->relativeRoute('help.index', ['category' => 'matchs-et-paris']).'#faq-center',
            ]],
            nextSteps: [
                $user ? 'Ouvre Matchs pour verifier les rencontres planifiees.' : 'Passe par la FAQ Matchs et paris si tu veux comprendre le fonctionnement avant de te connecter.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function missionsPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Les missions te donnent un objectif clair, un compteur de progression et une recompense quand les conditions sont remplies. Le bon reflexe est d ouvrir Missions pour verifier les objectifs actifs, lire les conditions et prioriser ce qui peut etre termine rapidement.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Missions',
                'url' => $user ? $this->relativeRoute('missions.index') : $this->relativeRoute('help.index', ['article' => 'gagner-des-points-avec-les-missions-quotidiennes']).'#faq-center',
            ]],
            nextSteps: [
                $user ? 'Ouvre Missions et trie les objectifs les plus accessibles.' : 'Lis d abord la FAQ missions pour comprendre comment la progression est structuree.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function giftsPromptPayload(?User $user, ?array $userContext): array
    {
        $answer = 'Pour recuperer un cadeau, il faut accumuler assez de points, ouvrir le catalogue cadeaux, verifier le cout et le stock, puis envoyer une demande de redemption quand le solde le permet.';

        if ($userContext) {
            $answer .= sprintf(' Ton contexte visible remonte %d points disponibles.', (int) ($userContext['points'] ?? 0));
        }

        return $this->directPayload(
            answer: $answer,
            userContext: $userContext,
            sources: [[
                'type' => 'article',
                'title' => 'Utiliser le reward wallet et demander un cadeau',
                'url' => $this->relativeRoute('help.index', ['article' => 'utiliser-le-reward-wallet-et-demander-un-cadeau']).'#faq-center',
            ]],
            nextSteps: [
                $user ? 'Ouvre Cadeaux pour verifier ce que ton solde peut deja debloquer.' : 'Lis la FAQ cadeaux pour comprendre le cycle reward wallet et redemption.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function profilePromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Pour ameliorer ton profil ERAH, commence par ajouter une bio claire, un avatar propre et tes liens sociaux utiles. L idee est d avoir un profil lisible, credible et immediatement comprehensible par les autres membres.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Profil',
                'url' => $user ? $this->relativeRoute('profile.show') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                $user ? 'Ouvre ton Profil pour corriger les elements manquants.' : 'Connecte-toi ensuite pour completer ton profil membre.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function bettingPromptPayload(?User $user, ?array $userContext): array
    {
        $answer = 'Oui, vous pouvez parier sur un match quand la plateforme propose un marche ouvert pour cette rencontre. Il faut ouvrir la fiche du match, choisir une selection, verifier que le verrouillage n est pas passe et avoir assez de points pour miser.';

        if ($userContext) {
            $answer .= sprintf(' Le contexte visible remonte %d points disponibles.', (int) ($userContext['points'] ?? 0));
        }

        return $this->directPayload(
            answer: $answer,
            userContext: $userContext,
            sources: [[
                'type' => 'article',
                'title' => 'Peut-on parier sur un match ?',
                'url' => $this->relativeRoute('help.index', ['article' => 'peut-on-parier-sur-un-match']).'#faq-center',
            ]],
            nextSteps: [
                $user ? 'Ouvre Matchs pour verifier les rencontres avec un marche ouvert.' : 'Lis la FAQ Matchs et paris pour comprendre quand un pari est disponible.',
                $user ? 'Ouvre ensuite Paris pour suivre tes mises deja placees.' : 'Connecte-toi ensuite pour miser quand un match eligible est ouvert.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function duelsPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Les duels servent a lancer des defis distincts de la progression classique. Pour y participer, il faut ouvrir l espace Duels, verifier les defis disponibles puis suivre leur statut jusqu a resolution.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Duels',
                'url' => $user ? $this->relativeRoute('duels.index') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                $user ? 'Ouvre Duels pour voir les defis disponibles et leur statut.' : 'Connecte-toi pour acceder au module Duels quand il est pertinent pour ton compte.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function leaderboardsPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Pour progresser dans les classements, il faut maintenir une activite reguliere sur les modules qui font avancer votre progression, surtout les missions et les actions utiles a votre compte. Les classements servent ensuite a visualiser votre position par rapport aux autres membres.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Classements',
                'url' => $user ? $this->relativeRoute('leaderboards.index') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                $user ? 'Ouvre Classements pour voir ta position et ta ligue.' : 'Lis la FAQ centrale puis connecte-toi pour suivre tes classements.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function communityPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'L esprit ERAH repose sur une progression claire, une culture esport assumee, des interactions communautaires utiles et un cadre respectueux. L idee n est pas seulement de consommer du contenu, mais de participer, progresser et transformer l activite en avantages concrets.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Centre d aide',
                'url' => $user ? $this->relativeRoute('console.help') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                'Parcours la FAQ et les modules communautaires pour voir comment cette logique se traduit concretement.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function clipsPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Pour partager un clip, ouvre le module Clips puis utilise le parcours prevu pour proposer ton contenu. Le bon reflexe est de preparer un clip propre, clairement presentable et conforme a la ligne communautaire avant l envoi.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Clips',
                'url' => $user ? $this->relativeRoute('clips.index') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                $user ? 'Ouvre Clips pour lancer un partage depuis ton espace.' : 'Connecte-toi ensuite pour utiliser le module Clips.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function securityPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Pour proteger ton compte ERAH, garde un mot de passe solide, verifie ton adresse de contact et evite de partager tes acces. Si tu vois un comportement anormal, le bon reflexe est de mettre a jour tes informations puis de signaler le probleme au support.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Contact',
                'url' => $this->relativeRoute('marketing.contact'),
            ]],
            nextSteps: [
                $user ? 'Passe sur ton profil et tes reglages si tu dois mettre a jour tes informations.' : 'Utilise la page Contact si tu soupconnes un probleme de compte.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function eventsPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Les evenements arrivent au fil du calendrier ERAH et des annonces de l equipe. Le plus fiable est de surveiller les annonces publiees sur la plateforme, les pages evenement dediees et les espaces ou l activite du moment est mise en avant.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Centre d aide',
                'url' => $user ? $this->relativeRoute('console.help') : $this->relativeRoute('help.index'),
            ]],
            nextSteps: [
                'Surveille les annonces officielles et les pages evenement quand une ouverture est publiee.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function bugReportPromptPayload(?User $user, ?array $userContext): array
    {
        return $this->directPayload(
            answer: 'Si tu as trouve un bug, le plus propre est de le signaler avec le contexte exact: module concerne, action realisee, resultat observe et si possible une capture. Plus le signalement est precis, plus l equipe peut corriger vite.',
            userContext: $userContext,
            sources: [[
                'type' => 'page',
                'title' => 'Contact',
                'url' => $this->relativeRoute('marketing.contact'),
            ]],
            nextSteps: [
                'Utilise la page Contact et decris clairement le bug, le moment et le module touches.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function supporterPayload(?User $user, ?array $userContext): array
    {
        $isActive = (bool) ($userContext['supporter_active'] ?? false);
        $answer = $isActive
            ? 'Tu es deja supporter sur ERAH. Le plus utile est maintenant d ouvrir la page Supporter pour gerer ton abonnement, verifier tes avantages et suivre les missions reservees.'
            : 'Pour devenir supporter sur ERAH, il faut ouvrir la page Supporter, comparer les formules puis lancer le checkout. C est la porte d entree pour activer le badge supporter, les missions exclusives et les avantages associes.';

        return [
            'mode' => config('help-center.assistant.mode', 'knowledge_base'),
            'answer' => $answer,
            'confidence' => 'high',
            'sources' => [[
                'type' => 'page',
                'title' => 'Supporter ERAH',
                'url' => $this->relativeRoute('supporter.show'),
            ]],
            'next_steps' => [
                $isActive
                    ? 'Ouvre la page Supporter pour suivre tes avantages actifs.'
                    : 'Ouvre la page Supporter pour choisir la formule qui te convient.',
            ],
            'user_context' => $userContext,
        ];
    }

    /**
     * @param array<string, scalar|null> $parameters
     */
    private function relativeRoute(string $name, array $parameters = []): string
    {
        return route($name, $parameters, false);
    }

    private function normalizeMessage(string $message): string
    {
        return Str::of($message)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]/', ' ')
            ->squish()
            ->toString();
    }

    /**
     * @param array<int, string> $needles
     */
    private function containsAny(string $normalized, array $needles): bool
    {
        return Str::contains($normalized, $needles);
    }
}
