<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points_logs', function (Blueprint $table): void {
            $table->string('idempotency_key')->nullable()->after('reference_type');
            $table->unique('idempotency_key', 'points_logs_idempotency_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('points_logs', function (Blueprint $table): void {
            $table->dropUnique('points_logs_idempotency_key_unique');
            $table->dropColumn('idempotency_key');
        });
    }
};

