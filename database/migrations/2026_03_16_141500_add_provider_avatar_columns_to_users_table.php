<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_avatar_url', 2048)->nullable()->after('avatar_path');
            $table->string('provider_avatar_provider', 20)->nullable()->after('provider_avatar_url');
        });

        $accountsByUser = DB::table('social_accounts')
            ->select(['user_id', 'provider', 'avatar_url', 'updated_at'])
            ->whereIn('provider', ['discord', 'google'])
            ->whereNotNull('avatar_url')
            ->orderBy('user_id')
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('user_id');

        foreach ($accountsByUser as $userId => $accounts) {
            $account = collect($accounts)->first(function (object $account): bool {
                $url = trim((string) $account->avatar_url);

                return $url !== '' && filter_var($url, FILTER_VALIDATE_URL) !== false;
            });

            if (! $account) {
                continue;
            }

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'provider_avatar_url' => $account->avatar_url,
                    'provider_avatar_provider' => $account->provider,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider_avatar_url', 'provider_avatar_provider']);
        });
    }
};