<?php

namespace App\Services;

use App\Models\PlatformEvent;
use Illuminate\Support\Collection;

class EventService
{
    /**
     * @return Collection<int, PlatformEvent>
     */
    public function active(?string $type = null): Collection
    {
        return PlatformEvent::query()
            ->activeWindow()
            ->when($type !== null, fn ($query) => $query->where('type', $type))
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * @param array<string, int> $rewards
     * @return array<string, int>
     */
    public function applyModifiers(array $rewards, ?string $type = null): array
    {
        $result = $rewards;

        foreach ($this->active() as $event) {
            if ($event->type !== 'double_xp' && $type !== null && $event->type !== $type) {
                continue;
            }

            $config = is_array($event->config) ? $event->config : [];
            if ($event->type === 'double_xp') {
                $multiplier = (float) ($config['xp_multiplier'] ?? 2);
                $result['xp'] = (int) round(((int) ($result['xp'] ?? 0)) * $multiplier);

                continue;
            }

            $result['xp'] = (int) round(((int) ($result['xp'] ?? 0)) * (float) ($config['xp_multiplier'] ?? 1));
            $result['rank_points'] = (int) round(((int) ($result['rank_points'] ?? 0)) * (float) ($config['rank_points_multiplier'] ?? 1));
            $result['reward_points'] = (int) round(((int) ($result['reward_points'] ?? 0)) * (float) ($config['reward_points_multiplier'] ?? 1));
            $result['bet_points'] = (int) round(((int) ($result['bet_points'] ?? 0)) * (float) ($config['bet_points_multiplier'] ?? 1));
            $result['duel_score'] = (int) round(((int) ($result['duel_score'] ?? 0)) * (float) ($config['duel_score_multiplier'] ?? 1));

            $result['xp'] += (int) ($config['xp_bonus'] ?? 0);
            $result['rank_points'] += (int) ($config['rank_points_bonus'] ?? 0);
            $result['reward_points'] += (int) ($config['reward_points_bonus'] ?? 0);
            $result['bet_points'] += (int) ($config['bet_points_bonus'] ?? 0);
            $result['duel_score'] += (int) ($config['duel_score_bonus'] ?? 0);
        }

        return $result;
    }
}
