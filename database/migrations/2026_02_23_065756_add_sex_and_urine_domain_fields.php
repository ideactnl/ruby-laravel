<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pbacs', function (Blueprint $table) {
            // Urnie & Stool
            $table->boolean('is_pain_during_peeing_urine')->nullable()->after('is_urine_stool_no_stool');
            $table->boolean('is_pain_during_pooping_urine')->nullable()->after('is_pain_during_peeing_urine');

            // Sex
            $table->boolean('is_sex_skipped_due_to_period')->nullable()->after('is_sex_emotionally_physically_satisfied');
            $table->boolean('is_sex_pain_during_sex')->nullable()->after('is_sex_skipped_due_to_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pbacs', function (Blueprint $table) {
            $table->dropColumn([
                'is_pain_during_peeing_urine',
                'is_pain_during_pooping_urine',
                'is_sex_skipped_due_to_period',
                'is_sex_pain_during_sex',
            ]);
        });
    }
};
