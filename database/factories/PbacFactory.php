<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Participant;
use App\Models\Pbac;

class PbacFactory extends Factory
{
    protected $model = Pbac::class;

    private array $patterns = [
        'severe' => [
            'menstrual_blood_loss' => [8, 9, 10],
            'pain_slider_value' => [7, 8, 9],
            'impact_slider_grade_your_day' => [1, 2, 3],
            'general_health_energy_level_slider_value' => [1, 2, 3],
            'is_impact_medicine_effective' => [3, 4, 5],
            'blood_loss_flags' => ['is_bl_very_heavy', 'is_bl_blood_clots', 'is_bl_leaked_clothes'],
            'pain_flags' => ['is_pain_headache_migraine', 'is_pain_during_sex'],
            'impact_flags' => ['is_impact_missed_work', 'is_impact_had_to_stay_longer_in_bed'],
            'medications' => ['is_impact_med_paracetamol', 'is_impact_med_diclofenac'],
        ],
        'moderate' => [
            'menstrual_blood_loss' => [4, 5, 6],
            'pain_slider_value' => [4, 5, 6],
            'impact_slider_grade_your_day' => [4, 5, 6],
            'general_health_energy_level_slider_value' => [4, 5, 6],
            'is_impact_medicine_effective' => [5, 6, 7],
            'blood_loss_flags' => ['is_bl_moderate', 'is_bl_change_products'],
            'pain_flags' => ['is_pain_during_pooping'],
            'impact_flags' => ['is_impact_missed_leisure_activities'],
            'medications' => ['is_impact_med_naproxen'],
        ],
        'light' => [
            'menstrual_blood_loss' => [1, 2, 3],
            'pain_slider_value' => [1, 2, 3],
            'impact_slider_grade_your_day' => [7, 8, 9],
            'general_health_energy_level_slider_value' => [7, 8, 9],
            'is_impact_medicine_effective' => [7, 8, 9],
            'blood_loss_flags' => ['is_bl_light', 'is_bl_very_light'],
            'pain_flags' => [],
            'impact_flags' => [],
            'medications' => [],
        ],
        'none' => [
            'menstrual_blood_loss' => [0],
            'pain_slider_value' => [0],
            'impact_slider_grade_your_day' => [8, 9, 10],
            'general_health_energy_level_slider_value' => [8, 9, 10],
            'is_impact_medicine_effective' => [0],
            'blood_loss_flags' => ['no_blood_loss'],
            'pain_flags' => ['no_pain'],
            'impact_flags' => [],
            'medications' => [],
        ],
    ];

