<?php

namespace App\Application\Actions\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class StoreAuditLogAction
{
    public function execute(
        string $action,
        ?Model $actor = null,
        ?Model $target = null,
        array $context = []
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'target_type' => $target ? $target::class : null,
            'target_id' => $target?->getKey(),
            'context' => $context,
            'created_at' => now(),
        ]);
    }
}
