<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gifts', function (Blueprint $table): void {
            $table->string('slug', 160)->nullable()->after('key');
        });

        DB::table('gifts')
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get()
            ->each(function (object $gift): void {
                $baseSlug = Str::slug((string) $gift->title);
                $baseSlug = $baseSlug !== '' ? Str::limit($baseSlug, 150, '') : 'gift-'.$gift->id;
                $slug = $baseSlug;
                $suffix = 2;

                while (DB::table('gifts')
                    ->where('slug', $slug)
                    ->where('id', '!=', $gift->id)
                    ->exists()) {
                    $slug = Str::limit($baseSlug, 145, '').'-'.$suffix;
                    $suffix++;
                }

                DB::table('gifts')
                    ->where('id', $gift->id)
                    ->update(['slug' => $slug]);
            });

        Schema::table('gifts', function (Blueprint $table): void {
            $table->unique('slug', 'gifts_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('gifts', function (Blueprint $table): void {
            $table->dropUnique('gifts_slug_unique');
            $table->dropColumn('slug');
        });
    }
};