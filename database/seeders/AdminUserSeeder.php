<?php

namespace Database\Seeders;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = $this->seedLocalAdmin();
            $platformAdmin = $this->seedPlatformAdmin();

            $auditActor = $admin ?? $platformAdmin;

            app(StoreAuditLogAction::class)->execute(
                action: 'seed.admin_user.upserted',
                actor: $auditActor,
                target: $platformAdmin,
                context: [
                    'seed_class' => self::class,
                    'platform_admin_id' => $platformAdmin->id,
                    'platform_admin_email' => $platformAdmin->email,
                ],
            );
        });
    }

    private function seedLocalAdmin(): ?User
    {
        if (app()->environment('production')) {
            return null;
        }

        return User::query()->updateOrCreate(
            ['email' => 'admin@erah.local'],
            [
                'name' => 'ERAH Admin',
                'password' => Hash::make((string) env('ADMIN_SEED_PASSWORD', 'ChangeMe123!')),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedPlatformAdmin(): User
    {
        $email = trim((string) env('PLATFORM_ADMIN_EMAIL', 'erah.association@gmail.com'));
        $name = trim((string) env('PLATFORM_ADMIN_NAME', 'ERAH Association'));
        $password = (string) env(
            'PLATFORM_ADMIN_PASSWORD',
            app()->environment('production') ? '' : 'SeedAdmin!2026'
        );

        if ($email === '') {
            throw new RuntimeException('PLATFORM_ADMIN_EMAIL ne peut pas etre vide.');
        }

        $this->assertSecurePassword($password);

        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name !== '' ? $name : 'ERAH Association',
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }

    private function assertSecurePassword(string $password): void
    {
        if (strlen($password) < 12) {
            throw new RuntimeException('PLATFORM_ADMIN_PASSWORD doit contenir au moins 12 caracteres.');
        }

        $hasUpper = preg_match('/[A-Z]/', $password) === 1;
        $hasLower = preg_match('/[a-z]/', $password) === 1;
        $hasDigit = preg_match('/\d/', $password) === 1;
        $hasSymbol = preg_match('/[^A-Za-z0-9]/', $password) === 1;

        if (! $hasUpper || ! $hasLower || ! $hasDigit || ! $hasSymbol) {
            throw new RuntimeException(
                'PLATFORM_ADMIN_PASSWORD doit contenir une majuscule, une minuscule, un chiffre et un caractere special.'
            );
        }
    }
}
