<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participant_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->index();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->integer('duration_seconds')->default(0);
            $table->json('section_breakdown')->nullable();
            $table->json('interactions_breakdown')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_sessions');
    }
};
