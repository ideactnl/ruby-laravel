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
        Schema::create('video_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable()->comment('Optional subtitle/description for the video');
            $table->enum('location', ['education', 'self-management'])->index();
            $table->integer('order')->default(0);
            $table->text('condition')->nullable()->comment('Condition to show video on daily view. NULL means NA (not shown on daily view)');
            $table->string('video_url');
            $table->string('video_type')->default('youtube');
            $table->string('video_id')->nullable()->comment('YouTube video ID extracted from URL');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['location', 'order']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_contents');
    }
};
