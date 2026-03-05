<?php

namespace Database\Seeders;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::query()->updateOrCreate(
                ['email' => 'admin@erah.local'],
                [
                    'name' => 'ERAH Admin',
                    'password' => Hash::make(env('ADMIN_SEED_PASSWORD', 'ChangeMe123!')),
                    'role' => User::ROLE_ADMIN,
                    'email_verified_at' => now(),
                ]
            );

            $platformAdmin = User::query()->updateOrCreate(
                ['email' => env('PLATFORM_ADMIN_EMAIL', 'admingmail.com')],
                [
                    'name' => 'Platform Admin',
                    'password' => Hash::make(env('PLATFORM_ADMIN_PASSWORD', '12345678')),
                    'role' => User::ROLE_ADMIN,
                    'email_verified_at' => now(),
                ]
            );

            app(StoreAuditLogAction::class)->execute(
                action: 'seed.admin_user.upserted',
                actor: $admin,
                target: $admin,
                context: [
                    'seed_class' => self::class,
                    'platform_admin_id' => $platformAdmin->id,
                ],
            );
        });
    }
}
