<?php

namespace App\Services;

use App\Models\MissionTemplate;
use App\Models\User;

class MissionConstraintEvaluator
{
    /**
     * @param array<string, mixed> $context
     */
    public function passes(User $user, MissionTemplate $template, array $context = []): bool
    {
        $constraints = is_array($template->constraints) ? $template->constraints : [];

        if (($constraints['supporter_only'] ?? false) && ! $user->isSupporterActive()) {
            return false;
        }

        if (! $this->passesMinimum($constraints, $context, 'min_stake', 'stake_points')) {
            return false;
        }

        if (! $this->passesMaximum($constraints, $context, 'max_stake', 'stake_points')) {
            return false;
        }

        if (! $this->passesMinimum($constraints, $context, 'min_profile_completion', 'profile_completion')) {
            return false;
        }

        if (! $this->passesMinimum($constraints, $context, 'min_absence_days', 'absence_days')) {
            return false;
        }

        if (! $this->passesMinimum($constraints, $context, 'min_level', 'level')) {
            return false;
        }

        if (! $this->passesRequiredRank($constraints, $context)) {
            return false;
        }

        if (isset($constraints['channel']) && (string) $constraints['channel'] !== (string) ($context['channel'] ?? '')) {
            return false;
        }

        if (isset($constraints['required_status']) && (string) $constraints['required_status'] !== (string) ($context['status'] ?? '')) {
            return false;
        }

        if (($constraints['first_time_only'] ?? false) && ! ((bool) ($context['is_first_time'] ?? false))) {
            return false;
        }

        if (($constraints['login_daily'] ?? false) && ! ((bool) ($context['is_daily_login'] ?? false))) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $context
     */
    private function passesMinimum(array $constraints, array $context, string $constraintKey, string $contextKey): bool
    {
        if (! isset($constraints[$constraintKey])) {
            return true;
        }

        return (int) ($context[$contextKey] ?? 0) >= (int) $constraints[$constraintKey];
    }

    /**
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $context
     */
    private function passesMaximum(array $constraints, array $context, string $constraintKey, string $contextKey): bool
    {
        if (! isset($constraints[$constraintKey])) {
            return true;
        }

        return (int) ($context[$contextKey] ?? 0) <= (int) $constraints[$constraintKey];
    }

    /**
     * @param array<string, mixed> $constraints
     * @param array<string, mixed> $context
     */
    private function passesRequiredRank(array $constraints, array $context): bool
    {
        if (! isset($constraints['required_rank'])) {
            return true;
        }

        $requiredRank = MissionTemplate::normalizeEventType((string) $constraints['required_rank']);
        $currentRank = MissionTemplate::normalizeEventType((string) ($context['rank_key'] ?? ''));

        if ($requiredRank === '' || $currentRank === '') {
            return false;
        }

        $order = collect((array) config('community.xp_leagues', []))
            ->pluck('key')
            ->map(fn (mixed $key): string => MissionTemplate::normalizeEventType((string) $key))
            ->values();

        $requiredIndex = $order->search($requiredRank, true);
        $currentIndex = $order->search($currentRank, true);

        if ($requiredIndex === false || $currentIndex === false) {
            return false;
        }

        return $currentIndex >= $requiredIndex;
    }
}
