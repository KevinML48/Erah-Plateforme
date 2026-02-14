<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table): void {
            $table->unsignedInteger('stake_points')->default(0)->after('prediction');
            $table->unsignedInteger('potential_points')->default(0)->after('stake_points');
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table): void {
            $table->dropColumn(['stake_points', 'potential_points']);
        });
    }
};

