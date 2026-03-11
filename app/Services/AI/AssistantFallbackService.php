<?php

namespace App\Services\AI;

use App\Models\User;
use App\Services\HelpAssistantService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AssistantFallbackService
{
    public function __construct(
        private readonly HelpAssistantService $helpAssistantService,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function reply(string $message, User $user, array $context, ?AssistantQueryClassification $classification = null): AssistantResponse
    {
        $normalized = Str::of($message)->lower()->ascii()->squish()->toString();
        $userContext = Arr::get($context, 'user', []);

        if ($classification?->requiresGuardResponse()) {
            return $this->guardedReply($classification);
        }

        if ($this->looksLikeOverviewQuestion($normalized)) {
            return $this->overviewReply($userContext, $context);
        }

        if ($this->looksLikeMissionQuestion($normalized)) {
            return $this->missionReply($userContext, $context);
        }

        if ($this->looksLikeMatchesQuestion($normalized) && Arr::get($userContext, 'upcoming_matches')) {
            return $this->upcomingMatchesReply($userContext, $context);
        }

        if ($this->looksLikeBetQuestion($normalized)) {
            return $this->betReply($userContext, $context);
        }

        if ($this->looksLikeSupporterQuestion($normalized)) {
            return $this->supporterReply($userContext, $context);
        }

        if ($this->looksLikeRewardsQuestion($normalized)) {
            return $this->rewardReply($userContext, $context);
        }

        if ($this->looksLikeNotificationQuestion($normalized)) {
            return $this->notificationReply($userContext, $context);
        }

        if ($this->looksLikePointsQuestion($normalized)) {
            return $this->pointsReply($userContext, $context);
        }

        if ($this->looksLikeProfileQuestion($normalized)) {
            return $this->profileReply($userContext, $context);
        }

        if ($this->looksLikeNextStepQuestion($normalized)) {
            return $this->nextStepReply($userContext, $context);
        }

        $knowledge = $this->helpAssistantService->ask($message, $user);
        $content = $this->knowledgeReply($knowledge, $userContext, $context);

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
            metadata: [
                'sources' => $knowledge['sources'] ?? [],
                'next_steps' => $knowledge['next_steps'] ?? [],
            ],
        );
    }

    /**
     */
    public function guardedReply(AssistantQueryClassification $classification): AssistantResponse
    {
        $followUp = $classification->needsClarification()
            ? 'Tu peux me demander par exemple comment gagner des points, suivre les matchs, comprendre les bets ou ameliorer ton profil.'
            : 'Si tu veux, reformule ta demande en lien avec les points, missions, matchs, paris, recompenses ou le profil.';

        return new AssistantResponse(
            content: trim((string) $classification->fallbackMessage."\n\n".$followUp),
            provider: 'knowledge-base',
            model: 'local-guard',
            metadata: [
                'qualification' => [
                    'kind' => $classification->kind,
                    'reason' => $classification->reason,
                    'topics' => $classification->matchedTopics,
                ],
            ],
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function overviewReply(array $userContext, array $context): AssistantResponse
    {
        $league = Arr::get($userContext, 'progress.league', 'Bronze');
        $xp = (int) Arr::get($userContext, 'progress.xp', 0);
        $points = (int) Arr::get($userContext, 'wallets.points', 0);

        $content = trim(implode("\n\n", array_filter([
            'Si tu veux comprendre ERAH dans les grandes lignes, l idee est simple : tout est rassemble dans la meme console pour suivre ta progression, tes missions, les matchs, les bets, ton profil, tes notifications et tes recompenses.',
            "Dans ton contexte actuel, tu es en ligue {$league} avec {$xp} XP et {$points} points disponibles sur la plateforme.",
            'Le plus utile maintenant, c est de regarder '.($this->contextLink($context, 'Missions') ?: 'la page Missions').' puis '.($this->contextLink($context, 'Matchs') ?: 'la page Matchs').' pour voir ce qui peut te faire avancer rapidement.',
        ])));

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function missionReply(array $userContext, array $context): AssistantResponse
    {
        $missions = collect($userContext['missions'] ?? [])->take(3);
        $lines = $missions->map(function (array $mission): string {
            $target = max(0, (int) ($mission['target'] ?? 0));
            $progress = max(0, (int) ($mission['progress'] ?? 0));
            $suffix = ($mission['completed'] ?? false)
                ? ' - terminee'
                : ($target > 0 ? " - {$progress}/{$target}" : '');

            return '- '.$mission['title'].$suffix;
        })->all();

        $content = trim(implode("\n\n", array_filter([
            'Sur ERAH, les missions sont souvent le levier le plus direct pour progresser sans te disperser.',
            $lines !== [] ? "Celles qui ressortent dans ton contexte :\n".implode("\n", $lines) : 'Je n ai pas de mission active fiable a citer ici, donc le plus propre est d ouvrir directement ton espace Missions.',
            'Le prochain pas le plus utile : '.($this->contextLink($context, 'Missions') ?: 'ouvrir Missions').' pour voir le detail, les conditions et ce qui peut etre termine rapidement.',
        ])));

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function upcomingMatchesReply(array $userContext, array $context): AssistantResponse
    {
        $matches = collect($userContext['upcoming_matches'] ?? [])->take(3);
        $lines = $matches->map(function (array $match): string {
            $start = $match['starts_at'] ? ' - '.str_replace('T', ' ', substr((string) $match['starts_at'], 0, 16)) : '';

            return '- '.$match['title'].$start;
        })->all();

        $content = trim(implode("\n\n", array_filter([
            'Voici les matchs qui remontent le plus clairement dans la plateforme pour le moment.',
            implode("\n", $lines),
            'Le meilleur prochain pas : '.($this->contextLink($context, 'Matchs') ?: 'ouvrir Matchs').' pour verifier le statut, les details et les bets disponibles.',
        ])));

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function betReply(array $userContext, array $context): AssistantResponse
    {
        $points = (int) Arr::get($userContext, 'wallets.points', 0);
        $matches = collect($userContext['upcoming_matches'] ?? [])->take(2);

        $sections = [
            'Sur ERAH, les bets se preparent a partir des matchs disponibles. Le plus utile est de miser avec du contexte, pas a l aveugle.',
            "Ton solde actuel est de {$points} points.",
        ];

        if ($matches->isNotEmpty()) {
            $sections[] = "Pour ne pas partir de zero, tu peux surveiller :\n".$matches
                ->map(fn (array $match): string => '- '.$match['title'])
                ->implode("\n");
        }

        $sections[] = 'Le meilleur prochain pas : '.($this->contextLink($context, 'Paris') ?: 'ouvrir Paris').' ou '.($this->contextLink($context, 'Matchs') ?: 'ouvrir Matchs').' pour voir les opportunites reellement disponibles.';

        return new AssistantResponse(
            content: trim(implode("\n\n", array_filter($sections))),
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function rewardReply(array $userContext, array $context): AssistantResponse
    {
        $points = (int) Arr::get($userContext, 'wallets.points', 0);
        $gifts = collect($userContext['gift_highlights'] ?? [])->take(3);

        $sections = [
            "Tu disposes actuellement de {$points} points sur la plateforme.",
        ];

        if ($gifts->isNotEmpty()) {
            $sections[] = "Voici ce qui peut etre interessant a regarder :\n".$gifts
                ->map(function (array $gift): string {
                    $status = ($gift['reachable'] ?? false) ? 'accessible maintenant' : 'a preparer';

                    return '- '.$gift['title'].' - '.$gift['cost_points'].' pts - '.$status;
                })
                ->implode("\n");
        } else {
            $sections[] = 'Je n ai pas de recompense fiable a citer dans le contexte actuel, donc le plus propre est de verifier directement le catalogue cadeaux.';
        }

        $sections[] = 'Le meilleur prochain pas : '.($this->contextLink($context, 'Cadeaux') ?: 'ouvrir Cadeaux').' pour verifier ce que ton solde peut deja debloquer.';

        return new AssistantResponse(
            content: trim(implode("\n\n", array_filter($sections))),
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function supporterReply(array $userContext, array $context): AssistantResponse
    {
        $supporter = Arr::get($userContext, 'supporter', []);
        $isActive = (bool) ($supporter['is_active'] ?? false);
        $planName = (string) ($supporter['current_plan_name'] ?? '');
        $benefits = [
            'badge supporter visible sur la plateforme',
            'missions exclusives et bonus lies au programme',
            'votes clips, reactions premium et avantages communautaires',
        ];

        $sections = [];

        if ($isActive) {
            $sections[] = $planName !== ''
                ? 'Ton statut supporter est deja actif via la formule '.$planName.'.'
                : 'Ton statut supporter est deja actif sur ERAH.';
            $sections[] = 'Le plus utile maintenant est d ouvrir '.($this->contextLink($context, 'Supporter') ?: 'la page Supporter').' pour gerer ton abonnement, verifier tes avantages et suivre tes missions reservees.';
        } else {
            $sections[] = 'Oui. Pour devenir supporter sur ERAH, il faut passer par la page Supporter, comparer les formules puis lancer le checkout securise.';
            $sections[] = "En general, cela debloque notamment :\n- ".implode("\n- ", $benefits);
            $sections[] = 'Le bon prochain pas : '.($this->contextLink($context, 'Supporter') ?: 'ouvrir la page Supporter').' pour choisir la formule qui te convient.';
        }

        return new AssistantResponse(
            content: trim(implode("\n\n", array_filter($sections))),
            provider: 'knowledge-base',
            model: 'local-fallback',
            metadata: [
                'next_steps' => [
                    $isActive ? 'Verifier votre console supporter' : 'Comparer les formules supporter puis lancer le checkout',
                ],
            ],
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function notificationReply(array $userContext, array $context): AssistantResponse
    {
        $unread = (int) Arr::get($userContext, 'notifications_unread', 0);

        $content = trim(implode("\n\n", array_filter([
            $unread > 0
                ? "Tu as {$unread} notification".($unread > 1 ? 's non lues' : ' non lue').' dans ta console.'
                : 'Je ne vois pas de notification non lue dans le contexte disponible.',
            'Le plus utile est de passer par '.($this->contextLink($context, 'Notifications') ?: 'Notifications').' pour verifier ce qui demande une action ou un simple suivi.',
        ])));

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function pointsReply(array $userContext, array $context): AssistantResponse
    {
        $points = (int) Arr::get($userContext, 'wallets.points', 0);
        $xp = (int) Arr::get($userContext, 'progress.xp', 0);
        $actions = collect($userContext['recommended_actions'] ?? [])->take(3);

        $sections = [
            "A ce stade, tu as {$points} points et {$xp} XP.",
            'Pour gagner ou relancer ta progression, les missions actives et le suivi des matchs restent en general les premiers leviers a regarder.',
        ];

        if ($actions->isNotEmpty()) {
            $sections[] = "Les actions qui ont le plus de sens pour toi maintenant :\n".$actions
                ->map(fn (array $action): string => '- '.$action['label'].' : '.$action['description'])
                ->implode("\n");
        }

        $sections[] = 'Le meilleur prochain pas : '.($this->contextLink($context, 'Missions') ?: 'ouvrir Missions').' pour identifier ce qui peut te faire progresser rapidement.';

        return new AssistantResponse(
            content: trim(implode("\n\n", array_filter($sections))),
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function profileReply(array $userContext, array $context): AssistantResponse
    {
        $suggestions = collect($userContext['profile_suggestions'] ?? [])
            ->map(fn (string $item): string => '- '.$item)
            ->all();

        $content = trim(implode("\n\n", array_filter([
            'Pour renforcer ton profil ERAH, commence par les elements qui ameliorent tout de suite sa lisibilite et sa credibilite dans la plateforme.',
            $suggestions !== [] ? implode("\n", $suggestions) : null,
            'Le meilleur prochain pas : '.($this->contextLink($context, 'Profil') ?: 'ouvrir ton Profil').' pour finaliser ce qui manque et verifier le rendu public.',
        ])));

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function nextStepReply(array $userContext, array $context): AssistantResponse
    {
        $actions = collect($userContext['recommended_actions'] ?? [])
            ->map(fn (array $action): string => '- '.$action['label'].' : '.$action['description'])
            ->all();

        $content = trim(implode("\n\n", array_filter([
            'Si tu veux avancer sans te disperser sur ERAH, voici les prochaines actions qui ont le plus de valeur d apres ton contexte actuel.',
            $actions !== [] ? implode("\n", $actions) : null,
            'Si tu veux une seule recommandation claire, commence par '.($this->contextLink($context, 'Missions') ?: 'Missions').' ou dis-moi ta priorite exacte pour que je t oriente plus finement.',
        ])));

        return new AssistantResponse(
            content: $content,
            provider: 'knowledge-base',
            model: 'local-fallback',
        );
    }

    /**
     * @param array<string, mixed> $knowledge
     * @param array<string, mixed> $userContext
     * @param array<string, mixed> $context
     */
    private function knowledgeReply(array $knowledge, array $userContext, array $context): string
    {
        $sections = [];
        $confidence = $knowledge['confidence'] ?? null;
        $answer = trim((string) ($knowledge['answer'] ?? ''));

        if ($confidence === 'fallback') {
            $sections[] = 'Je vois le sujet, mais je prefere rester prudent plutot que t inventer une reponse approximative.';
        }

        $sections[] = $answer !== '' ? $answer : 'Je n ai pas trouve de reponse fiable pour le moment.';

        $details = collect($knowledge['details'] ?? [])->filter()->values()->all();
        if ($details !== []) {
            $sections[] = implode("\n\n", $details);
        }

        $nextSteps = collect($knowledge['next_steps'] ?? [])
            ->filter()
            ->map(fn (string $step): string => '- '.$step)
            ->values()
            ->all();

        if ($nextSteps !== []) {
            $sections[] = "Si tu veux aller plus loin :\n".implode("\n", $nextSteps);
        }

        if ($confidence === 'fallback' && ($userContext['recommended_actions'] ?? []) !== []) {
            $actions = collect($userContext['recommended_actions'])
                ->map(fn (array $action): string => '- '.$action['label'].' : '.$action['description'])
                ->values()
                ->all();

            $sections[] = "Ce que tu peux faire tout de suite :\n".implode("\n", $actions);
        }

        if ($confidence === 'fallback' && $nextSteps === [] && ($contextLink = $this->contextLink($context, 'Aide'))) {
            $sections[] = 'Pour rester sur une base fiable, tu peux aussi verifier '.$contextLink.'.';
        }

        return trim(implode("\n\n", array_filter($sections)));
    }

    private function looksLikeOverviewQuestion(string $message): bool
    {
        return Str::contains($message, [
            'comment fonctionne erah',
            'comment marche erah',
            'comment ca marche',
            'c est quoi erah',
            'je debute',
            'nouveau sur erah',
            'par quoi commencer',
            'premiers pas',
            'commencer sur la plateforme',
        ]);
    }

    private function looksLikeMissionQuestion(string $message): bool
    {
        return Str::contains($message, ['mission', 'missions', 'objectif', 'objectifs', 'quete', 'quotidienne', 'hebdomadaire']);
    }

    private function looksLikeMatchesQuestion(string $message): bool
    {
        return Str::contains($message, ['match', 'matchs', 'rencontre', 'calendrier', 'arrive bientot', 'match a venir', 'matchs a venir']);
    }

    private function looksLikeBetQuestion(string $message): bool
    {
        return Str::contains($message, ['pari', 'paris', 'bet', 'bets', 'miser', 'mise']);
    }

    private function looksLikeRewardsQuestion(string $message): bool
    {
        return Str::contains($message, ['reward', 'recompense', 'cadeau', 'cadeaux', 'gift', 'gifts']);
    }

    private function looksLikeSupporterQuestion(string $message): bool
    {
        return Str::contains($message, [
            'supporter',
            'devenir supporter',
            'soutenir erah',
            'abonnement supporter',
            'badge supporter',
            'formule supporter',
            'avantage supporter',
        ]);
    }

    private function looksLikeNotificationQuestion(string $message): bool
    {
        return Str::contains($message, ['notification', 'notifications', 'alerte', 'alertes', 'rappel']);
    }

    private function looksLikePointsQuestion(string $message): bool
    {
        return Str::contains($message, ['point', 'points', 'xp', 'progression', 'wallet', 'solde', 'gagner des points']);
    }

    private function looksLikeProfileQuestion(string $message): bool
    {
        return Str::contains($message, ['profil', 'avatar', 'bio', 'compte public']);
    }

    private function looksLikeNextStepQuestion(string $message): bool
    {
        return Str::contains($message, [
            'quoi faire ensuite',
            'que faire ensuite',
            'que puis je faire',
            'que dois je faire',
            'je dois faire quoi',
            'je fais quoi maintenant',
            'quoi faire maintenant',
            'que faire maintenant',
            'par quoi commencer',
            'next step',
            'prochaine action',
            'ensuite',
        ]);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function contextLink(array $context, string $label): ?string
    {
        $url = collect(Arr::get($context, 'links', []))
            ->firstWhere('label', $label)['url'] ?? null;

        return filled($url) ? '['.$label.']('.$url.')' : null;
    }
}
