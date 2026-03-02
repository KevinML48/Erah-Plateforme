<?php

namespace App\Application\Actions\Notifications;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\DB;

class RegisterUserDeviceAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(User $user, array $payload): UserDevice
    {
        return DB::transaction(function () use ($user, $payload) {
            $device = UserDevice::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'platform' => strtolower((string) $payload['platform']),
                    'device_token' => trim((string) $payload['device_token']),
                ],
                [
                    'device_name' => $payload['device_name'] ?? null,
                    'is_active' => array_key_exists('is_active', $payload)
                        ? (bool) $payload['is_active']
                        : true,
                    'meta' => $payload['meta'] ?? null,
                    'last_seen_at' => now(),
                ]
            );

            $this->storeAuditLogAction->execute(
                action: 'notifications.device.upserted',
                actor: $user,
                target: $device,
                context: [
                    'platform' => $device->platform,
                    'is_active' => $device->is_active,
                ],
            );

            return $device;
        });
    }
}
