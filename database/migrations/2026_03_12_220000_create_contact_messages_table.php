<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 255)->index();
            $table->string('subject', 180);
            $table->string('category', 60)->nullable()->index();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'contact_messages_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};

