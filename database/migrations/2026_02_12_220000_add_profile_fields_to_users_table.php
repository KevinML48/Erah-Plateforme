<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('email');
            $table->string('bio')->nullable()->after('phone');
            $table->string('country', 100)->nullable()->after('bio');
            $table->string('city_state', 150)->nullable()->after('country');
            $table->string('postal_code', 50)->nullable()->after('city_state');
            $table->string('tax_id', 100)->nullable()->after('postal_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'bio', 'country', 'city_state', 'postal_code', 'tax_id']);
        });
    }
};
