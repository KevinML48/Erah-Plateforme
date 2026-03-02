<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterUserAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(array $payload, ?string $ipAddress = null, ?string $userAgent = null): User
    {
        return DB::transaction(function () use ($payload, $ipAddress, $userAgent) {
            $user = User::query()->create([
                'name' => $payload['name'],
                'email' => Str::lower($payload['email']),
                'password' => $payload['password'],
                'role' => User::ROLE_USER,
            ]);

            $this->storeAuditLogAction->execute(
                action: 'auth.registered',
                actor: $user,
                target: $user,
                context: [
                    'auth_method' => 'password',
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ],
            );

            return $user;
        });
    }
}
