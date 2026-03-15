<?php

namespace App\Services\GuidedTour;

use App\Models\HelpTourStep;
use App\Models\User;
use App\Models\UserGuidedTour;
use Illuminate\Support\Facades\Schema;

class PlatformGuidedTourService
{
    public const TOUR_KEY = 'platform-onboarding';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function steps(): array
    {
        $records = HelpTourStep::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('step_number')
            ->get()
            ->keyBy('step_number');

        return collect($this->stepBlueprints())
            ->map(function (array $blueprint, int $stepNumber) use ($records): array {
                $record = $records->get($stepNumber);

                return [
                    'id' => $blueprint['id'],
                    'step_number' => $stepNumber,
                    'title' => (string) ($record?->title ?: $blueprint['title']),
                    'summary' => (string) ($record?->summary ?: $blueprint['summary']),
                    'description' => (string) ($record?->body ?: $blueprint['description']),
                    'route' => $blueprint['route'],
                    'selector' => $blueprint['selector'],
                    'placement' => $blueprint['placement'],
                    'fallback_title' => $blueprint['fallback_title'],
                    'fallback_body' => $blueprint['fallback_body'],
                    'visual_title' => (string) ($record?->visual_title ?: $blueprint['visual_title']),
                    'visual_body' => (string) ($record?->visual_body ?: $blueprint['visual_body']),
                    'progress_label' => sprintf('%d/%d', $stepNumber, count($this->stepBlueprints())),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function entryPayload(?User $user): array
    {
        $totalSteps = count($this->steps());

        if (! $user) {
            return [
                'available' => false,
                'requires_auth' => true,
                'status' => 'guest',
                'status_badge' => 'Connexion requise',
                'progress_text' => sprintf("%d étapes interactives réelles", $totalSteps),
                'primary_label' => 'Se connecter pour lancer la visite',
                'primary_action' => null,
                'primary_url' => route('login'),
                'secondary_label' => null,
                'secondary_action' => null,
                'current_step_title' => null,
            ];
        }

        if (! $this->isPersistenceReady()) {
            return [
                'available' => false,
                'requires_auth' => false,
                'status' => 'unavailable',
                'status_badge' => 'Configuration requise',
                'progress_text' => 'La visite sera disponible des que la base est a jour.',
                'primary_label' => null,
                'primary_action' => null,
                'primary_url' => null,
                'secondary_label' => null,
                'secondary_action' => null,
                'current_step_title' => null,
            ];
        }

        $summary = $this->summaryFor($user);

        return [
            'available' => true,
            'requires_auth' => false,
            'status' => $summary['status'],
            'status_badge' => $summary['status_badge'],
            'progress_text' => $summary['progress_text'],
            'primary_label' => $summary['primary_label'],
            'primary_action' => $summary['primary_action'],
            'primary_url' => null,
            'secondary_label' => $summary['secondary_label'],
            'secondary_action' => $summary['secondary_action'],
            'current_step_title' => $summary['current_step_title'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryFor(User $user): array
    {
        $steps = $this->steps();
        $totalSteps = count($steps);

        if (! $this->isPersistenceReady()) {
            return [
                'status' => 'unavailable',
                'status_badge' => 'Configuration requise',
                'progress_text' => 'La visite sera disponible des que la base est a jour.',
                'primary_label' => null,
                'primary_action' => null,
                'secondary_label' => null,
                'secondary_action' => null,
                'current_step_index' => 0,
                'current_step_title' => null,
                'is_paused' => false,
                'is_complèted' => false,
                'current_step_url' => route('console.help'),
            ];
        }

        $progress = $this->progressFor($user);

        if (! $progress) {
            return [
                'status' => 'not_started',
                'status_badge' => 'Visite jamais lancee',
                'progress_text' => sprintf('%d etapes interactives a parcourir', $totalSteps),
                'primary_label' => 'Commencer la visite',
                'primary_action' => 'start',
                'secondary_label' => null,
                'secondary_action' => null,
                'current_step_index' => 0,
                'current_step_title' => null,
                'is_paused' => false,
                'is_complèted' => false,
                'current_step_url' => $steps[0]['route'] ?? route('console.help'),
            ];
        }

        $currentIndex = $this->boundedStepIndex((int) $progress->current_step_index, $totalSteps);
        $currentStep = $steps[$currentIndex] ?? null;
        $isCompleted = $progress->status === UserGuidedTour::STATUS_COMPLETED;

        return [
            'status' => $progress->status,
            'status_badge' => $isCompleted
                ? 'Visite terminee'
                : ($progress->is_paused ? 'Visite en pause' : 'Visite en cours'),
            'progress_text' => $isCompleted
                ? sprintf('Parcours termine sur %d etapes', $totalSteps)
                : sprintf('Etape %d sur %d', $currentIndex + 1, $totalSteps),
            'primary_label' => $isCompleted
                ? 'Revoir la visite'
                : ($progress->is_paused ? 'Reprendre la visite' : 'Continuer la visite'),
            'primary_action' => $isCompleted ? 'restart' : 'resume',
            'secondary_label' => $isCompleted ? null : 'Recommencer depuis le debut',
            'secondary_action' => $isCompleted ? null : 'restart',
            'current_step_index' => $currentIndex,
            'current_step_title' => $currentStep['title'] ?? null,
            'is_paused' => (bool) $progress->is_paused,
            'is_complèted' => $isCompleted,
            'current_step_url' => $currentStep['route'] ?? route('console.help'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function bootstrapFor(User $user): array
    {
        if (! $this->isPersistenceReady()) {
            return [
                'enabled' => false,
            ];
        }

        return [
            'enabled' => true,
            'tour_key' => self::TOUR_KEY,
            'steps' => $this->steps(),
            'state' => $this->summaryFor($user),
            'csrf' => csrf_token(),
            'endpoints' => [
                'show' => route('guided-tour.show'),
                'start' => route('guided-tour.start'),
                'restart' => route('guided-tour.restart'),
                'update' => route('guided-tour.update'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function show(User $user): array
    {
        return [
            'steps' => $this->steps(),
            'state' => $this->summaryFor($user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function start(User $user): array
    {
        if (! $this->isPersistenceReady()) {
            return $this->show($user);
        }

        $progress = $this->progressFor($user, create: true);
        $progress->status = UserGuidedTour::STATUS_IN_PROGRESS;
        $progress->current_step_index = 0;
        $progress->is_paused = false;
        $progress->started_at = $progress->started_at ?: now();
        $progress->last_seen_at = now();
        $progress->complèted_at = null;
        $progress->save();

        return $this->show($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function restart(User $user): array
    {
        if (! $this->isPersistenceReady()) {
            return $this->show($user);
        }

        $progress = $this->progressFor($user, create: true);
        $progress->status = UserGuidedTour::STATUS_IN_PROGRESS;
        $progress->current_step_index = 0;
        $progress->is_paused = false;
        $progress->started_at = now();
        $progress->last_seen_at = now();
        $progress->complèted_at = null;
        $progress->save();

        return $this->show($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function update(User $user, string $action): array
    {
        if (! $this->isPersistenceReady()) {
            return $this->show($user);
        }

        $steps = $this->steps();
        $lastIndex = max(0, count($steps) - 1);
        $progress = $this->progressFor($user, create: true);
        $currentIndex = $this->boundedStepIndex((int) $progress->current_step_index, count($steps));

        if ($progress->status !== UserGuidedTour::STATUS_COMPLETED && ! $progress->started_at) {
            $progress->started_at = now();
        }

        if ($action === 'previous') {
            $progress->status = UserGuidedTour::STATUS_IN_PROGRESS;
            $progress->current_step_index = max(0, $currentIndex - 1);
            $progress->is_paused = false;
            $progress->complèted_at = null;
        }

        if ($action === 'next') {
            if ($currentIndex >= $lastIndex) {
                $progress->status = UserGuidedTour::STATUS_COMPLETED;
                $progress->current_step_index = $lastIndex;
                $progress->is_paused = true;
                $progress->complèted_at = now();
            } else {
                $progress->status = UserGuidedTour::STATUS_IN_PROGRESS;
                $progress->current_step_index = $currentIndex + 1;
                $progress->is_paused = false;
                $progress->complèted_at = null;
            }
        }

        if ($action === 'pause') {
            if ($progress->status === UserGuidedTour::STATUS_COMPLETED) {
                $progress->is_paused = true;
                $progress->last_seen_at = now();
                $progress->save();

                return $this->show($user);
            }

            $progress->status = UserGuidedTour::STATUS_IN_PROGRESS;
            $progress->current_step_index = $currentIndex;
            $progress->is_paused = true;
        }

        if ($action === 'resume') {
            if ($progress->status !== UserGuidedTour::STATUS_COMPLETED) {
                $progress->status = UserGuidedTour::STATUS_IN_PROGRESS;
                $progress->current_step_index = $currentIndex;
                $progress->is_paused = false;
            }
        }

        $progress->last_seen_at = now();
        $progress->save();

        return $this->show($user);
    }

    public function progressFor(User $user, bool $create = false): ?UserGuidedTour
    {
        if (! $this->isPersistenceReady()) {
            return null;
        }

        $query = UserGuidedTour::query()
            ->where('user_id', $user->id)
            ->where('tour_key', self::TOUR_KEY);

        if ($create) {
            return $query->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'tour_key' => self::TOUR_KEY,
                ],
                [
                    'status' => UserGuidedTour::STATUS_IN_PROGRESS,
                    'current_step_index' => 0,
                    'is_paused' => true,
                ],
            );
        }

        return $query->first();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function stepBlueprints(): array
    {
        return [
            1 => [
                'id' => 'help-hub',
                'title' => "Comprendre le parcours",
                'summary' => "Le centre d'aide sert de point d'entrée pour relancer la visite et retrouver le bon module.",
                'description' => "On commence ici pour garder un fil clair: FAQ, assistant et visite guidee sont regroupes au meme endroit avant de repartir vers la plateforme.",
                'visual_title' => "Point d'entrée unique",
                'visual_body' => "Aide, assistant et parcours interactif au meme endroit.",
                'route' => route('console.help').'#starter-journey',
                'selector' => '[data-tour="help-tour-entry"]',
                'placement' => 'bottom',
                'fallback_title' => "La visite demarre depuis le hub d'aide",
                'fallback_body' => "Si le bloc d'entrée n'est pas visible, vous pouvez quand meme continuer vers le dashboard.",
            ],
            2 => [
                'id' => 'dashboard-modules',
                'title' => "Ouvrir le dashboard",
                'summary' => "Reperez ici la grande grille de modules: C'est votre point de depart pour naviguer partout dans la plateforme.",
                'description' => "Prenez une seconde pour identifier la grille des modules du dashboard. Quand C'est clair pour vous, cliquez sur Suivant: la visite vous emmene ensuite directement vers Matchs.",
                'visual_title' => "Console centrale",
                'visual_body' => "Une vue d'ensemble pour repartir vers les bons modules.",
                'route' => route('dashboard').'#portfolio-grid',
                'selector' => '[data-tour="dashboard-module-grid"]',
                'placement' => 'top',
                'fallback_title' => "Le dashboard est la porte d'entrée principale",
                'fallback_body' => "Si la grille n'est pas encore visible, attendez que la page se charge puis repèrez le grand bloc modules avant de continuer.",
            ],
            3 => [
                'id' => 'matches-overview',
                'title' => "Lire les matchs et la compétition",
                'summary' => "Le module matchs montre les affiches a venir, le live et les résultats.",
                'description' => "C'est ici que vous comprenez le rythme compétitif de la plateforme et, selon le contexte, que vous basculez ensuite vers les bets.",
                'visual_title' => "Match center",
                'visual_body' => "Calendrier, statuts de match et entrée vers la lecture competitive.",
                'route' => route('matches.index'),
                'selector' => '[data-tour="matches-overview"]',
                'placement' => 'top',
                'fallback_title' => "Le module matchs reste la base de la lecture competitive",
                'fallback_body' => "Si la zone ciblee manque, le plus utile est quand meme de regarder les matchs a venir avant de passer a la suite.",
            ],
            4 => [
                'id' => 'missions-overview',
                'title' => "Comprendre la progression",
                'summary' => "Les missions donnent un cap concret et structurent les gains.",
                'description' => "Si vous ne savez pas quoi faire ensuite sur ERAH, commencez ici. Les missions rendent la progression lisible avec objectifs, recompenses et rythme quotidien.",
                'visual_title' => "Boucle de progression",
                'visual_body' => "Objectifs actifs, taux de completion et potentiel de recompenses.",
                'route' => route('missions.index'),
                'selector' => '[data-tour="missions-overview"]',
                'placement' => 'top',
                'fallback_title' => "Les missions structurent la suite du parcours",
                'fallback_body' => "Meme si le bloc cible change, les missions restent la meilleure zone pour savoir quoi faire ensuite.",
            ],
            5 => [
                'id' => 'clips-feed',
                'title' => "Voir la partie communautaire",
                'summary' => "Les clips servent a consulter, interagir et gagner en visibilite.",
                'description' => "C'est ici que la plateforme devient vivante: likes, favoris, commentaires et contenus de la communaute donnent du relief a votre presence.",
                'visual_title' => "Feed communautaire",
                'visual_body' => "Clips publics, interactions et favoris pour rester actif.",
                'route' => route('clips.index'),
                'selector' => '[data-tour="clips-feed"]',
                'placement' => 'top',
                'fallback_title' => "Les clips portent la couche communautaire",
                'fallback_body' => "Si le feed change de place, gardez en tete que les clips restent le cœur de l'engagement visible.",
            ],
            6 => [
                'id' => 'profile-overview',
                'title' => "Revenir a votre profil",
                'summary' => "Le profil rassemble vos repères personnels et les prochaines actions utiles.",
                'description' => "C'est la bonne zone pour verifier votre image publique, votre progression, vos raccourcis et vos favoris assistant. Quand vous etes pret a convertir vos gains, les cadeaux sont la suite naturelle.",
                'visual_title' => "Repere personnel",
                'visual_body' => "Profil, stats visibles, raccourcis et memos utiles pour la suite.",
                'route' => route('profile.show'),
                'selector' => '[data-tour="profile-overview"]',
                'placement' => 'right',
                'fallback_title' => "Le profil sert de repère final",
                'fallback_body' => "Si la zone laterale n'est pas disponible, revenez quand meme sur votre profil pour verifier vos repères et relancer les bons modules.",
            ],
        ];
    }

    private function boundedStepIndex(int $index, int $totalSteps): int
    {
        if ($totalSteps <= 0) {
            return 0;
        }

        return max(0, min($index, $totalSteps - 1));
    }

    private function isPersistenceReady(): bool
    {
        return Schema::hasTable('user_guided_tours');
    }
}
