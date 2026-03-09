<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_progress', function (Blueprint $table): void {
            $table->unsignedInteger('duel_current_streak')->default(0)->after('duel_losses');
            $table->unsignedInteger('duel_best_streak')->default(0)->after('duel_current_streak');
        });

        Schema::table('quiz_questions', function (Blueprint $table): void {
            $table->string('question_type', 40)->default('single_choice')->after('prompt');
            $table->text('accepted_answer')->nullable()->after('explanation');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table): void {
            $table->dropColumn(['question_type', 'accepted_answer']);
        });

        Schema::table('user_progress', function (Blueprint $table): void {
            $table->dropColumn(['duel_current_streak', 'duel_best_streak']);
        });
    }
};
