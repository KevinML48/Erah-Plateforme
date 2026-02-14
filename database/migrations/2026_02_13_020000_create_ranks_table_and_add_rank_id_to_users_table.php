<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('min_points');
            $table->string('badge_color')->nullable();
            $table->timestamps();

            $table->index('min_points');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rank_id')
                ->nullable()
                ->after('points_balance')
                ->constrained('ranks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rank_id');
        });

        Schema::dropIfExists('ranks');
    }
};

