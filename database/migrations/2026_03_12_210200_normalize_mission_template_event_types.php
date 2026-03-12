<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mission_templates')) {
            return;
        }

        $remap = [
            'supporter.weekly.clip' => 'clip.like',
            'supporter.exclusive.community' => 'login.daily',
            'supporter.monthly' => 'supporter.monthly',
        ];

        DB::table('mission_templates')
            ->orderBy('id')
            ->chunkById(500, function ($templates) use ($remap): void {
                foreach ($templates as $template) {
                    $raw = (string) ($template->event_type ?? '');
                    $normalized = (string) str($raw)
                        ->trim()
                        ->lower()
                        ->replace([' ', '-', '_'], '.')
                        ->replace('..', '.')
                        ->trim('.');

                    $target = $remap[$normalized] ?? $normalized;

                    if ($target === $raw) {
                        continue;
                    }

                    DB::table('mission_templates')
                        ->where('id', $template->id)
                        ->update([
                            'event_type' => $target,
                            'updated_at' => now(),
                        ]);
                }
            });

        DB::table('mission_templates')
            ->where('key', 'supporter-weekly-clips')
            ->update(['event_type' => 'clip.like', 'updated_at' => now()]);

        DB::table('mission_templates')
            ->where('key', 'supporter-exclusive-community')
            ->update(['event_type' => 'login.daily', 'updated_at' => now()]);

        DB::table('mission_templates')
            ->where('key', 'supporter-monthly')
            ->update(['event_type' => 'supporter.monthly', 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Not reversed: normalization should stay stable.
    }
};

