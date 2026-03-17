<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_outbound_emails', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('sender_admin_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_email', 190)->index();
            $table->string('recipient_name', 150)->nullable();
            $table->string('subject', 190);
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();
            $table->string('category', 40)->index();
            $table->string('status', 20)->index();
            $table->string('mailer', 40)->nullable();
            $table->string('provider', 40)->nullable();
            $table->string('provider_message_id', 190)->nullable();
            $table->timestamp('queued_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable()->index();
            $table->text('failure_reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['sender_admin_user_id', 'created_at']);
            $table->index(['recipient_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_outbound_emails');
    }
};