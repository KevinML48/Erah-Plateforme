<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class AssistantQueryClassifier
{
    private const GENERIC_TOPICS = ['overview', 'help'];

    private const GREETINGS = [
        'bonjour',
        'bonsoir',
        'salut',
        'hello',
        'yo',
        'coucou',
    ];

    private const QUESTION_INTENTS = [
        'comment',
        'pourquoi',
        'quoi',
        'quel',
        'quelle',
        'quels',
        'quelles',
        'ou',
        'quand',
        'combien',
        'peux tu',
        'tu peux',
        'explique',
        'aide moi',
        'j ai besoin',
        'je dois',
        'que faire',
        'quoi faire',
    ];

    private const BROAD_PATTERNS = [
        'comment ca marche',
        'comment fonctionne',
        'ca marche comment',
        'je comprends pas',
        'aide moi',
        'explique moi',
        'j ai besoin d aide',
        'j ai une question',
    ];

    private const STOPWORDS = [
        'alors',
        'apres',
        'assez',
        'avec',
        'avoir',
        'bonjour',
        'bonsoir',
        'cela',
        'celle',
        'celui',
        'cette',
        'comment',
        'dans',
        'depuis',
        'donc',
        'elle',
        'elles',
        'encore',
        'erah',
        'etre',
        'faire',
        'faut',
        'hello',
        'ici',
        'juste',
        'leur',
        'leurs',
        'mais',
        'meme',
        'merci',
        'moi',
        'mon',
        'plus',
        'pour',
        'pourquoi',
        'quoi',
        'quand',
        'quel',
        'quelle',
        'quelles',
        'quels',
        'salut',
        'sera',
        'suis',
        'sur',
        'tout',
        'tous',
        'tres',
        'une',
        'vous',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const TOPIC_KEYWORDS = [
        'overview' => [
            'erah',
            'plateforme',
            'console',
            'comment ca marche',
            'comment fonctionne',
            'comment marche',
            'c est quoi erah',
            'fonctionnement',
            'module',
            'modules',
        ],
        'missions' => [
            'mission',
            'missions',
            'objectif',
            'objectifs',
            'quotidienne',
            'quotidiennes',
            'hebdomadaire',
            'hebdomadaires',
            'quete',
            'quetes',
        ],
        'matches' => [
            'match',
            'matchs',
            'rencontre',
            'rencontres',
            'calendrier',
            'match a venir',
            'matchs a venir',
            'planning',
        ],
        'bets' => [
            'pari',
            'paris',
            'bet',
            'bets',
            'miser',
            'mise',
            'pronostic',
            'pronostics',
        ],
        'rewards' => [
            'reward',
            'rewards',
            'recompense',
            'recompenses',
            'cadeau',
            'cadeaux',
            'gift',
            'gifts',
        ],
        'notifications' => [
            'notification',
            'notifications',
            'alerte',
            'alertes',
            'rappel',
            'rappels',
        ],
        'points' => [
            'point',
            'points',
            'xp',
            'progression',
            'ligue',
            'wallet',
            'solde',
            'rank points',
            'reward wallet',
        ],
        'profile' => [
            'profil',
            'bio',
            'avatar',
            'compte',
            'compte public',
            'social',
            'reseaux',
        ],
        'supporter' => [
            'supporter',
            'devenir supporter',
            'soutenir erah',
            'abonnement supporter',
            'badge supporter',
            'formule supporter',
            'mission supporter',
            'avantage supporter',
            'avantages supporter',
            'checkout supporter',
        ],
        'next_step' => [
            'je dois faire quoi',
            'que dois je faire',
            'quoi faire maintenant',
            'que faire maintenant',
            'je fais quoi maintenant',
            'par quoi commencer',
            'premiers pas',
            'je debute',
            'nouveau sur erah',
            'que puis je faire',
            'quoi faire ensuite',
            'que faire ensuite',
            'prochaine action',
            'prochaine etape',
            'next step',
        ],
        'help' => [
            'aide',
            'faq',
            'glossaire',
            'article',
            'articles',
            'assistant',
            'support',
        ],
        'clips' => [
            'clip',
            'clips',
            'video',
            'videos',
            'stream',
            'streams',
            'twitch',
            'youtube',
            'contenu',
            'contenus',
            'replay',
            'replays',
        ],
        'duels' => [
            'duel',
            'duels',
            ' 1v1',
            '1v1 ',
            'versus',
            'challenge',
            'challenges',
            'combat',
            'combats',
        ],
        'leaderboards' => [
            'leaderboard',
            'leaderboards',
            'classement',
            'classements',
            'ranking',
            'rankings',
            'top',
            'tops',
            'meilleurs',
            'podium',
            'score global',
        ],
        'community' => [
            'communaute',
            'community',
            'regles',
            'reglement',
            'code of conduct',
            'guide',
            'guides',
            'charte',
            'esprit erah',
            'valeurs',
        ],
        'events' => [
            'evenement',
            'evenements',
            'event',
            'events',
            'lans',
            'lan',
            'tournoi',
            'tournois',
            'competition',
            'competitions',
            'gauntlet',
            'challenge esport',
        ],
        'account' => [
            'compte',
            'securite',
            'mot de passe',
            'password',
            '2fa',
            'double authentification',
            'email',
            'verification',
            'changement mot de passe',
            'recuperation compte',
            'donnees personnelles',
        ],
        'activity' => [
            'historique',
            'history',
            'activite',
            'activites',
            'progression',
            'statistiques',
            'stats',
            'badge',
            'badges',
            'achievement',
            'achievements',
            'cursus',
            'parcours',
        ],
        'bugs' => [
            'bug',
            'bugs',
            'probleme',
            'problemes',
            'erreur',
            'erreurs',
            'ne fonctionne pas',
            'bug report',
            'signaler un bug',
            'rapport de bug',
            'incident',
            'incidents',
        ],
    ];

    public function classify(string $message): AssistantQueryClassification
    {
        $normalized = $this->normalize($message);
        $tokens = $this->tokens($normalized);
        $matchedTopics = $this->matchedTopics($normalized);
        $questionIntent = $this->hasQuestionIntent($normalized);
        $usefulTokens = $this->usefulTokens($tokens);
        $specificTopics = array_values(array_diff($matchedTopics, self::GENERIC_TOPICS));

        if ($normalized === '') {
            return $this->outOfScope(
                normalized: $normalized,
                tokens: $tokens,
                matchedTopics: $matchedTopics,
                message: 'Je n ai pas bien compris ta question. Tu peux la reformuler ?',
                reason: 'empty'
            );
        }

        if ($this->isGreetingOnly($normalized, $tokens)) {
            return $this->needsClarification(
                normalized: $normalized,
                tokens: $tokens,
                matchedTopics: $matchedTopics,
                message: 'Bonjour. Je peux t aider sur ERAH. Dis-moi simplement ce que tu veux comprendre : les points, les missions, les matchs, les paris, le profil ou les recompenses.',
                reason: 'greeting'
            );
        }

        if ($matchedTopics === []) {
            if (! $questionIntent) {
                return $this->outOfScope(
                    normalized: $normalized,
                    tokens: $tokens,
                    matchedTopics: $matchedTopics,
                    message: 'Je n ai pas bien compris ta question. Tu peux la reformuler ?',
                    reason: 'incomprehensible'
                );
            }

            return $this->outOfScope(
                normalized: $normalized,
                tokens: $tokens,
                matchedTopics: $matchedTopics,
                message: 'Je n ai pas trouve de lien clair avec la plateforme ERAH. Tu peux reformuler ta demande ?',
                reason: 'out_of_scope'
            );
        }

        if ($this->looksBroadButRelevant($normalized, $specificTopics)) {
            return $this->needsClarification(
                normalized: $normalized,
                tokens: $tokens,
                matchedTopics: $matchedTopics,
                message: 'Je peux t aider, mais ta question est assez large. Tu veux comprendre le fonctionnement global de la plateforme, les points, les matchs, les missions ou une autre partie precise ?',
                reason: 'broad_related'
            );
        }

        if ($specificTopics === [] && count($usefulTokens) <= 2) {
            return $this->needsClarification(
                normalized: $normalized,
                tokens: $tokens,
                matchedTopics: $matchedTopics,
                message: 'Je crois voir ce que tu veux dire, mais il me manque un peu de contexte. Tu veux parler des points, des missions, des matchs, des paris ou du profil ?',
                reason: 'too_short_related'
            );
        }

        return new AssistantQueryClassification(
            kind: 'clear',
            confidence: $this->confidenceScore($matchedTopics, $questionIntent, $specificTopics),
            normalized: $normalized,
            tokens: $tokens,
            matchedTopics: $matchedTopics,
            reason: 'clear_related',
        );
    }

    private function normalize(string $message): string
    {
        return Str::of($message)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->squish()
            ->toString();
    }

    /**
     * @return array<int, string>
     */
    private function tokens(string $normalized): array
    {
        return collect(explode(' ', $normalized))
            ->map(fn (string $token): string => trim($token))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function matchedTopics(string $normalized): array
    {
        return collect(self::TOPIC_KEYWORDS)
            ->filter(fn (array $keywords): bool => Str::contains($normalized, $keywords))
            ->keys()
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $tokens
     */
    private function isGreetingOnly(string $normalized, array $tokens): bool
    {
        if (count($tokens) > 2) {
            return false;
        }

        return in_array($normalized, self::GREETINGS, true)
            || collect($tokens)->every(fn (string $token): bool => in_array($token, self::GREETINGS, true));
    }

    private function hasQuestionIntent(string $normalized): bool
    {
        return Str::contains($normalized, self::QUESTION_INTENTS);
    }

    /**
     * @param array<int, string> $tokens
     * @return array<int, string>
     */
    private function usefulTokens(array $tokens): array
    {
        return collect($tokens)
            ->filter(fn (string $token): bool => mb_strlen($token) >= 2)
            ->reject(fn (string $token): bool => in_array($token, self::STOPWORDS, true))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int, string> $specificTopics
     */
    private function looksBroadButRelevant(string $normalized, array $specificTopics): bool
    {
        if (in_array('next_step', $specificTopics, true)) {
            return false;
        }

        if (Str::contains($normalized, self::BROAD_PATTERNS) && $specificTopics === []) {
            return true;
        }

        return $specificTopics === []
            && Str::contains($normalized, ['plateforme', 'console', 'fonctionnement', 'aide', 'assistant']);
    }

    /**
     * @param array<int, string> $matchedTopics
     * @param array<int, string> $specificTopics
     */
    private function confidenceScore(array $matchedTopics, bool $questionIntent, array $specificTopics): float
    {
        $score = 0.35 + (count($matchedTopics) * 0.12);

        if ($questionIntent) {
            $score += 0.15;
        }

        if ($specificTopics !== []) {
            $score += 0.2;
        }

        return min(0.98, round($score, 2));
    }

    /**
     * @param array<int, string> $tokens
     * @param array<int, string> $matchedTopics
     */
    private function needsClarification(string $normalized, array $tokens, array $matchedTopics, string $message, string $reason): AssistantQueryClassification
    {
        return new AssistantQueryClassification(
            kind: 'needs_clarification',
            confidence: 0.42,
            normalized: $normalized,
            tokens: $tokens,
            matchedTopics: $matchedTopics,
            fallbackMessage: $message,
            reason: $reason,
        );
    }

    /**
     * @param array<int, string> $tokens
     * @param array<int, string> $matchedTopics
     */
    private function outOfScope(string $normalized, array $tokens, array $matchedTopics, string $message, string $reason): AssistantQueryClassification
    {
        return new AssistantQueryClassification(
            kind: 'out_of_scope',
            confidence: 0.12,
            normalized: $normalized,
            tokens: $tokens,
            matchedTopics: $matchedTopics,
            fallbackMessage: $message,
            reason: $reason,
        );
    }
}
