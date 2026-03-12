<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_purchases')) {
            return;
        }

        if (! Schema::hasColumn('user_purchases', 'idempotency_key')) {
            Schema::table('user_purchases', function (Blueprint $table): void {
                $table->string('idempotency_key', 120)->nullable()->after('status');
            });
        }

        DB::table('user_purchases')
            ->whereNull('idempotency_key')
            ->orderBy('id')
            ->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('user_purchases')
                        ->where('id', $row->id)
                        ->update(['idempotency_key' => 'legacy-shop-'.$row->id]);
                }
            });

        Schema::table('user_purchases', function (Blueprint $table): void {
            $table->unique(['user_id', 'idempotency_key'], 'user_purchases_user_idempotency_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_purchases')) {
            return;
        }

        Schema::table('user_purchases', function (Blueprint $table): void {
            $table->dropUnique('user_purchases_user_idempotency_unique');
        });

        if (Schema::hasColumn('user_purchases', 'idempotency_key')) {
            Schema::table('user_purchases', function (Blueprint $table): void {
                $table->dropColumn('idempotency_key');
            });
        }
    }
};

