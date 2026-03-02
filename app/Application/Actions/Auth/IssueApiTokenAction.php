<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IssueApiTokenAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(
        User $user,
        string $deviceName,
        string $reason,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): string {
        return DB::transaction(function () use ($user, $deviceName, $reason, $ipAddress, $userAgent) {
            $plainTextToken = $user->createToken($deviceName)->plainTextToken;

            $this->storeAuditLogAction->execute(
                action: 'auth.token.issued',
                actor: $user,
                target: $user,
                context: [
                    'reason' => $reason,
                    'device_name' => $deviceName,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ],
            );

            return $plainTextToken;
        });
    }
}
