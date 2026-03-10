<?php

namespace App\Services;

use App\Models\HelpArticle;
use App\Models\HelpGlossaryTerm;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HelpAssistantService
{
    public function __construct(
        private readonly RankService $rankService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function ask(string $message, ?User $user = null): array
    {
        $normalized = Str::of($message)->lower()->ascii()->replaceMatches('/[^a-z0-9\s]+/', ' ')->squish()->toString();
        $tokens = collect(explode(' ', $normalized))
            ->filter(fn (string $token): bool => mb_strlen($token) >= 3)
            ->unique()
            ->values();

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

        if (($bestArticle['score'] ?? 0) <= 0 && ($bestGlossary['score'] ?? 0) <= 0) {
            return [
                'mode' => config('help-center.assistant.mode', 'knowledge_base'),
                'answer' => "Je n'ai pas de reponse suffisamment fiable pour cette question. Essayez un sujet plus precis ou utilisez une des questions suggerees.",
                'confidence' => 'fallback',
                'sources' => [],
                'next_steps' => [
                    'Parcourir la FAQ centrale pour retrouver la bonne categorie.',
                    'Utiliser des mots cles comme matchs, missions, points, clips, duels ou cadeaux.',
                ],
                'user_context' => $this->userContext($user),
            ];
        }

        if (($bestGlossary['score'] ?? 0) > ($bestArticle['score'] ?? 0)) {
            /** @var HelpGlossaryTerm $term */
            $term = $bestGlossary['term'];

            return [
                'mode' => config('help-center.assistant.mode', 'knowledge_base'),
                'answer' => $term->short_answer ?: $term->definition,
                'confidence' => 'medium',
                'sources' => [[
                    'type' => 'glossary',
                    'title' => $term->term,
                    'url' => route('help.index').'#faq-center',
                ]],
                'next_steps' => ['Consultez la FAQ centrale si vous voulez une explication plus detaillee.'],
                'user_context' => $this->userContext($user),
            ];
        }

        /** @var HelpArticle $article */
        $article = $bestArticle['article'];
        $paragraphs = $this->paragraphs($article->body);

        return [
            'mode' => config('help-center.assistant.mode', 'knowledge_base'),
            'answer' => $article->short_answer ?: ($article->summary ?: ($paragraphs[0] ?? '')),
            'confidence' => ($bestArticle['score'] ?? 0) >= 8 ? 'high' : 'medium',
            'details' => array_slice($paragraphs, 0, 2),
            'sources' => [[
                'type' => 'article',
                'title' => $article->title,
                'category' => $article->category?->title,
                'url' => route('help.index', ['article' => $article->slug]).'#faq-center',
            ]],
            'next_steps' => array_values(array_filter([
                $article->cta_label && $article->cta_url ? $article->cta_label.' -> '.$article->cta_url : null,
                'Ouvrez la section FAQ si vous voulez plus de contexte ou une categorie voisine.',
            ])),
            'user_context' => $this->userContext($user),
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

        return [
            'points' => (int) ($user->wallet?->balance ?? 0),
            'reward_balance' => (int) ($user->rewardWallet?->balance ?? 0),
            'xp' => (int) ($progress?->total_xp ?? 0),
            'league' => $league['name'],
        ];
    }
}
