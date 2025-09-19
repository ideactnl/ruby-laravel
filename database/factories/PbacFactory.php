<?php

namespace Database\Factories;

use App\Models\Pbac;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbacFactory extends Factory
{
    protected $model = Pbac::class;

    public function definition()
    {
        return [
            'participant_id' => function () {
                return \App\Models\Participant::factory()->create()->id;
            },
            'reported_date' => $this->faker->date(),
            'is_live' => $this->faker->boolean(),
            'is_blood_loss_answered' => $this->faker->boolean(),
            'menstrual_blood_loss' => $this->faker->numberBetween(0, 10),
            'spotting' => $this->faker->boolean(),
            'no_blood_loss' => $this->faker->boolean(),
            'no_pain' => $this->faker->boolean(),
            'is_bl_first_day_period' => $this->faker->boolean(),
            'is_bl_pads' => $this->faker->boolean(),
            'is_bl_tampon' => $this->faker->boolean(),
            'is_bl_menstrual_cup' => $this->faker->boolean(),
            'is_bl_period_underwear' => $this->faker->boolean(),
            'is_bl_other' => $this->faker->boolean(),
            'is_bl_other_text' => $this->faker->text(),
            'bl_pad_small' => $this->faker->numberBetween(0, 10),
            'bl_pad_medium' => $this->faker->numberBetween(0, 10),
            'bl_pad_large' => $this->faker->numberBetween(0, 10),
            'bl_tampon_small' => $this->faker->numberBetween(0, 10),
            'bl_tampon_medium' => $this->faker->numberBetween(0, 10),
            'bl_tampon_large' => $this->faker->numberBetween(0, 10),
            'is_bl_very_light' => $this->faker->boolean(),
            'is_bl_light' => $this->faker->boolean(),
            'is_bl_moderate' => $this->faker->boolean(),
            'is_bl_heavy' => $this->faker->boolean(),
            'is_bl_very_heavy' => $this->faker->boolean(),
            'is_bl_blood_clots' => $this->faker->boolean(),
            'is_bl_double_protection' => $this->faker->boolean(),
            'is_bl_leaked_clothes' => $this->faker->boolean(),
            'is_bl_change_products' => $this->faker->boolean(),
            'is_bl_wake_up_night' => $this->faker->boolean(),
            'is_pain_answered' => $this->faker->boolean(),
            'pain_slider_value' => $this->faker->numberBetween(0, 10),
            'is_pain_headache_migraine' => $this->faker->boolean(),
            'is_pain_during_peeing' => $this->faker->boolean(),
            'is_pain_during_pooping' => $this->faker->boolean(),
            'is_pain_during_sex' => $this->faker->boolean(),
            'is_pain_image1_umbilical' => $this->faker->boolean(),
            'is_pain_image1_left_umbilical' => $this->faker->boolean(),
            'is_pain_image1_right_umbilical' => $this->faker->boolean(),
            'is_pain_image1_bladder' => $this->faker->boolean(),
            'is_pain_image1_left_groin' => $this->faker->boolean(),
            'is_pain_image1_right_groin' => $this->faker->boolean(),
            'is_pain_image1_left_leg' => $this->faker->boolean(),
            'is_pain_image1_right_leg' => $this->faker->boolean(),
            'is_pain_image2_upper_back' => $this->faker->boolean(),
            'is_pain_image2_back' => $this->faker->boolean(),
            'is_pain_image2_left_buttock' => $this->faker->boolean(),
            'is_pain_image2_right_buttock' => $this->faker->boolean(),
            'is_pain_image2_left_back_leg' => $this->faker->boolean(),
            'is_pain_image2_right_back_leg' => $this->faker->boolean(),
            'is_impact_answered' => $this->faker->boolean(),
            'impact_slider_grade_your_day' => $this->faker->numberBetween(0, 10),
            'impact_slider_complaints' => $this->faker->numberBetween(0, 10),
            'is_impact_used_medication' => $this->faker->boolean(),
            'is_impact_missed_work' => $this->faker->boolean(),
            'is_impact_missed_school' => $this->faker->boolean(),
            'is_impact_could_no_sport' => $this->faker->boolean(),
            'is_impact_missed_special_activities' => $this->faker->boolean(),
            'is_impact_missed_leisure_activities' => $this->faker->boolean(),
            'is_impact_had_to_sit_more' => $this->faker->boolean(),
            'is_impact_could_not_move' => $this->faker->boolean(),
            'is_impact_had_to_stay_longer_in_bed' => $this->faker->boolean(),
            'is_impact_could_not_do_unpaid_work' => $this->faker->boolean(),
            'is_impact_other' => $this->faker->boolean(),
            'is_impact_other_text' => $this->faker->text(),
            'is_impact_med_paracetamol' => $this->faker->boolean(),
            'is_impact_med_diclofenac' => $this->faker->boolean(),
            'is_impact_med_naproxen' => $this->faker->boolean(),
            'is_impact_med_iron_pills' => $this->faker->boolean(),
            'is_impact_med_tramodol' => $this->faker->boolean(),
            'is_impact_med_oxynorm' => $this->faker->boolean(),
            'is_impact_med_anticonception_pill' => $this->faker->boolean(),
            'is_impact_med_other_hormones' => $this->faker->boolean(),
            'is_impact_med_other' => $this->faker->boolean(),
            'is_impact_med_other_text' => $this->faker->text(),
            'is_impact_medicine_effective' => $this->faker->boolean(),
        ];
    }
}
