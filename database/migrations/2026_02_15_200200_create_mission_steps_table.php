<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mission_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
            $table->string('step_key', 100);
            $table->text('step_value')->nullable();
            $table->string('label');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['mission_id', 'order']);
            $table->index(['mission_id', 'step_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mission_steps');
    }
};
