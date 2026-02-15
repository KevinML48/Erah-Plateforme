<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountDeletionService
{
    public function __construct(
        private readonly LeaderboardService $leaderboardService
    ) {
    }

    public function purge(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedUser) {
                return;
            }

            $userId = (int) $lockedUser->id;
            $email = (string) $lockedUser->email;

            // Purge sessions + reset tokens linked to the account.
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $userId)->delete();
            }

            if ($email !== '' && Schema::hasTable('password_reset_tokens')) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
            }

            // Purge role/permission assignments (polymorphic tables without FK to users).
            $modelMorphKey = config('permission.column_names.model_morph_key', 'model_id');
            $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
            $modelHasPermissionsTable = config('permission.table_names.model_has_permissions', 'model_has_permissions');

            if (Schema::hasTable($modelHasRolesTable)) {
                DB::table($modelHasRolesTable)
                    ->where('model_type', User::class)
                    ->where($modelMorphKey, $userId)
                    ->delete();
            }

            if (Schema::hasTable($modelHasPermissionsTable)) {
                DB::table($modelHasPermissionsTable)
                    ->where('model_type', User::class)
                    ->where($modelMorphKey, $userId)
                    ->delete();
            }

            // Purge token-based auth data if Sanctum is enabled.
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', User::class)
                    ->where('tokenable_id', $userId)
                    ->delete();
            }

            // Purge audit lines authored by the user.
            if (Schema::hasTable('admin_audit_logs')) {
                DB::table('admin_audit_logs')->where('actor_user_id', $userId)->delete();
            }

            if (Schema::hasTable('audit_logs')) {
                DB::table('audit_logs')->where('actor_user_id', $userId)->delete();
            }

            // Explicit cleanup for user-bound tables (in addition to FK cascade).
            foreach ([
                'points_logs',
                'leaderboard_stats',
                'predictions',
                'tickets',
                'reward_redemptions',
                'user_events',
                'mission_progress',
                'user_streaks',
            ] as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->where('user_id', $userId)->delete();
                }
            }

            $lockedUser->delete();
        });

        $this->leaderboardService->invalidateCache();
    }
}

