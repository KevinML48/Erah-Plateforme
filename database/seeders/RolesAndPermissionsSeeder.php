<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'admin.access',
            'users.view',
            'users.manage',
            'points.adjust',
            'rewards.manage',
            'redemptions.manage',
            'matches.manage',
            'settlements.manage',
            'content.manage',
            'settings.manage',
            'audit.view',
            'missions.manage',
            'missions.view_progress',
            'claims.view',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roleMap = [
            'super_admin' => $permissions,
            'admin' => [
                'admin.access',
                'users.view',
                'users.manage',
                'points.adjust',
                'rewards.manage',
                'redemptions.manage',
                'matches.manage',
                'settlements.manage',
                'content.manage',
                'settings.manage',
                'audit.view',
                'missions.manage',
                'missions.view_progress',
                'claims.view',
            ],
            'moderator' => [
                'admin.access',
                'users.view',
                'matches.manage',
                'settlements.manage',
                'content.manage',
                'audit.view',
                'missions.manage',
                'missions.view_progress',
                'claims.view',
            ],
            'logistics' => [
                'admin.access',
                'rewards.manage',
                'redemptions.manage',
                'audit.view',
            ],
            'analyst' => [
                'admin.access',
                'users.view',
                'audit.view',
                'missions.view_progress',
                'claims.view',
            ],
        ];

        foreach ($roleMap as $roleName => $rolePermissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        $superAdminEmail = (string) env('SUPER_ADMIN_EMAIL', '');
        if ($superAdminEmail !== '') {
            $superAdmin = User::query()->where('email', $superAdminEmail)->first();

            if ($superAdmin) {
                $superAdmin->assignRole('super_admin');
                $superAdmin->syncPermissions($permissions);
                $superAdmin->forceFill([
                    'is_admin' => true,
                    'role' => 'ADMIN',
                ])->save();
            }
        }
    }
}
