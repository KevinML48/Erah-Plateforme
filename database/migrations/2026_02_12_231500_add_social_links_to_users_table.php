<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('facebook')->nullable()->after('tax_id');
            $table->string('x_url')->nullable()->after('facebook');
            $table->string('linkedin')->nullable()->after('x_url');
            $table->string('instagram')->nullable()->after('linkedin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['facebook', 'x_url', 'linkedin', 'instagram']);
        });
    }
};

