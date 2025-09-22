<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PbacResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            // *** Core *** //
            'reportedDate' => $this->reported_date?->format('Y-m-d'),
            'isLive' => $this->is_live,

            // *** Blood loss *** //
            'isBloodLossAnswered' => $this->is_blood_loss_answered,
            'menstrualBloodLoss' => $this->menstrual_blood_loss,
            'spotting' => $this->spotting,
            'noBloodLoss' => $this->no_blood_loss,
            'noPain' => $this->no_pain,
            'isBlFirstDayPeriod' => $this->is_bl_first_day_period,
            'isBlPads' => $this->is_bl_pads,
            'isBlTampon' => $this->is_bl_tampon,
            'isBlMenstrualCup' => $this->is_bl_menstrual_cup,
            'isBlPeriodUnderwear' => $this->is_bl_period_underwear,
            'isBlOther' => $this->is_bl_other,
            'isBlOtherText' => $this->is_bl_other_text,
            'blPadSmall' => $this->bl_pad_small,
            'blPadMedium' => $this->bl_pad_medium,
            'blPadLarge' => $this->bl_pad_large,
            'blTamponSmall' => $this->bl_tampon_small,
            'blTamponMedium' => $this->bl_tampon_medium,
            'blTamponLarge' => $this->bl_tampon_large,
            'isBlVeryLight' => $this->is_bl_very_light,
            'isBlLight' => $this->is_bl_light,
            'isBlModerate' => $this->is_bl_moderate,
            'isBlHeavy' => $this->is_bl_heavy,
            'isBlVeryHeavy' => $this->is_bl_very_heavy,
            'isBlBloodClots' => $this->is_bl_blood_clots,
            'isBlDoubleProtection' => $this->is_bl_double_protection,
            'isBlLeakedClothes' => $this->is_bl_leaked_clothes,
            'isBlChangeProducts' => $this->is_bl_change_products,
            'isBlWakeUpNight' => $this->is_bl_wake_up_night,

            // *** Pain *** //
            'isPainAnswered' => $this->is_pain_answered,
            'painSliderValue' => $this->pain_slider_value,
            'isPainHeadacheMigraine' => $this->is_pain_headache_migraine,
            'isPainDuringPeeing' => $this->is_pain_during_peeing,
            'isPainDuringPooping' => $this->is_pain_during_pooping,
            'isPainDuringSex' => $this->is_pain_during_sex,
            'isPainImage1Umbilical' => $this->is_pain_image1_umbilical,
            'isPainImage1LeftUmbilical' => $this->is_pain_image1_left_umbilical,
            'isPainImage1RightUmbilical' => $this->is_pain_image1_right_umbilical,
            'isPainImage1Bladder' => $this->is_pain_image1_bladder,
            'isPainImage1LeftGroin' => $this->is_pain_image1_left_groin,
            'isPainImage1RightGroin' => $this->is_pain_image1_right_groin,
            'isPainImage1LeftLeg' => $this->is_pain_image1_left_leg,
            'isPainImage1RightLeg' => $this->is_pain_image1_right_leg,
            'isPainImage2UpperBack' => $this->is_pain_image2_upper_back,
            'isPainImage2Back' => $this->is_pain_image2_back,
            'isPainImage2LeftButtock' => $this->is_pain_image2_left_buttock,
            'isPainImage2RightButtock' => $this->is_pain_image2_right_buttock,
            'isPainImage2LeftBackLeg' => $this->is_pain_image2_left_back_leg,
            'isPainImage2RightBackLeg' => $this->is_pain_image2_right_back_leg,

            // *** Impact *** //
            'isImpactAnswered' => $this->is_impact_answered,
            'impactSliderGradeYourDay' => $this->impact_slider_grade_your_day,
            'impactSliderComplaints' => $this->impact_slider_complaints,
            'isImpactUsedMedication' => $this->is_impact_used_medication,
            'isImpactMissedWork' => $this->is_impact_missed_work,
            'isImpactMissedSchool' => $this->is_impact_missed_school,
            'isImpactCouldNoSport' => $this->is_impact_could_no_sport,
            'isImpactMissedSpecialActivities' => $this->is_impact_missed_special_activities,
            'isImpactMissedLeisureActivities' => $this->is_impact_missed_leisure_activities,
            'isImpactHadToSitMore' => $this->is_impact_had_to_sit_more,
            'isImpactCouldNotMove' => $this->is_impact_could_not_move,
            'isImpactHadToStayLongerInBed' => $this->is_impact_had_to_stay_longer_in_bed,
            'isImpactCouldNotDoUnpaidWork' => $this->is_impact_could_not_do_unpaid_work,
            'isImpactOther' => $this->is_impact_other,
            'isImpactOtherText' => $this->is_impact_other_text,
            'isImpactMedParacetamol' => $this->is_impact_med_paracetamol,
            'isImpactMedDiclofenac' => $this->is_impact_med_diclofenac,
            'isImpactMedNaproxen' => $this->is_impact_med_naproxen,
            'isImpactMedIronPills' => $this->is_impact_med_iron_pills,
            'isImpactMedTramodol' => $this->is_impact_med_tramodol,
            'isImpactMedOxynorm' => $this->is_impact_med_oxynorm,
            'isImpactMedAnticonceptionPill' => $this->is_impact_med_anticonception_pill,
            'isImpactMedOtherHormones' => $this->is_impact_med_other_hormones,
            'isImpactMedTranexamineZuur' => $this->is_impact_med_tranexamine_zuur,
            'isImpactMedOther' => $this->is_impact_med_other,
            'isImpactMedOtherText' => $this->is_impact_med_other_text,
            'isImpactMedicineEffective' => $this->is_impact_medicine_effective,

            // *** General health *** //
            'isGeneralHealthAnswered' => $this->is_general_health_answered,
            'generalHealthEnergyLevelSliderValue' => $this->general_health_energy_level_slider_value,
            'isGeneralHealthDizzy' => $this->is_general_health_dizzy,
            'isGeneralHealthNauseous' => $this->is_general_health_nauseous,
            'isGeneralHealthHeadacheMigraine' => $this->is_general_health_headache_migraine,
            'isGeneralHealthBloated' => $this->is_general_health_bloated,
            'isGeneralHealthPainfulSensitiveBreasts' => $this->is_general_health_painful_sensitive_breasts,
            'isGeneralHealthAcne' => $this->is_general_health_acne,
            'isGeneralHealthMuscleJointPain' => $this->is_general_health_muscle_joint_pain,

            // *** Mood *** //
            'isMoodAnswered' => $this->is_mood_answered,
            'isMoodCalm' => $this->is_mood_calm,
            'isMoodHappy' => $this->is_mood_happy,
            'isMoodExcited' => $this->is_mood_excited,
            'isMoodAnxiousStressed' => $this->is_mood_anxious_stressed,
            'isMoodAshamed' => $this->is_mood_ashamed,
            'isMoodAngryIrritable' => $this->is_mood_angry_irritable,
            'isMoodSensitive' => $this->is_mood_sensitive,
            'isMoodSwings' => $this->is_mood_swings,
            'isMoodWorthlessGuilty' => $this->is_mood_worthless_guilty,
            'isMoodOverwhelmed' => $this->is_mood_overwhelmed,
            'isMoodHopes' => $this->is_mood_hopes,
            'isMoodDepressedSadDown' => $this->is_mood_depressed_sad_down,

            // *** Urine/Stool *** //
            'isUrineStoolAnswered' => $this->is_urine_stool_answered,
            'isUrineStoolBloodInUrine' => $this->is_urine_stool_blood_in_urine,
            'isUrineStoolBloodInStool' => $this->is_urine_stool_blood_in_stool,
            'isUrineStoolHard' => $this->is_urine_stool_hard,
            'isUrineStoolNormal' => $this->is_urine_stool_normal,
            'isUrineStoolSoft' => $this->is_urine_stool_soft,
            'isUrineStoolDiarrhea' => $this->is_urine_stool_diarrhea,
            'isUrineStoolSomethingElse' => $this->is_urine_stool_something_else,
            'isUrineStoolSomethingElseText' => $this->is_urine_stool_something_else_text,
            'isUrineStoolNoStool' => $this->is_urine_stool_no_stool,

            // *** Sleep *** //
            'isSleepAnswered' => $this->is_sleep_answered,
            'sleepFellAsleepTime' => $this->sleep_fell_asleep_time,
            'sleepWokeUpTime' => $this->sleep_woke_up_time,
            'sleepHoursOfSleep' => $this->sleep_hours_of_sleep,
            'isSleepWorkSchoolDay' => $this->is_sleep_work_school_day,
            'isSleepFreeDay' => $this->is_sleep_free_day,
            'isSleepTroubleAsleep' => $this->is_sleep_trouble_asleep,
            'isSleepTiredRested' => $this->is_sleep_tired_rested,
            'isSleepWakeUpDuringNight' => $this->is_sleep_wake_up_during_night,

            // *** Exercise *** //
            'isExerciseAnswered' => $this->is_exercise_answered,
            'isExerciseLessThirty' => $this->is_exercise_less_thirty,
            'isExerciseThirtyToSixty' => $this->is_exercise_thirty_to_sixty,
            'isExerciseGreaterSixty' => $this->is_exercise_greater_sixty,
            'isExerciseHighImpact' => $this->is_exercise_high_impact,
            'isExerciseLowImpact' => $this->is_exercise_low_impact,

            // *** Diet *** //
            'isDietAnswered' => $this->is_diet_answered,
            'isDietVegetables' => $this->is_diet_vegetables,
            'isDietFruit' => $this->is_diet_fruit,
            'isDietPotatoRiceBread' => $this->is_diet_potato_rice_bread,
            'isDietDairy' => $this->is_diet_dairy,
            'isDietNutsTofuTempe' => $this->is_diet_nuts_tofu_tempe,
            'isDietEggs' => $this->is_diet_eggs,
            'isDietFish' => $this->is_diet_fish,
            'isDietMeat' => $this->is_diet_meat,
            'isDietSoda' => $this->is_diet_soda,
            'isDietWater' => $this->is_diet_water,
            'isDietCoffee' => $this->is_diet_coffee,
            'isDietAlcohol' => $this->is_diet_alcohol,

            // *** Sex *** //
            'isSexAnswered' => $this->is_sex_answered,
            'isSexToday' => $this->is_sex_today,
            'isSexAvoided' => $this->is_sex_avoided,
            'isSexBloodlossDuringAfter' => $this->is_sex_bloodloss_during_after,
            'isSexDiscomfortPelvicArea' => $this->is_sex_discomfort_pelvic_area,
            'isSexEmotionallyPhysicallySatisfied' => $this->is_sex_emotionally_physically_satisfied,

            // *** Notes *** //
            'isAdditionalNotesAnswered' => $this->is_additional_notes_answered,
            'additionalNotes' => $this->additional_notes,

            // *** Structured Pillars *** //
            'bloodLoss' => $this->blood_loss,
            'pain' => $this->pain,
            'impact' => $this->impact,
            'generalHealth' => $this->general_health,
            'mood' => $this->mood,
            'stoolUrine' => $this->stool_urine,
            'sleep' => $this->sleep,
            'diet' => $this->diet,
            'exercise' => $this->exercise,
            'sex' => $this->sex,
            'notes' => $this->notes,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
