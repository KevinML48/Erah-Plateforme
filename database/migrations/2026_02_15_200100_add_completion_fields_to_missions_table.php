<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table): void {
            $table->string('completion_rule', 20)->default('ALL')->after('recurrence');
            $table->unsignedSmallInteger('any_n')->nullable()->after('completion_rule');
            $table->index('completion_rule');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table): void {
            $table->dropIndex(['completion_rule']);
            $table->dropColumn(['completion_rule', 'any_n']);
        });
    }
};
