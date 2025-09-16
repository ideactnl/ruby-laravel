<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('participant_id')->index();
            $table->enum('type', ['csv', 'pdf']);
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('file_path')->nullable();
            $table->string('download_url')->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error')->nullable();
            $table->string('queue_job_id')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_jobs');
    }
};