    public function definition()
    {
        $pattern = $this->faker->randomElement(array_keys($this->patterns));
        $patternData = $this->patterns[$pattern];
        
        $data = [
            'participant_id' => function () {
                return Participant::factory()->create()->id;
            },
            'reported_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'is_live' => true,
            'is_blood_loss_answered' => true,
            'menstrual_blood_loss' => $this->faker->randomElement($patternData['menstrual_blood_loss']),
            'is_pain_answered' => true,
            'pain_slider_value' => $this->faker->randomElement($patternData['pain_slider_value']),
            'is_impact_answered' => true,
            'impact_slider_grade_your_day' => $this->faker->randomElement($patternData['impact_slider_grade_your_day']),
            'impact_slider_complaints' => $this->faker->numberBetween(0, 10),
            'is_impact_medicine_effective' => $this->faker->randomElement($patternData['is_impact_medicine_effective']),
        ];

        foreach ($patternData['blood_loss_flags'] as $flag) {
            $data[$flag] = true;
        }

        foreach ($patternData['pain_flags'] as $flag) {
            $data[$flag] = true;
        }

        foreach ($patternData['impact_flags'] as $flag) {
            $data[$flag] = true;
        }

        if (!empty($patternData['medications'])) {
            $data['is_impact_used_medication'] = true;
            foreach ($patternData['medications'] as $medication) {
                $data[$medication] = true;
            }
        }

        if (!isset($data['no_blood_loss'])) {
            if ($this->faker->boolean(80)) {
                $data['is_bl_pads'] = true;
                $data['bl_pad_small'] = $this->faker->numberBetween(0, 3);
                $data['bl_pad_medium'] = $this->faker->numberBetween(0, 4);
                $data['bl_pad_large'] = $this->faker->numberBetween(0, 2);
            }
            
            if ($this->faker->boolean(60)) {
                $data['is_bl_tampon'] = true;
                $data['bl_tampon_small'] = $this->faker->numberBetween(0, 2);
                $data['bl_tampon_medium'] = $this->faker->numberBetween(0, 3);
                $data['bl_tampon_large'] = $this->faker->numberBetween(0, 2);
            }
        }

        if (!isset($data['no_pain'])) {
            $painLocations = [
                'is_pain_image1_umbilical', 'is_pain_image1_bladder', 'is_pain_image1_left_groin',
                'is_pain_image2_back', 'is_pain_image2_left_buttock'
            ];
            
            $numLocations = $this->faker->numberBetween(0, 3);
            if ($numLocations > 0) {
                $selectedLocations = $this->faker->randomElements($painLocations, $numLocations);
                foreach ($selectedLocations as $location) {
                    $data[$location] = true;
                }
            }
        }

        if ($this->faker->boolean(70)) {
            $data['is_exercise_answered'] = true;
            $exerciseTypes = ['is_exercise_less_thirty', 'is_exercise_thirty_to_sixty', 'is_exercise_greater_sixty'];
            $data[$this->faker->randomElement($exerciseTypes)] = true;
            
            $impactTypes = ['is_exercise_low_impact', 'is_exercise_high_impact'];
            $data[$this->faker->randomElement($impactTypes)] = true;
        }

        if ($this->faker->boolean(80)) {
            $data['is_diet_answered'] = true;
            $healthyFoods = ['is_diet_vegetables', 'is_diet_fruit', 'is_diet_water', 'is_diet_fish'];
            $selectedHealthy = $this->faker->randomElements($healthyFoods, $this->faker->numberBetween(1, 3));
            foreach ($selectedHealthy as $food) {
                $data[$food] = true;
            }
            
            if ($this->faker->boolean(40)) {
                $unhealthyFoods = ['is_diet_soda', 'is_diet_alcohol'];
                $data[$this->faker->randomElement($unhealthyFoods)] = true;
            }
        }

        if ($this->faker->boolean(30)) {
            $data['is_sex_answered'] = true;
            $data['is_sex_today'] = $this->faker->boolean(40);
            if ($pattern === 'severe') {
                $data['is_sex_avoided'] = true;
                $data['is_sex_discomfort_pelvic_area'] = true;
            } else {
                $data['is_sex_emotionally_physically_satisfied'] = $pattern !== 'moderate';
            }
        }

        if ($this->faker->boolean(40)) {
            $data['is_urine_stool_answered'] = true;
            if ($pattern === 'severe') {
                $data['is_urine_stool_hard'] = true;
            } else {
                $data['is_urine_stool_normal'] = true;
            }
        }

        if ($this->faker->boolean(20)) {
            $data['is_additional_notes_answered'] = true;
            $notes = [
                'severe' => 'Difficult day with significant symptoms.',
                'moderate' => 'Manageable symptoms today.',
                'light' => 'Feeling much better today.',
                'none' => 'Excellent day with no symptoms.',
            ];
            $data['additional_notes'] = $notes[$pattern];
        }

        return $data;
    }

    public function severe()
    {
        return $this->state(function (array $attributes) {
            $patternData = $this->patterns['severe'];
            return [
                'menstrual_blood_loss' => $this->faker->randomElement($patternData['menstrual_blood_loss']),
                'pain_slider_value' => $this->faker->randomElement($patternData['pain_slider_value']),
                'impact_slider_grade_your_day' => $this->faker->randomElement($patternData['impact_slider_grade_your_day']),
                'is_bl_very_heavy' => true,
                'is_bl_blood_clots' => true,
                'is_pain_headache_migraine' => true,
                'is_impact_missed_work' => true,
            ];
        });
    }

    public function moderate()
    {
        return $this->state(function (array $attributes) {
            $patternData = $this->patterns['moderate'];
            return [
                'menstrual_blood_loss' => $this->faker->randomElement($patternData['menstrual_blood_loss']),
                'pain_slider_value' => $this->faker->randomElement($patternData['pain_slider_value']),
                'impact_slider_grade_your_day' => $this->faker->randomElement($patternData['impact_slider_grade_your_day']),
                'is_bl_moderate' => true,
                'is_impact_missed_leisure_activities' => true,
            ];
        });
    }

    public function light()
    {
        return $this->state(function (array $attributes) {
            $patternData = $this->patterns['light'];
            return [
                'menstrual_blood_loss' => $this->faker->randomElement($patternData['menstrual_blood_loss']),
                'pain_slider_value' => $this->faker->randomElement($patternData['pain_slider_value']),
                'impact_slider_grade_your_day' => $this->faker->randomElement($patternData['impact_slider_grade_your_day']),
                'is_bl_light' => true,
            ];
        });
    }

    public function none()
    {
        return $this->state(function (array $attributes) {
            return [
                'menstrual_blood_loss' => 0,
                'pain_slider_value' => 0,
                'impact_slider_grade_your_day' => $this->faker->numberBetween(8, 10),
                'no_blood_loss' => true,
                'no_pain' => true,
            ];
        });
    }
}
