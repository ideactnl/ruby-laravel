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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 255)->nullable();
            $table->string('pin', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->tinyInteger('enable_data_sharing')->default(0);
            $table->tinyInteger('opt_in_for_research')->default(0);
            $table->tinyInteger('allow_medical_specialist_login')->default(0);
            $table->string('medical_specialist_temporary_pin')->nullable();
            $table->timestamp('medical_specialist_temporary_pin_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
