<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::getConnection()->statement("
                UPDATE user_events
                SET event_value = JSON_QUOTE(event_value)
                WHERE event_value IS NOT NULL
                  AND JSON_VALID(event_value) = 0
            ");
            Schema::getConnection()->statement('ALTER TABLE user_events MODIFY event_value JSON NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::getConnection()->statement('ALTER TABLE user_events MODIFY event_value TEXT NULL');
        }
    }
};
