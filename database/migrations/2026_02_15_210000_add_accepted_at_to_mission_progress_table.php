<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mission_progress', function (Blueprint $table): void {
            $table->dateTime('accepted_at')->nullable()->after('period_key');
            $table->index(['user_id', 'accepted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('mission_progress', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'accepted_at']);
            $table->dropColumn('accepted_at');
        });
    }
};
