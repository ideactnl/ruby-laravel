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
        Schema::create('participant_additional_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participant_id');
            $table->string('study_number')->unique();
            $table->date('dob');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_additional_details');
    }
};
