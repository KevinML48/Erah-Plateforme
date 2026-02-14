<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('USER')->after('is_admin');
                $table->index('role', 'users_role_idx');
            }
        });

        if (Schema::hasColumn('users', 'is_admin')) {
            DB::table('users')
                ->where('is_admin', true)
                ->update(['role' => 'ADMIN']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex('users_role_idx');
                $table->dropColumn('role');
            }
        });
    }
};
