<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->foreignId('game_id')->nullable()->after('id')->constrained('games')->nullOnDelete();
            $table->string('format', 10)->nullable()->after('title');
            $table->dateTime('lock_at')->nullable()->after('starts_at');
            $table->json('result_json')->nullable()->after('result');

            $table->index('game_id');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->dropIndex(['game_id']);
            $table->dropConstrainedForeignId('game_id');
            $table->dropColumn(['format', 'lock_at', 'result_json']);
        });
    }
};

