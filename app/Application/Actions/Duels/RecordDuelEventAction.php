<?php

namespace App\Application\Actions\Duels;

use App\Models\Duel;
use App\Models\DuelEvent;
use App\Models\User;

class RecordDuelEventAction
{
    /**
     * @param array<string, mixed> $meta
     */
    public function execute(
        Duel $duel,
        string $eventType,
        ?User $actor = null,
        array $meta = []
    ): DuelEvent {
        return DuelEvent::query()->create([
            'duel_id' => $duel->id,
            'actor_id' => $actor?->id,
            'event_type' => $eventType,
            'meta' => $meta,
            'occurred_at' => now(),
        ]);
    }
}
