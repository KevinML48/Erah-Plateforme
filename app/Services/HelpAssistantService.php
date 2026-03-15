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
        $classification = $this->assistantQueryClassifier->classify($message);
        $userContext = $this->userContext($user);

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
                answer: 'Je vois le sujet, mais je n ai pas trouve de réponse assez fiable pour te repondre au hasard.',
                confidence: 'fallback',
                userContext: $userContext,
                nextSteps: [
                    'Essaie une question plus précise avec un mot-cle comme points, missions, matchs, paris, recompenses ou profil.',
                    'Tu peux aussi parcourir la FAQ centrale pour retrouver la bonne categorie.',
                ],
            );
        }

        if (($bestGlossary['score'] ?? 0) > ($bestArticle['score'] ?? 0)) {
            /** @var HelpGlossaryTerm $term */
            $term = $bestGlossary['term'];
            $answer = trim(implode(' ', array_filter([
                'Sur ERAH, l'idée est simple :',
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
            'détails' => array_slice($paragraphs, 0, 2),
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
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function overviewPayload(?array $userContext): array
    {
        $answer = 'Sur ERAH, l'idée est de centraliser la progression, les missions, les matchs, les paris, le profil, les notifications et les recompenses dans un meme espace.';

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
                'Ensuite, pose une question plus précise sur les points, les missions, les matchs, les paris ou le profil.',
            ],
        );
    }

    /**
     * @param array<string, mixed>|null $userContext
     * @return array<string, mixed>
     */
    private function nextStepPayload(?array $userContext): array
    {
        $answer = 'En général, le plus utile est de complèter ton profil, verifier les missions actives, puis regarder les matchs et les recompenses selon ton objectif.';

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
    private function supporterPayload(?User $user, ?array $userContext): array
    {
        $isActive = (bool) ($userContext['supporter_active'] ?? false);
        $answer = $isActive
            ? 'Tu es deja supporter sur ERAH. Le plus utile est maintenant d ouvrir la page Supporter pour gérer ton abonnement, verifier tes avantages et suivre les missions reservees.'
            : 'Pour devenir supporter sur ERAH, il faut ouvrir la page Supporter, comparer les formules puis lancer le checkout. C est la porte d'entrée pour activer le badge supporter, les missions exclusives et les avantages associes.';

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
}
