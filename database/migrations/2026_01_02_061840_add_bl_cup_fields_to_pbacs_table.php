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
        Schema::table('pbacs', function (Blueprint $table) {
            $table->string(column: 'bl_cup_low_count')->nullable()->after('bl_tampon_large');
            $table->string(column: 'bl_cup_half_empty_count')->nullable()->after('bl_cup_low_count');
            $table->string(column: 'bl_cup_full_count')->nullable()->after('bl_cup_half_empty_count');
            $table->string(column: 'bl_period_underwear_count')->nullable()->after('bl_cup_full_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pbacs', function (Blueprint $table) {
            $table->dropColumn(columns: [
                'bl_cup_low_count',
                'bl_cup_half_empty_count',
                'bl_cup_full_count',
                'bl_period_underwear_count',
            ]);
        });
    }
};
