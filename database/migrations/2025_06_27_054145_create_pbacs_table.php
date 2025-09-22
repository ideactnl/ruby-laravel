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
        Schema::create('pbacs', function (Blueprint $table) {
            $table->id();
            $table->integer('participant_id')->nullable()->index();
            $table->date('reported_date')->index();

            // Core flags and metadata
            $table->boolean('is_live')->nullable();
            $table->boolean('is_blood_loss_answered')->nullable();
            $table->integer('menstrual_blood_loss')->nullable();
            $table->boolean('spotting')->nullable();
            $table->boolean('no_blood_loss')->nullable();
            $table->boolean('no_pain')->nullable();
            $table->boolean('is_bl_first_day_period')->nullable();

            // Blood loss methods and details
            $table->boolean('is_bl_pads')->nullable();
            $table->boolean('is_bl_tampon')->nullable();
            $table->boolean('is_bl_menstrual_cup')->nullable();
            $table->boolean('is_bl_period_underwear')->nullable();
            $table->boolean('is_bl_other')->nullable();
            $table->string('is_bl_other_text', 255)->nullable();
            $table->integer('bl_pad_small')->nullable();
            $table->integer('bl_pad_medium')->nullable();
            $table->integer('bl_pad_large')->nullable();
            $table->integer('bl_tampon_small')->nullable();
            $table->integer('bl_tampon_medium')->nullable();
            $table->integer('bl_tampon_large')->nullable();
            $table->boolean('is_bl_very_light')->nullable();
            $table->boolean('is_bl_light')->nullable();
            $table->boolean('is_bl_moderate')->nullable();
            $table->boolean('is_bl_heavy')->nullable();
            $table->boolean('is_bl_very_heavy')->nullable();
            $table->boolean('is_bl_blood_clots')->nullable();
            $table->boolean('is_bl_double_protection')->nullable();
            $table->boolean('is_bl_leaked_clothes')->nullable();
            $table->boolean('is_bl_change_products')->nullable();
            $table->boolean('is_bl_wake_up_night')->nullable();

            // Pain section
            $table->boolean('is_pain_answered')->nullable();
            $table->integer('pain_slider_value')->nullable();
            $table->boolean('is_pain_headache_migraine')->nullable();
            $table->boolean('is_pain_during_peeing')->nullable();
            $table->boolean('is_pain_during_pooping')->nullable();
            $table->boolean('is_pain_during_sex')->nullable();
            // Pain locations - front
            $table->boolean('is_pain_image1_umbilical')->nullable();
            $table->boolean('is_pain_image1_left_umbilical')->nullable();
            $table->boolean('is_pain_image1_right_umbilical')->nullable();
            $table->boolean('is_pain_image1_bladder')->nullable();
            $table->boolean('is_pain_image1_left_groin')->nullable();
            $table->boolean('is_pain_image1_right_groin')->nullable();
            $table->boolean('is_pain_image1_left_leg')->nullable();
            $table->boolean('is_pain_image1_right_leg')->nullable();
            // Pain locations - back
            $table->boolean('is_pain_image2_upper_back')->nullable();
            $table->boolean('is_pain_image2_back')->nullable();
            $table->boolean('is_pain_image2_left_buttock')->nullable();
            $table->boolean('is_pain_image2_right_buttock')->nullable();
            $table->boolean('is_pain_image2_left_back_leg')->nullable();
            $table->boolean('is_pain_image2_right_back_leg')->nullable();

            // Impact section
            $table->boolean('is_impact_answered')->nullable();
            $table->integer('impact_slider_grade_your_day')->nullable();
            $table->integer('impact_slider_complaints')->nullable();
            $table->boolean('is_impact_used_medication')->nullable();
            $table->boolean('is_impact_missed_work')->nullable();
            $table->boolean('is_impact_missed_school')->nullable();
            $table->boolean('is_impact_could_no_sport')->nullable();
            $table->boolean('is_impact_missed_special_activities')->nullable();
            $table->boolean('is_impact_missed_leisure_activities')->nullable();
            $table->boolean('is_impact_had_to_sit_more')->nullable();
            $table->boolean('is_impact_could_not_move')->nullable();
            $table->boolean('is_impact_had_to_stay_longer_in_bed')->nullable();
            $table->boolean('is_impact_could_not_do_unpaid_work')->nullable();
            $table->boolean('is_impact_other')->nullable();
            $table->string('is_impact_other_text', 255)->nullable();
            $table->boolean('is_impact_med_paracetamol')->nullable();
            $table->boolean('is_impact_med_diclofenac')->nullable();
            $table->boolean('is_impact_med_naproxen')->nullable();
            $table->boolean('is_impact_med_iron_pills')->nullable();
            $table->boolean('is_impact_med_tramodol')->nullable();
            $table->boolean('is_impact_med_oxynorm')->nullable();
            $table->boolean('is_impact_med_anticonception_pill')->nullable();
            $table->boolean('is_impact_med_other_hormones')->nullable();
            $table->boolean('is_impact_med_tranexamine_zuur')->nullable();
            $table->boolean('is_impact_med_other')->nullable();
            $table->string('is_impact_med_other_text', 255)->nullable();
            $table->boolean('is_impact_medicine_effective')->nullable();

            // General health
            $table->boolean('is_general_health_answered')->nullable();
            $table->integer('general_health_energy_level_slider_value')->nullable();
            $table->boolean('is_general_health_dizzy')->nullable();
            $table->boolean('is_general_health_nauseous')->nullable();
            $table->boolean('is_general_health_headache_migraine')->nullable();
            $table->boolean('is_general_health_bloated')->nullable();
            $table->boolean('is_general_health_painful_sensitive_breasts')->nullable();
            $table->boolean('is_general_health_acne')->nullable();
            $table->boolean('is_general_health_muscle_joint_pain')->nullable();

            // Mood
            $table->boolean('is_mood_answered')->nullable();
            $table->boolean('is_mood_calm')->nullable();
            $table->boolean('is_mood_happy')->nullable();
            $table->boolean('is_mood_excited')->nullable();
            $table->boolean('is_mood_anxious_stressed')->nullable();
            $table->boolean('is_mood_ashamed')->nullable();
            $table->boolean('is_mood_angry_irritable')->nullable();
            $table->boolean('is_mood_sensitive')->nullable();
            $table->boolean('is_mood_swings')->nullable();
            $table->boolean('is_mood_worthless_guilty')->nullable();
            $table->boolean('is_mood_overwhelmed')->nullable();
            $table->boolean('is_mood_hopes')->nullable();
            $table->boolean('is_mood_depressed_sad_down')->nullable();

            // Urine / Stool
            $table->boolean('is_urine_stool_answered')->nullable();
            $table->boolean('is_urine_stool_blood_in_urine')->nullable();
            $table->boolean('is_urine_stool_blood_in_stool')->nullable();
            $table->boolean('is_urine_stool_hard')->nullable();
            $table->boolean('is_urine_stool_normal')->nullable();
            $table->boolean('is_urine_stool_soft')->nullable();
            $table->boolean('is_urine_stool_diarrhea')->nullable();
            $table->boolean('is_urine_stool_something_else')->nullable();
            $table->string('is_urine_stool_something_else_text', 255)->nullable();
            $table->boolean('is_urine_stool_no_stool')->nullable();

            // Sleep
            $table->boolean('is_sleep_answered')->nullable();
            $table->string('sleep_fell_asleep_time', 8)->nullable();
            $table->string('sleep_woke_up_time', 8)->nullable();
            $table->string('sleep_hours_of_sleep', 8)->nullable();
            $table->boolean('is_sleep_work_school_day')->nullable();
            $table->boolean('is_sleep_free_day')->nullable();
            $table->boolean('is_sleep_trouble_asleep')->nullable();
            $table->boolean('is_sleep_tired_rested')->nullable();
            $table->boolean('is_sleep_wake_up_during_night')->nullable();

            // Exercise
            $table->boolean('is_exercise_answered')->nullable();
            $table->boolean('is_exercise_less_thirty')->nullable();
            $table->boolean('is_exercise_thirty_to_sixty')->nullable();
            $table->boolean('is_exercise_greater_sixty')->nullable();
            $table->boolean('is_exercise_high_impact')->nullable();
            $table->boolean('is_exercise_low_impact')->nullable();

            // Diet
            $table->boolean('is_diet_answered')->nullable();
            $table->boolean('is_diet_vegetables')->nullable();
            $table->boolean('is_diet_fruit')->nullable();
            $table->boolean('is_diet_potato_rice_bread')->nullable();
            $table->boolean('is_diet_dairy')->nullable();
            $table->boolean('is_diet_nuts_tofu_tempe')->nullable();
            $table->boolean('is_diet_eggs')->nullable();
            $table->boolean('is_diet_fish')->nullable();
            $table->boolean('is_diet_meat')->nullable();
            $table->boolean('is_diet_soda')->nullable();
            $table->boolean('is_diet_water')->nullable();
            $table->boolean('is_diet_coffee')->nullable();
            $table->boolean('is_diet_alcohol')->nullable();

            // Sex
            $table->boolean('is_sex_answered')->nullable();
            $table->boolean('is_sex_today')->nullable();
            $table->boolean('is_sex_avoided')->nullable();
            $table->boolean('is_sex_bloodloss_during_after')->nullable();
            $table->boolean('is_sex_discomfort_pelvic_area')->nullable();
            $table->boolean('is_sex_emotionally_physically_satisfied')->nullable();

            // Notes
            $table->boolean('is_additional_notes_answered')->nullable();
            $table->text('additional_notes')->nullable();

            $table->unique(['participant_id', 'reported_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pbacs');
    }
};
