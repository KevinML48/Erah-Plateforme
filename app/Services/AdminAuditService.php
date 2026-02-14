<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\User;

class AdminAuditService
{
    public function log(
        ?User $actor,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $metadata = null
    ): AdminAuditLog {
        $request = request();

        return AdminAuditLog::query()->create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata_json' => $metadata,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}

