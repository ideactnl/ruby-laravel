<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $participant_id
 * @property \Carbon\CarbonInterface $reported_date
 * @property \Carbon\CarbonInterface|null $created_at
 * @property \Carbon\CarbonInterface|null $updated_at
 * @property bool|null $is_live
 * @property bool|null $is_blood_loss_answered
 * @property bool|null $spotting
 * @property bool|null $no_blood_loss
 * @property int|null $menstrual_blood_loss
 * @property bool|null $is_bl_first_day_period
 * @property bool|null $is_bl_pads
 * @property bool|null $is_bl_tampon
 * @property bool|null $is_bl_menstrual_cup
 * @property bool|null $is_bl_period_underwear
 * @property bool|null $is_bl_other
 * @property string|null $is_bl_other_text
 * @property int|null $bl_pad_small
 * @property int|null $bl_pad_medium
 * @property int|null $bl_pad_large
 * @property int|null $bl_tampon_small
 * @property int|null $bl_tampon_medium
 * @property int|null $bl_tampon_large
 * @property bool|null $is_bl_very_light
 * @property bool|null $is_bl_light
 * @property bool|null $is_bl_moderate
 * @property bool|null $is_bl_heavy
 * @property bool|null $is_bl_very_heavy
 * @property bool|null $is_bl_blood_clots
 * @property bool|null $is_bl_double_protection
 * @property bool|null $is_bl_leaked_clothes
 * @property bool|null $is_bl_change_products
 * @property bool|null $is_bl_wake_up_night
 * @property bool|null $no_pain
 * @property bool|null $is_pain_answered
 * @property int|null $pain_slider_value
 * @property bool|null $is_pain_headache_migraine
 * @property bool|null $is_pain_during_peeing
 * @property bool|null $is_pain_during_pooping
 * @property bool|null $is_pain_during_sex
 * @property bool|null $is_pain_image1_umbilical
 * @property bool|null $is_pain_image1_left_umbilical
 * @property bool|null $is_pain_image1_right_umbilical
 * @property bool|null $is_pain_image1_bladder
 * @property bool|null $is_pain_image1_left_groin
 * @property bool|null $is_pain_image1_right_groin
 * @property bool|null $is_pain_image1_left_leg
 * @property bool|null $is_pain_image1_right_leg
 * @property bool|null $is_pain_image2_upper_back
 * @property bool|null $is_pain_image2_back
 * @property bool|null $is_pain_image2_left_buttock
 * @property bool|null $is_pain_image2_right_buttock
 * @property bool|null $is_pain_image2_left_back_leg
 * @property bool|null $is_pain_image2_right_back_leg
 * @property bool|null $is_impact_answered
 * @property int|null $impact_slider_grade_your_day
 * @property int|null $impact_slider_complaints
 * @property bool|null $is_impact_used_medication
 * @property bool|null $is_impact_missed_work
 * @property bool|null $is_impact_missed_school
 * @property bool|null $is_impact_could_no_sport
 * @property bool|null $is_impact_missed_special_activities
 * @property bool|null $is_impact_missed_leisure_activities
 * @property bool|null $is_impact_had_to_sit_more
 * @property bool|null $is_impact_could_not_move
 * @property bool|null $is_impact_had_to_stay_longer_in_bed
 * @property bool|null $is_impact_could_not_do_unpaid_work
 * @property bool|null $is_impact_other
 * @property string|null $is_impact_other_text
 * @property bool|null $is_impact_med_paracetamol
 * @property bool|null $is_impact_med_diclofenac
 * @property bool|null $is_impact_med_naproxen
 * @property bool|null $is_impact_med_iron_pills
 * @property bool|null $is_impact_med_tramodol
 * @property bool|null $is_impact_med_oxynorm
 * @property bool|null $is_impact_med_anticonception_pill
 * @property bool|null $is_impact_med_other_hormones
 * @property bool|null $is_impact_med_tranexamine_zuur
 * @property bool|null $is_impact_med_other
 * @property string|null $is_impact_med_other_text
 * @property bool|null $is_impact_medicine_effective
 * @property bool|null $is_general_health_answered
 * @property int|null $general_health_energy_level_slider_value
 * @property bool|null $is_general_health_dizzy
 * @property bool|null $is_general_health_nauseous
 * @property bool|null $is_general_health_headache_migraine
 * @property bool|null $is_general_health_bloated
 * @property bool|null $is_general_health_painful_sensitive_breasts
 * @property bool|null $is_general_health_acne
 * @property bool|null $is_general_health_muscle_joint_pain
 * @property bool|null $is_mood_answered
 * @property bool|null $is_mood_calm
 * @property bool|null $is_mood_happy
 * @property bool|null $is_mood_excited
 * @property bool|null $is_mood_anxious_stressed
 * @property bool|null $is_mood_ashamed
 * @property bool|null $is_mood_angry_irritable
 * @property bool|null $is_mood_sensitive
 * @property bool|null $is_mood_swings
 * @property bool|null $is_mood_worthless_guilty
 * @property bool|null $is_mood_overwhelmed
 * @property bool|null $is_mood_hopes
 * @property bool|null $is_mood_depressed_sad_down
 * @property bool|null $is_urine_stool_answered
 * @property bool|null $is_urine_stool_blood_in_urine
 * @property bool|null $is_urine_stool_blood_in_stool
 * @property bool|null $is_urine_stool_hard
 * @property bool|null $is_urine_stool_normal
 * @property bool|null $is_urine_stool_soft
 * @property bool|null $is_urine_stool_diarrhea
 * @property bool|null $is_urine_stool_something_else
 * @property string|null $is_urine_stool_something_else_text
 * @property bool|null $is_urine_stool_no_stool
 * @property bool|null $is_sleep_answered
 * @property string|null $sleep_fell_asleep_time
 * @property string|null $sleep_woke_up_time
 * @property string|null $sleep_hours_of_sleep
 * @property bool|null $is_sleep_work_school_day
 * @property bool|null $is_sleep_free_day
 * @property bool|null $is_sleep_trouble_asleep
 * @property bool|null $is_sleep_tired_rested
 * @property bool|null $is_sleep_wake_up_during_night
 * @property bool|null $is_exercise_answered
 * @property bool|null $is_exercise_less_thirty
 * @property bool|null $is_exercise_thirty_to_sixty
 * @property bool|null $is_exercise_greater_sixty
 * @property bool|null $is_exercise_high_impact
 * @property bool|null $is_exercise_low_impact
 * @property bool|null $is_diet_answered
 * @property bool|null $is_diet_vegetables
 * @property bool|null $is_diet_fruit
 * @property bool|null $is_diet_potato_rice_bread
 * @property bool|null $is_diet_dairy
 * @property bool|null $is_diet_nuts_tofu_tempe
 * @property bool|null $is_diet_eggs
 * @property bool|null $is_diet_fish
 * @property bool|null $is_diet_meat
 * @property bool|null $is_diet_soda
 * @property bool|null $is_diet_water
 * @property bool|null $is_diet_coffee
 * @property bool|null $is_diet_alcohol
 * @property bool|null $is_sex_answered
 * @property bool|null $is_sex_today
 * @property bool|null $is_sex_avoided
 * @property bool|null $is_sex_bloodloss_during_after
 * @property bool|null $is_sex_discomfort_pelvic_area
 * @property bool|null $is_sex_emotionally_physically_satisfied
 * @property bool|null $is_additional_notes_answered
 * @property string|null $additional_notes
 * @property-read array|null $blood_loss
 * @property-read array|null $pain
 * @property-read array|null $impact
 * @property-read array|null $general_health
 * @property-read array|null $mood
 * @property-read array|null $stool_urine
 * @property-read array|null $diet
 * @property-read array|null $exercise
 * @property-read array|null $sex
 * @property-read array $notes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Pbac forParticipant(int $participantId)
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Pbac extends Model
{
    use HasFactory;

    /****** FIELDS GROUPS *******/
    private const CORE_FIELDS = [
        'reported_date',
        'participant_id',
        'is_live',
    ];

    private const BLOOD_LOSS_FIELDS = [
        'is_blood_loss_answered', 'menstrual_blood_loss', 'spotting', 'no_blood_loss', 'is_bl_first_day_period',
        'is_bl_pads', 'is_bl_tampon', 'is_bl_menstrual_cup', 'is_bl_period_underwear', 'is_bl_other', 'is_bl_other_text',
        'bl_pad_small', 'bl_pad_medium', 'bl_pad_large', 'bl_tampon_small', 'bl_tampon_medium', 'bl_tampon_large',
        'is_bl_very_light', 'is_bl_light', 'is_bl_moderate', 'is_bl_heavy', 'is_bl_very_heavy', 'is_bl_blood_clots',
        'is_bl_double_protection', 'is_bl_leaked_clothes', 'is_bl_change_products', 'is_bl_wake_up_night',
    ];

    private const PAIN_FIELDS = [
        'no_pain', 'is_pain_answered', 'pain_slider_value',
        'is_pain_headache_migraine', 'is_pain_during_peeing', 'is_pain_during_pooping', 'is_pain_during_sex',
        'is_pain_image1_umbilical', 'is_pain_image1_left_umbilical', 'is_pain_image1_right_umbilical', 'is_pain_image1_bladder',
        'is_pain_image1_left_groin', 'is_pain_image1_right_groin', 'is_pain_image1_left_leg', 'is_pain_image1_right_leg',
        'is_pain_image2_upper_back', 'is_pain_image2_back', 'is_pain_image2_left_buttock', 'is_pain_image2_right_buttock',
        'is_pain_image2_left_back_leg', 'is_pain_image2_right_back_leg',
    ];

    private const IMPACT_FIELDS = [
        'is_impact_answered', 'impact_slider_grade_your_day', 'impact_slider_complaints', 'is_impact_used_medication',
        'is_impact_missed_work', 'is_impact_missed_school', 'is_impact_could_no_sport', 'is_impact_missed_special_activities',
        'is_impact_missed_leisure_activities', 'is_impact_had_to_sit_more', 'is_impact_could_not_move',
        'is_impact_had_to_stay_longer_in_bed', 'is_impact_could_not_do_unpaid_work', 'is_impact_other', 'is_impact_other_text',
        'is_impact_med_paracetamol', 'is_impact_med_diclofenac', 'is_impact_med_naproxen', 'is_impact_med_iron_pills',
        'is_impact_med_tramodol', 'is_impact_med_oxynorm', 'is_impact_med_anticonception_pill', 'is_impact_med_other_hormones',
        'is_impact_med_tranexamine_zuur', 'is_impact_med_other', 'is_impact_med_other_text', 'is_impact_medicine_effective',
    ];

    private const GENERAL_HEALTH_FIELDS = [
        'is_general_health_answered', 'general_health_energy_level_slider_value', 'is_general_health_dizzy',
        'is_general_health_nauseous', 'is_general_health_headache_migraine', 'is_general_health_bloated',
        'is_general_health_painful_sensitive_breasts', 'is_general_health_acne', 'is_general_health_muscle_joint_pain',
    ];

    private const MOOD_FIELDS = [
        'is_mood_answered', 'is_mood_calm', 'is_mood_happy', 'is_mood_excited', 'is_mood_anxious_stressed',
        'is_mood_ashamed', 'is_mood_angry_irritable', 'is_mood_sensitive', 'is_mood_swings', 'is_mood_worthless_guilty',
        'is_mood_overwhelmed', 'is_mood_hopes', 'is_mood_depressed_sad_down',
    ];

    private const URINE_STOOL_FIELDS = [
        'is_urine_stool_answered', 'is_urine_stool_blood_in_urine', 'is_urine_stool_blood_in_stool', 'is_urine_stool_hard',
        'is_urine_stool_normal', 'is_urine_stool_soft', 'is_urine_stool_diarrhea', 'is_urine_stool_something_else',
        'is_urine_stool_something_else_text', 'is_urine_stool_no_stool',
    ];

    private const SLEEP_FIELDS = [
        'is_sleep_answered', 'sleep_fell_asleep_time', 'sleep_woke_up_time', 'sleep_hours_of_sleep',
        'is_sleep_work_school_day', 'is_sleep_free_day', 'is_sleep_trouble_asleep', 'is_sleep_tired_rested',
        'is_sleep_wake_up_during_night',
    ];

    private const EXERCISE_FIELDS = [
        'is_exercise_answered', 'is_exercise_less_thirty', 'is_exercise_thirty_to_sixty', 'is_exercise_greater_sixty',
        'is_exercise_high_impact', 'is_exercise_low_impact',
    ];

    private const DIET_FIELDS = [
        'is_diet_answered', 'is_diet_vegetables', 'is_diet_fruit', 'is_diet_potato_rice_bread', 'is_diet_dairy',
        'is_diet_nuts_tofu_tempe', 'is_diet_eggs', 'is_diet_fish', 'is_diet_meat', 'is_diet_soda', 'is_diet_water',
        'is_diet_coffee', 'is_diet_alcohol',
    ];

    private const SEX_FIELDS = [
        'is_sex_answered', 'is_sex_today', 'is_sex_avoided', 'is_sex_bloodloss_during_after',
        'is_sex_discomfort_pelvic_area', 'is_sex_emotionally_physically_satisfied',
    ];

    private const ADDITIONAL_NOTES_FIELDS = [
        'is_additional_notes_answered', 'additional_notes',
    ];

    /****** END FIELDS GROUPS *******/

    /****** CASTS GROUPS *******/
    private const CORE_CASTS = [
        'reported_date' => 'date',
        'is_live' => 'boolean',
    ];

    private const INTEGER_CASTS = [
        'menstrual_blood_loss' => 'integer',
        'bl_pad_small' => 'integer',
        'bl_pad_medium' => 'integer',
        'bl_pad_large' => 'integer',
        'bl_tampon_small' => 'integer',
        'bl_tampon_medium' => 'integer',
        'bl_tampon_large' => 'integer',
        'pain_slider_value' => 'integer',
        'impact_slider_grade_your_day' => 'integer',
        'impact_slider_complaints' => 'integer',
        'general_health_energy_level_slider_value' => 'integer',
    ];

    private const BOOLEAN_CASTS = [
        // Blood Loss
        'is_blood_loss_answered' => 'boolean', 'spotting' => 'boolean', 'no_blood_loss' => 'boolean', 'is_bl_first_day_period' => 'boolean',
        'is_bl_pads' => 'boolean', 'is_bl_tampon' => 'boolean', 'is_bl_menstrual_cup' => 'boolean', 'is_bl_period_underwear' => 'boolean', 'is_bl_other' => 'boolean',
        'is_bl_very_light' => 'boolean', 'is_bl_light' => 'boolean', 'is_bl_moderate' => 'boolean', 'is_bl_heavy' => 'boolean', 'is_bl_very_heavy' => 'boolean',
        'is_bl_blood_clots' => 'boolean', 'is_bl_double_protection' => 'boolean', 'is_bl_leaked_clothes' => 'boolean', 'is_bl_change_products' => 'boolean', 'is_bl_wake_up_night' => 'boolean',

        // Pain
        'no_pain' => 'boolean', 'is_pain_answered' => 'boolean', 'is_pain_headache_migraine' => 'boolean', 'is_pain_during_peeing' => 'boolean', 'is_pain_during_pooping' => 'boolean', 'is_pain_during_sex' => 'boolean',
        'is_pain_image1_umbilical' => 'boolean', 'is_pain_image1_left_umbilical' => 'boolean', 'is_pain_image1_right_umbilical' => 'boolean', 'is_pain_image1_bladder' => 'boolean',
        'is_pain_image1_left_groin' => 'boolean', 'is_pain_image1_right_groin' => 'boolean', 'is_pain_image1_left_leg' => 'boolean', 'is_pain_image1_right_leg' => 'boolean',
        'is_pain_image2_upper_back' => 'boolean', 'is_pain_image2_back' => 'boolean', 'is_pain_image2_left_buttock' => 'boolean', 'is_pain_image2_right_buttock' => 'boolean',
        'is_pain_image2_left_back_leg' => 'boolean', 'is_pain_image2_right_back_leg' => 'boolean',

        // Impact
        'is_impact_answered' => 'boolean', 'is_impact_used_medication' => 'boolean', 'is_impact_missed_work' => 'boolean', 'is_impact_missed_school' => 'boolean',
        'is_impact_could_no_sport' => 'boolean', 'is_impact_missed_special_activities' => 'boolean', 'is_impact_missed_leisure_activities' => 'boolean',
        'is_impact_had_to_sit_more' => 'boolean', 'is_impact_could_not_move' => 'boolean', 'is_impact_had_to_stay_longer_in_bed' => 'boolean',
        'is_impact_could_not_do_unpaid_work' => 'boolean', 'is_impact_other' => 'boolean', 'is_impact_medicine_effective' => 'boolean',
        'is_impact_med_paracetamol' => 'boolean', 'is_impact_med_diclofenac' => 'boolean', 'is_impact_med_naproxen' => 'boolean', 'is_impact_med_iron_pills' => 'boolean',
        'is_impact_med_tramodol' => 'boolean', 'is_impact_med_oxynorm' => 'boolean', 'is_impact_med_anticonception_pill' => 'boolean', 'is_impact_med_other_hormones' => 'boolean',
        'is_impact_med_tranexamine_zuur' => 'boolean', 'is_impact_med_other' => 'boolean',

        // General Health
        'is_general_health_answered' => 'boolean', 'is_general_health_dizzy' => 'boolean', 'is_general_health_nauseous' => 'boolean',
        'is_general_health_headache_migraine' => 'boolean', 'is_general_health_bloated' => 'boolean', 'is_general_health_painful_sensitive_breasts' => 'boolean',
        'is_general_health_acne' => 'boolean', 'is_general_health_muscle_joint_pain' => 'boolean',

        // Mood
        'is_mood_answered' => 'boolean', 'is_mood_calm' => 'boolean', 'is_mood_happy' => 'boolean', 'is_mood_excited' => 'boolean',
        'is_mood_anxious_stressed' => 'boolean', 'is_mood_ashamed' => 'boolean', 'is_mood_angry_irritable' => 'boolean', 'is_mood_sensitive' => 'boolean',
        'is_mood_swings' => 'boolean', 'is_mood_worthless_guilty' => 'boolean', 'is_mood_overwhelmed' => 'boolean', 'is_mood_hopes' => 'boolean', 'is_mood_depressed_sad_down' => 'boolean',

        // Urine & Stool
        'is_urine_stool_answered' => 'boolean', 'is_urine_stool_blood_in_urine' => 'boolean', 'is_urine_stool_blood_in_stool' => 'boolean',
        'is_urine_stool_hard' => 'boolean', 'is_urine_stool_normal' => 'boolean', 'is_urine_stool_soft' => 'boolean', 'is_urine_stool_diarrhea' => 'boolean',
        'is_urine_stool_something_else' => 'boolean', 'is_urine_stool_no_stool' => 'boolean',

        // Sleep
        'is_sleep_answered' => 'boolean', 'is_sleep_work_school_day' => 'boolean', 'is_sleep_free_day' => 'boolean',
        'is_sleep_trouble_asleep' => 'boolean', 'is_sleep_tired_rested' => 'boolean', 'is_sleep_wake_up_during_night' => 'boolean',

        // Exercise
        'is_exercise_answered' => 'boolean', 'is_exercise_less_thirty' => 'boolean', 'is_exercise_thirty_to_sixty' => 'boolean',
        'is_exercise_greater_sixty' => 'boolean', 'is_exercise_high_impact' => 'boolean', 'is_exercise_low_impact' => 'boolean',

        // Diet
        'is_diet_answered' => 'boolean', 'is_diet_vegetables' => 'boolean', 'is_diet_fruit' => 'boolean', 'is_diet_potato_rice_bread' => 'boolean',
        'is_diet_dairy' => 'boolean', 'is_diet_nuts_tofu_tempe' => 'boolean', 'is_diet_eggs' => 'boolean', 'is_diet_fish' => 'boolean',
        'is_diet_meat' => 'boolean', 'is_diet_soda' => 'boolean', 'is_diet_water' => 'boolean', 'is_diet_coffee' => 'boolean', 'is_diet_alcohol' => 'boolean',

        // Sex
        'is_sex_answered' => 'boolean', 'is_sex_today' => 'boolean', 'is_sex_avoided' => 'boolean', 'is_sex_bloodloss_during_after' => 'boolean',
        'is_sex_discomfort_pelvic_area' => 'boolean', 'is_sex_emotionally_physically_satisfied' => 'boolean',

        // Additional Notes
        'is_additional_notes_answered' => 'boolean',
    ];

    /** END CASTS GROUPS **/
    protected $fillable = [
        ...self::CORE_FIELDS,
        ...self::BLOOD_LOSS_FIELDS,
        ...self::PAIN_FIELDS,
        ...self::IMPACT_FIELDS,
        ...self::GENERAL_HEALTH_FIELDS,
        ...self::MOOD_FIELDS,
        ...self::URINE_STOOL_FIELDS,
        ...self::SLEEP_FIELDS,
        ...self::EXERCISE_FIELDS,
        ...self::DIET_FIELDS,
        ...self::SEX_FIELDS,
        ...self::ADDITIONAL_NOTES_FIELDS,
    ];

    protected $casts = [
        ...self::CORE_CASTS,
        ...self::INTEGER_CASTS,
        ...self::BOOLEAN_CASTS,
    ];

    protected $appends = [
        'blood_loss',
        'pain',
        'impact',
        'general_health',
        'mood',
        'stool_urine',
        'sleep',
        'diet',
        'exercise',
        'sex',
        'notes',
    ];

    /**
     * Scope: only rows for a participant.
     */
    public function scopeForParticipant(Builder $query, int $participantId): Builder
    {
        return $query->where('participant_id', $participantId);
    }

    /**
     * Get all fields for a specific section.
     */
    public static function getFieldsForSection(string $section): array
    {
        return match ($section) {
            'core' => self::CORE_FIELDS,
            'blood_loss' => self::BLOOD_LOSS_FIELDS,
            'pain' => self::PAIN_FIELDS,
            'impact' => self::IMPACT_FIELDS,
            'general_health' => self::GENERAL_HEALTH_FIELDS,
            'mood' => self::MOOD_FIELDS,
            'urine_stool' => self::URINE_STOOL_FIELDS,
            'sleep' => self::SLEEP_FIELDS,
            'exercise' => self::EXERCISE_FIELDS,
            'diet' => self::DIET_FIELDS,
            'sex' => self::SEX_FIELDS,
            'additional_notes' => self::ADDITIONAL_NOTES_FIELDS,
            default => [],
        };
    }

    /**
     * Get all available sections.
     */
    public static function getAvailableSections(): array
    {
        return [
            'core', 'blood_loss', 'pain', 'impact', 'general_health',
            'mood', 'urine_stool', 'sleep', 'exercise', 'diet', 'sex', 'additional_notes',
        ];
    }

    /**
     * Get all fillable fields as a flat array.
     */
    public static function getAllFillableFields(): array
    {
        return array_merge(
            self::CORE_FIELDS,
            self::BLOOD_LOSS_FIELDS,
            self::PAIN_FIELDS,
            self::IMPACT_FIELDS,
            self::GENERAL_HEALTH_FIELDS,
            self::MOOD_FIELDS,
            self::URINE_STOOL_FIELDS,
            self::SLEEP_FIELDS,
            self::EXERCISE_FIELDS,
            self::DIET_FIELDS,
            self::SEX_FIELDS,
            self::ADDITIONAL_NOTES_FIELDS
        );
    }

    /**
     * Check if a field belongs to a specific section.
     */
    public static function fieldBelongsToSection(string $field, string $section): bool
    {
        return in_array($field, self::getFieldsForSection($section));
    }

    /**
     * Convert top-level array keys from camelCase to snake_case.
     */
    public static function camelToSnake(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            $out[\Illuminate\Support\Str::snake($k)] = $v;
        }

        return $out;
    }

    /**
     * Get the participant that owns the PBAC record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /**
     * Pillar: blood_loss (structured)
     */
    public function getBloodLossAttribute(): ?array
    {
        $answered = (bool) ($this->is_blood_loss_answered ?? false) || (bool) ($this->no_blood_loss ?? false);
        if (! $answered) {
            return null;
        }
        $amount = (int) array_sum([
            (int) ($this->bl_pad_small ?? 0),
            (int) ($this->bl_pad_medium ?? 0),
            (int) ($this->bl_pad_large ?? 0),
            (int) ($this->bl_tampon_small ?? 0),
            (int) ($this->bl_tampon_medium ?? 0),
            (int) ($this->bl_tampon_large ?? 0),
        ]);
        if ((bool) ($this->no_blood_loss ?? false)) {
            $amount = 0;
        }
        $severity = 'none';
        if ($amount > 0) {
            if ((bool) ($this->is_bl_very_heavy ?? false)) {
                $severity = 'very_heavy';
            } elseif ((bool) ($this->is_bl_heavy ?? false)) {
                $severity = 'heavy';
            } elseif ((bool) ($this->is_bl_moderate ?? false)) {
                $severity = 'moderate';
            } elseif ((bool) ($this->is_bl_light ?? false)) {
                $severity = 'light';
            } elseif ((bool) ($this->is_bl_very_light ?? false)) {
                $severity = 'very_light';
            }
        }

        return [
            'answered' => true,
            'amount' => $amount,
            'severity' => $severity,
            'flags' => [
                'spotting' => (bool) ($this->spotting ?? false),
                'bloodClots' => (bool) ($this->is_bl_blood_clots ?? false),
                'doubleProtection' => (bool) ($this->is_bl_double_protection ?? false),
                'leakedClothes' => (bool) ($this->is_bl_leaked_clothes ?? false),
                'changedProducts' => (bool) ($this->is_bl_change_products ?? false),
                'wokeUpAtNight' => (bool) ($this->is_bl_wake_up_night ?? false),
            ],
        ];
    }

    /**
     * Pillar: pain (structured)
     */
    public function getPainAttribute(): ?array
    {
        $answered = (bool) ($this->is_pain_answered ?? false) || (bool) ($this->no_pain ?? false);
        if (! $answered) {
            return null;
        }
        $value = (int) ($this->pain_slider_value ?? 0);
        if ((bool) ($this->no_pain ?? false)) {
            $value = 0;
        }
        $during = [];
        if ((bool) ($this->is_pain_during_peeing ?? false)) {
            $during[] = 'peeing';
        }
        if ((bool) ($this->is_pain_during_pooping ?? false)) {
            $during[] = 'pooping';
        }
        if ((bool) ($this->is_pain_during_sex ?? false)) {
            $during[] = 'sex';
        }
        $regions = [];
        if ((bool) ($this->is_pain_image1_umbilical ?? false)) {
            $regions[] = 'umbilical';
        }
        if ((bool) ($this->is_pain_image1_left_umbilical ?? false)) {
            $regions[] = 'left_umbilical';
        }
        if ((bool) ($this->is_pain_image1_right_umbilical ?? false)) {
            $regions[] = 'right_umbilical';
        }
        if ((bool) ($this->is_pain_image1_bladder ?? false)) {
            $regions[] = 'bladder';
        }
        if ((bool) ($this->is_pain_image1_left_groin ?? false)) {
            $regions[] = 'left_groin';
        }
        if ((bool) ($this->is_pain_image1_right_groin ?? false)) {
            $regions[] = 'right_groin';
        }
        if ((bool) ($this->is_pain_image1_left_leg ?? false)) {
            $regions[] = 'left_leg';
        }
        if ((bool) ($this->is_pain_image1_right_leg ?? false)) {
            $regions[] = 'right_leg';
        }
        if ((bool) ($this->is_pain_image2_upper_back ?? false)) {
            $regions[] = 'upper_back';
        }
        if ((bool) ($this->is_pain_image2_back ?? false)) {
            $regions[] = 'back';
        }
        if ((bool) ($this->is_pain_image2_left_buttock ?? false)) {
            $regions[] = 'left_buttock';
        }
        if ((bool) ($this->is_pain_image2_right_buttock ?? false)) {
            $regions[] = 'right_buttock';
        }
        if ((bool) ($this->is_pain_image2_left_back_leg ?? false)) {
            $regions[] = 'left_back_leg';
        }
        if ((bool) ($this->is_pain_image2_right_back_leg ?? false)) {
            $regions[] = 'right_back_leg';
        }

        return [
            'answered' => true,
            'value' => $value,
            'during' => $during,
            'regions' => $regions,
        ];
    }

    /**
     * Pillar: impact (structured)
     */
    public function getImpactAttribute(): ?array
    {
        if (! (bool) ($this->is_impact_answered ?? false)) {
            return null;
        }
        $limitations = [];
        if ($this->is_impact_missed_work) {
            $limitations[] = 'missed_work';
        }
        if ($this->is_impact_missed_school) {
            $limitations[] = 'missed_school';
        }
        if ($this->is_impact_could_no_sport) {
            $limitations[] = 'could_no_sport';
        }
        if ($this->is_impact_missed_special_activities) {
            $limitations[] = 'missed_special_activities';
        }
        if ($this->is_impact_missed_leisure_activities) {
            $limitations[] = 'missed_leisure_activities';
        }
        if ($this->is_impact_had_to_sit_more) {
            $limitations[] = 'had_to_sit_more';
        }
        if ($this->is_impact_could_not_move) {
            $limitations[] = 'could_not_move';
        }
        if ($this->is_impact_had_to_stay_longer_in_bed) {
            $limitations[] = 'had_to_stay_longer_in_bed';
        }
        if ($this->is_impact_could_not_do_unpaid_work) {
            $limitations[] = 'could_not_do_unpaid_work';
        }
        if ($this->is_impact_other) {
            $limitations[] = 'other';
        }

        $medList = [];
        if ($this->is_impact_med_paracetamol) {
            $medList[] = 'paracetamol';
        }
        if ($this->is_impact_med_diclofenac) {
            $medList[] = 'diclofenac';
        }
        if ($this->is_impact_med_naproxen) {
            $medList[] = 'naproxen';
        }
        if ($this->is_impact_med_iron_pills) {
            $medList[] = 'iron_pills';
        }
        if ($this->is_impact_med_tramodol) {
            $medList[] = 'tramodol';
        }
        if ($this->is_impact_med_oxynorm) {
            $medList[] = 'oxynorm';
        }
        if ($this->is_impact_med_anticonception_pill) {
            $medList[] = 'anticonception_pill';
        }
        if ($this->is_impact_med_other_hormones) {
            $medList[] = 'other_hormones';
        }
        if ($this->is_impact_med_tranexamine_zuur) {
            $medList[] = 'tranexamine_zuur';
        }
        if ($this->is_impact_med_other) {
            $medList[] = 'other';
        }

        return [
            'answered' => true,
            'gradeYourDay' => (int) ($this->impact_slider_grade_your_day ?? 0),
            'complaints' => (int) ($this->impact_slider_complaints ?? 0),
            'limitations' => $limitations,
            'medications' => [
                'used' => (bool) ($this->is_impact_used_medication ?? false),
                'list' => $medList,
                'effective' => $this->is_impact_medicine_effective,
            ],
        ];
    }

    /**
     * Pillar: general_health (structured)
     */
    public function getGeneralHealthAttribute(): ?array
    {
        if (! (bool) ($this->is_general_health_answered ?? false)) {
            return null;
        }
        $symptoms = [];
        if ($this->is_general_health_dizzy) {
            $symptoms[] = 'dizzy';
        }
        if ($this->is_general_health_nauseous) {
            $symptoms[] = 'nauseous';
        }
        if ($this->is_general_health_headache_migraine) {
            $symptoms[] = 'headache_migraine';
        }
        if ($this->is_general_health_bloated) {
            $symptoms[] = 'bloated';
        }
        if ($this->is_general_health_painful_sensitive_breasts) {
            $symptoms[] = 'painful_sensitive_breasts';
        }
        if ($this->is_general_health_acne) {
            $symptoms[] = 'acne';
        }
        if ($this->is_general_health_muscle_joint_pain) {
            $symptoms[] = 'muscle_joint_pain';
        }

        return [
            'answered' => true,
            'energyLevel' => (int) ($this->general_health_energy_level_slider_value ?? 0),
            'symptoms' => $symptoms,
        ];
    }

    /**
     * Pillar: mood (structured)
     */
    public function getMoodAttribute(): ?array
    {
        if (! (bool) ($this->is_mood_answered ?? false)) {
            return null;
        }
        $positives = [];
        if ($this->is_mood_calm) {
            $positives[] = 'calm';
        }
        if ($this->is_mood_happy) {
            $positives[] = 'happy';
        }
        if ($this->is_mood_excited) {
            $positives[] = 'excited';
        }
        if ($this->is_mood_hopes) {
            $positives[] = 'hopes';
        }
        $negatives = [];
        if ($this->is_mood_anxious_stressed) {
            $negatives[] = 'anxious_stressed';
        }
        if ($this->is_mood_ashamed) {
            $negatives[] = 'ashamed';
        }
        if ($this->is_mood_angry_irritable) {
            $negatives[] = 'angry_irritable';
        }
        if ($this->is_mood_sensitive) {
            $negatives[] = 'sensitive';
        }
        if ($this->is_mood_swings) {
            $negatives[] = 'swings';
        }
        if ($this->is_mood_worthless_guilty) {
            $negatives[] = 'worthless_guilty';
        }
        if ($this->is_mood_overwhelmed) {
            $negatives[] = 'overwhelmed';
        }
        if ($this->is_mood_depressed_sad_down) {
            $negatives[] = 'depressed_sad_down';
        }

        return [
            'answered' => true,
            'positives' => $positives,
            'negatives' => $negatives,
        ];
    }

    /**
     * Pillar: stool_urine (structured)
     */
    public function getStoolUrineAttribute(): ?array
    {
        if (! (bool) ($this->is_urine_stool_answered ?? false)) {
            return null;
        }

        return [
            'answered' => true,
            'urine' => [
                'blood' => (bool) ($this->is_urine_stool_blood_in_urine ?? false),
                'painDuringPeeing' => (bool) ($this->is_pain_during_peeing ?? false),
            ],
            'stool' => [
                'blood' => (bool) ($this->is_urine_stool_blood_in_stool ?? false),
                'hard' => (bool) ($this->is_urine_stool_hard ?? false),
                'soft' => (bool) ($this->is_urine_stool_soft ?? false),
                'diarrhea' => (bool) ($this->is_urine_stool_diarrhea ?? false),
                'somethingElse' => (bool) ($this->is_urine_stool_something_else ?? false),
                'somethingElseText' => $this->is_urine_stool_something_else_text,
                'noStool' => (bool) ($this->is_urine_stool_no_stool ?? false),
                'normal' => (bool) ($this->is_urine_stool_normal ?? false),
            ],
        ];
    }

    /**
     * Pillar: sleep (structured)
     */
    public function getSleepAttribute(): ?array
    {
        $answered = (bool) ($this->is_sleep_answered ?? false);
        if (! $answered) {
            return null;
        }

        $sleepHours = null;

        if (! empty($this->sleep_fell_asleep_time) && ! empty($this->sleep_woke_up_time)) {
            try {
                $start = Carbon::createFromFormat('H:i', (string) $this->sleep_fell_asleep_time);
                $end = Carbon::createFromFormat('H:i', (string) $this->sleep_woke_up_time);
                if ($end->lessThanOrEqualTo($start)) {
                    $end->addDay();
                }
                $sleepHours = round($start->diffInMinutes($end) / 60, 1);
            } catch (\Exception $e) {
            }
        }

        if (is_null($sleepHours) && ! is_null($this->sleep_hours_of_sleep)) {
            try {
                $time = Carbon::createFromFormat('H:i', (string) $this->sleep_hours_of_sleep);
                $sleepHours = round($time->hour + ($time->minute / 60), 1);
            } catch (\Exception $e) {
                $sleepHours = null;
            }
        }

        return [
            'answered' => true,
            'fellAsleepTime' => $this->sleep_fell_asleep_time,
            'wokeUpTime' => $this->sleep_woke_up_time,
            'hoursOfSleep' => $this->sleep_hours_of_sleep,
            'calculatedHours' => $sleepHours,
            'isWorkSchoolDay' => (bool) ($this->is_sleep_work_school_day ?? false),
            'isFreeDay' => (bool) ($this->is_sleep_free_day ?? false),
            'troubleAsleep' => (bool) ($this->is_sleep_trouble_asleep ?? false),
            'tiredRested' => (bool) ($this->is_sleep_tired_rested ?? false),
            'wakeUpDuringNight' => (bool) ($this->is_sleep_wake_up_during_night ?? false),
        ];
    }

    /**
     * Pillar: diet (structured)
     */
    public function getDietAttribute(): ?array
    {
        if (! (bool) ($this->is_diet_answered ?? false)) {
            return null;
        }
        $positives = [];
        if ($this->is_diet_vegetables) {
            $positives[] = 'vegetables';
        }
        if ($this->is_diet_fruit) {
            $positives[] = 'fruit';
        }
        if ($this->is_diet_nuts_tofu_tempe) {
            $positives[] = 'nuts_tofu_tempe';
        }
        if ($this->is_diet_fish) {
            $positives[] = 'fish';
        }
        if ($this->is_diet_water) {
            $positives[] = 'water';
        }
        $negatives = [];
        if ($this->is_diet_soda) {
            $negatives[] = 'soda';
        }
        if ($this->is_diet_alcohol) {
            $negatives[] = 'alcohol';
        }
        $neutrals = [];
        if ($this->is_diet_potato_rice_bread) {
            $neutrals[] = 'potato_rice_bread';
        }
        if ($this->is_diet_dairy) {
            $neutrals[] = 'dairy';
        }
        if ($this->is_diet_eggs) {
            $neutrals[] = 'eggs';
        }
        if ($this->is_diet_meat) {
            $neutrals[] = 'meat';
        }
        if ($this->is_diet_coffee) {
            $neutrals[] = 'coffee';
        }

        return [
            'answered' => true,
            'positives' => $positives,
            'negatives' => $negatives,
            'neutrals' => $neutrals,
        ];
    }

    /**
     * Pillar: exercise (structured)
     */
    public function getExerciseAttribute(): ?array
    {
        if (! (bool) ($this->is_exercise_answered ?? false)) {
            return null;
        }
        $levels = [];
        if ($this->is_exercise_less_thirty) {
            $levels[] = 'less_thirty';
        }
        if ($this->is_exercise_thirty_to_sixty) {
            $levels[] = 'thirty_to_sixty';
        }
        if ($this->is_exercise_greater_sixty) {
            $levels[] = 'greater_sixty';
        }
        $impacts = [];
        if ($this->is_exercise_high_impact) {
            $impacts[] = 'high_impact';
        }
        if ($this->is_exercise_low_impact) {
            $impacts[] = 'low_impact';
        }
        $any = ! empty($levels) || ! empty($impacts);

        return [
            'answered' => true,
            'any' => $any,
            'levels' => $levels,
            'impacts' => $impacts,
        ];
    }

    /**
     * Pillar: sex (structured)
     */
    public function getSexAttribute(): ?array
    {
        if (! (bool) ($this->is_sex_answered ?? false)) {
            return null;
        }
        $issues = [];
        if ($this->is_sex_bloodloss_during_after) {
            $issues[] = 'bloodloss_during_after';
        }
        if ($this->is_sex_discomfort_pelvic_area) {
            $issues[] = 'discomfort_pelvic_area';
        }

        return [
            'answered' => true,
            'today' => (bool) ($this->is_sex_today ?? false),
            'avoided' => (bool) ($this->is_sex_avoided ?? false),
            'issues' => $issues,
            'satisfied' => (bool) ($this->is_sex_emotionally_physically_satisfied ?? false),
        ];
    }

    /**
     * Pillar: notes (structured)
     */
    public function getNotesAttribute(): array
    {
        $text = trim($this->additional_notes ?? '');

        return [
            'hasNote' => ! empty($text) || (bool) ($this->is_additional_notes_answered ?? false),
            'text' => ! empty($text) ? $text : null,
        ];
    }
}
