@push('scripts')
<script>
window.appLocale = '{{ app()->getLocale() }}';

window.healthDomainTranslations = {
    'blood_loss': '{{ __('participant.blood_loss') }}',
    'pain': '{{ __('participant.pain') }}',
    'impact': '{{ __('participant.impact') }}',
    'general_health': '{{ __('participant.general_health') }}',
    'mood': '{{ __('participant.mood') }}',
    'stool_urine': '{{ __('participant.stool_urine') }}',
    'sleep': '{{ __('participant.sleep') }}',
    'diet': '{{ __('participant.diet') }}',
    'exercise': '{{ __('participant.exercise') }}',
    'sex': '{{ __('participant.sexual_health') }}',
    'notes': '{{ __('participant.notes') }}'
};

window.translations = {
    'loading': '{{ __('participant.loading') }}',
    'more': '{{ __('participant.more') }}',
    'no_symptom_data_recorded': '{{ __('participant.no_symptom_data_recorded') }}',
    'previous': '{{ __('participant.previous') }}',
    'next': '{{ __('participant.next') }}',
    'select_date': '{{ __('participant.select_date') }}',
    'video': '{{ __('participant.video') }}',
    'prev': '{{ __('participant.prev') }}',
    'select': '{{ __('participant.select') }}'
};

window.cardStatusTranslations = {
    // Blood Loss
    'card_blood_loss_no_data': '{{ __('participant.card_blood_loss_no_data') }}',
    'card_blood_loss_spotting_detected': '{{ __('participant.card_blood_loss_spotting_detected') }}',
    'card_blood_loss_very_heavy': '{{ __('participant.card_blood_loss_very_heavy') }}',
    'card_blood_loss_heavy': '{{ __('participant.card_blood_loss_heavy') }}',
    'card_blood_loss_moderate': '{{ __('participant.card_blood_loss_moderate') }}',
    'card_blood_loss_light': '{{ __('participant.card_blood_loss_light') }}',
    'card_blood_loss_very_light': '{{ __('participant.card_blood_loss_very_light') }}',
    'card_blood_loss_spotting': '{{ __('participant.card_blood_loss_spotting') }}',
    
    // Pain
    'card_pain_no_pain': '{{ __('participant.card_pain_no_pain') }}',
    'card_pain_severe_pain': '{{ __('participant.card_pain_severe_pain') }}',
    'card_pain_moderate_pain': '{{ __('participant.card_pain_moderate_pain') }}',
    'card_pain_mild_pain': '{{ __('participant.card_pain_mild_pain') }}',
    'card_pain_light_pain': '{{ __('participant.card_pain_light_pain') }}',
    'card_pain_severe': '{{ __('participant.card_pain_severe') }}',
    'card_pain_moderate': '{{ __('participant.card_pain_moderate') }}',
    'card_pain_mild': '{{ __('participant.card_pain_mild') }}',
    'card_pain_light': '{{ __('participant.card_pain_light') }}',
    
    // Impact
    'card_impact_no_impact': '{{ __('participant.card_impact_no_impact') }}',
    'card_impact_great_day': '{{ __('participant.card_impact_great_day') }}',
    'card_impact_good_day': '{{ __('participant.card_impact_good_day') }}',
    'card_impact_challenging_day': '{{ __('participant.card_impact_challenging_day') }}',
    'card_impact_difficult_day': '{{ __('participant.card_impact_difficult_day') }}',
    'card_impact_great': '{{ __('participant.card_impact_great') }}',
    'card_impact_good': '{{ __('participant.card_impact_good') }}',
    'card_impact_challenging': '{{ __('participant.card_impact_challenging') }}',
    'card_impact_difficult': '{{ __('participant.card_impact_difficult') }}',
    
    // General Health
    'card_general_health_no_energy_data': '{{ __('participant.card_general_health_no_energy_data') }}',
    'card_general_health_high_energy_with_symptoms': '{{ __('participant.card_general_health_high_energy_with_symptoms') }}',
    'card_general_health_high_energy': '{{ __('participant.card_general_health_high_energy') }}',
    'card_general_health_good_energy_with_symptoms': '{{ __('participant.card_general_health_good_energy_with_symptoms') }}',
    'card_general_health_good_energy': '{{ __('participant.card_general_health_good_energy') }}',
    'card_general_health_moderate_energy': '{{ __('participant.card_general_health_moderate_energy') }}',
    'card_general_health_low_energy': '{{ __('participant.card_general_health_low_energy') }}',
    'card_general_health_very_low_energy': '{{ __('participant.card_general_health_very_low_energy') }}',
    'card_general_health_energy_very_low': '{{ __('participant.card_general_health_energy_very_low') }}',
    'card_general_health_energy_low': '{{ __('participant.card_general_health_energy_low') }}',
    'card_general_health_energy_moderate': '{{ __('participant.card_general_health_energy_moderate') }}',
    'card_general_health_energy_good': '{{ __('participant.card_general_health_energy_good') }}',
    'card_general_health_energy_high': '{{ __('participant.card_general_health_energy_high') }}',
    'card_general_health_unknown': '{{ __('participant.card_general_health_unknown') }}',
    'card_general_health_energy_1': '{{ __('participant.card_general_health_energy_1') }}',
    'card_general_health_energy_2': '{{ __('participant.card_general_health_energy_2') }}',
    'card_general_health_energy_3': '{{ __('participant.card_general_health_energy_3') }}',
    'card_general_health_energy_4': '{{ __('participant.card_general_health_energy_4') }}',
    'card_general_health_energy_5': '{{ __('participant.card_general_health_energy_5') }}',
    
    // Mood
    'card_mood_balanced': '{{ __('participant.card_mood_balanced') }}',
    'card_mood_positive_day': '{{ __('participant.card_mood_positive_day') }}',
    'card_mood_challenging_day': '{{ __('participant.card_mood_challenging_day') }}',
    'card_mood_positive': '{{ __('participant.card_mood_positive') }}',
    'card_mood_challenging': '{{ __('participant.card_mood_challenging') }}',
    
    // Stool/Urine
    'card_stool_urine_no_data': '{{ __('participant.card_stool_urine_no_data') }}',
    'card_stool_urine_blood_detected': '{{ __('participant.card_stool_urine_blood_detected') }}',
    'card_stool_urine_recorded': '{{ __('participant.card_stool_urine_recorded') }}',
    'card_stool_urine_blood_in_urine_and_stool': '{{ __('participant.card_stool_urine_blood_in_urine_and_stool') }}',
    'card_stool_urine_blood_in_urine': '{{ __('participant.card_stool_urine_blood_in_urine') }}',
    'card_stool_urine_blood_in_stool': '{{ __('participant.card_stool_urine_blood_in_stool') }}',
    
    // Sleep
    'card_sleep_sleep_tracked': '{{ __('participant.card_sleep_sleep_tracked') }}',
    'card_sleep_hours_template': '{{ __('participant.card_sleep_hours_template') }}',
    
    // Diet
    'card_diet_items_recorded': '{{ __('participant.card_diet_items_recorded') }}',
    'card_diet_diet_tracked': '{{ __('participant.card_diet_diet_tracked') }}',
    
    // Exercise
    'card_exercise_exercise_completed': '{{ __('participant.card_exercise_exercise_completed') }}',
    'card_exercise_more_than_60': '{{ __('participant.card_exercise_more_than_60') }}',
    'card_exercise_thirty_to_60': '{{ __('participant.card_exercise_thirty_to_60') }}',
    'card_exercise_less_than_30': '{{ __('participant.card_exercise_less_than_30') }}',
    
    // Sexual Health
    'card_sex_activity_recorded': '{{ __('participant.card_sex_activity_recorded') }}',
    'card_sex_avoided': '{{ __('participant.card_sex_avoided') }}',
    'card_sex_satisfied': '{{ __('participant.card_sex_satisfied') }}',
    
    // Notes
    'card_notes_note_available': '{{ __('participant.card_notes_note_available') }}',
    'card_notes_note_recorded': '{{ __('participant.card_notes_note_recorded') }}',
    
    // Common
    'card_common_unknown': '{{ __('participant.card_common_unknown') }}'
};

// Tooltip translations for JavaScript
window.tooltipTranslations = {
    // Region Labels
    'tooltip_region_umbilical': '{{ __('participant.tooltip_region_umbilical') }}',
    'tooltip_region_left_umbilical': '{{ __('participant.tooltip_region_left_umbilical') }}',
    'tooltip_region_right_umbilical': '{{ __('participant.tooltip_region_right_umbilical') }}',
    'tooltip_region_bladder': '{{ __('participant.tooltip_region_bladder') }}',
    'tooltip_region_left_groin': '{{ __('participant.tooltip_region_left_groin') }}',
    'tooltip_region_right_groin': '{{ __('participant.tooltip_region_right_groin') }}',
    'tooltip_region_left_leg': '{{ __('participant.tooltip_region_left_leg') }}',
    'tooltip_region_right_leg': '{{ __('participant.tooltip_region_right_leg') }}',
    'tooltip_region_upper_back': '{{ __('participant.tooltip_region_upper_back') }}',
    'tooltip_region_back': '{{ __('participant.tooltip_region_back') }}',
    'tooltip_region_left_buttock': '{{ __('participant.tooltip_region_left_buttock') }}',
    'tooltip_region_right_buttock': '{{ __('participant.tooltip_region_right_buttock') }}',
    'tooltip_region_left_back_leg': '{{ __('participant.tooltip_region_left_back_leg') }}',
    'tooltip_region_right_back_leg': '{{ __('participant.tooltip_region_right_back_leg') }}',
    'tooltip_region_headache_migraine': '{{ __('participant.tooltip_region_headache_migraine') }}',
    'tooltip_region_pain_during_peeing': '{{ __('participant.tooltip_region_pain_during_peeing') }}',
    'tooltip_region_pain_during_pooping': '{{ __('participant.tooltip_region_pain_during_pooping') }}',
    'tooltip_region_pain_during_sex': '{{ __('participant.tooltip_region_pain_during_sex') }}',
    
    // Mood Labels
    'mood_calm': '{{ __('participant.mood_calm') }}',
    'mood_happy': '{{ __('participant.mood_happy') }}',
    'mood_excited': '{{ __('participant.mood_excited') }}',
    'mood_anxious': '{{ __('participant.mood_anxious') }}',
    'mood_stressed': '{{ __('participant.mood_stressed') }}',
    'mood_ashamed': '{{ __('participant.mood_ashamed') }}',
    'mood_angry': '{{ __('participant.mood_angry') }}',
    'mood_irritable': '{{ __('participant.mood_irritable') }}',
    'mood_sensitive': '{{ __('participant.mood_sensitive') }}',
    'mood_mood_swings': '{{ __('participant.mood_mood_swings') }}',
    'mood_worthless': '{{ __('participant.mood_worthless') }}',
    'mood_guilty': '{{ __('participant.mood_guilty') }}',
    'mood_overwhelmed': '{{ __('participant.mood_overwhelmed') }}',
    'mood_hopeless': '{{ __('participant.mood_hopeless') }}',
    'mood_depressed': '{{ __('participant.mood_depressed') }}',
    
    // Stool/Urine Labels
    'stool_urine_normal': '{{ __('participant.stool_urine_normal') }}',
    'stool_urine_hard': '{{ __('participant.stool_urine_hard') }}',
    'stool_urine_soft': '{{ __('participant.stool_urine_soft') }}',
    'stool_urine_watery': '{{ __('participant.stool_urine_watery') }}',
    'stool_urine_something_else': '{{ __('participant.stool_urine_something_else') }}',
    'stool_urine_no_stool': '{{ __('participant.stool_urine_no_stool') }}',
    'stool_urine_blood': '{{ __('participant.stool_urine_blood') }}',
    
    // Sleep Labels
    'sleep_quality_good': '{{ __('participant.sleep_quality_good') }}',
    'sleep_quality_okay': '{{ __('participant.sleep_quality_okay') }}',
    'sleep_quality_poor': '{{ __('participant.sleep_quality_poor') }}',
    'sleep_issue_trouble_asleep': '{{ __('participant.sleep_issue_trouble_asleep') }}',
    'sleep_issue_wake_up_during_night': '{{ __('participant.sleep_issue_wake_up_during_night') }}',
    'sleep_issue_not_tired_rested': '{{ __('participant.sleep_issue_not_tired_rested') }}',
    
    // Diet Labels
    'diet_vegetables': '{{ __('participant.diet_vegetables') }}',
    'diet_fruit': '{{ __('participant.diet_fruit') }}',
    'diet_potato_rice_bread': '{{ __('participant.diet_potato_rice_bread') }}',
    'diet_dairy_products': '{{ __('participant.diet_dairy_products') }}',
    'diet_nuts_tofu_tempe': '{{ __('participant.diet_nuts_tofu_tempe') }}',
    'diet_egg': '{{ __('participant.diet_egg') }}',
    'diet_fish': '{{ __('participant.diet_fish') }}',
    'diet_meat': '{{ __('participant.diet_meat') }}',
    'diet_snacks': '{{ __('participant.diet_snacks') }}',
    'diet_water': '{{ __('participant.diet_water') }}',
    'diet_coffee': '{{ __('participant.diet_coffee') }}',
    'diet_soda': '{{ __('participant.diet_soda') }}',
    'diet_alcohol': '{{ __('participant.diet_alcohol') }}',
    
    // Exercise Labels
    'exercise_high_impact': '{{ __('participant.exercise_high_impact') }}',
    'exercise_low_impact': '{{ __('participant.exercise_low_impact') }}',
    'exercise_relaxation_exercise': '{{ __('participant.exercise_relaxation_exercise') }}',
    'exercise_duration_less_thirty': '{{ __('participant.exercise_duration_less_thirty') }}',
    'exercise_duration_thirty_to_sixty': '{{ __('participant.exercise_duration_thirty_to_sixty') }}',
    'exercise_duration_greater_sixty': '{{ __('participant.exercise_duration_greater_sixty') }}',
    
    // Sexual Health Labels
    'sex_issue_pain': '{{ __('participant.sex_issue_pain') }}',
    'sex_issue_discomfort': '{{ __('participant.sex_issue_discomfort') }}',
    'sex_issue_bleeding': '{{ __('participant.sex_issue_bleeding') }}',
    'sex_issue_dryness': '{{ __('participant.sex_issue_dryness') }}',
    'sex_issue_fatigue': '{{ __('participant.sex_issue_fatigue') }}',
    'sex_issue_discomfort_pelvic_area': '{{ __('participant.sex_issue_discomfort_pelvic_area') }}',
    'sex_status_satisfied': '{{ __('participant.sex_status_satisfied') }}',
    'sex_status_unsatisfied': '{{ __('participant.sex_status_unsatisfied') }}',
    'sex_status_avoided': '{{ __('participant.sex_status_avoided') }}',
    'sex_status_no_activity': '{{ __('participant.sex_status_no_activity') }}',
    
    // Limitation Labels
    'tooltip_limitation_used_medication': '{{ __('participant.tooltip_limitation_used_medication') }}',
    'tooltip_limitation_missed_work': '{{ __('participant.tooltip_limitation_missed_work') }}',
    'tooltip_limitation_missed_school': '{{ __('participant.tooltip_limitation_missed_school') }}',
    'tooltip_limitation_could_not_sport': '{{ __('participant.tooltip_limitation_could_not_sport') }}',
    'tooltip_limitation_missed_social_activities': '{{ __('participant.tooltip_limitation_missed_social_activities') }}',
    'tooltip_limitation_missed_leisure_activities': '{{ __('participant.tooltip_limitation_missed_leisure_activities') }}',
    'tooltip_limitation_had_to_sit_more': '{{ __('participant.tooltip_limitation_had_to_sit_more') }}',
    'tooltip_limitation_had_to_lie_down': '{{ __('participant.tooltip_limitation_had_to_lie_down') }}',
    'tooltip_limitation_had_to_stay_longer_in_bed': '{{ __('participant.tooltip_limitation_had_to_stay_longer_in_bed') }}',
    'tooltip_limitation_could_not_do_unpaid_work': '{{ __('participant.tooltip_limitation_could_not_do_unpaid_work') }}',
    'tooltip_limitation_other': '{{ __('participant.tooltip_limitation_other') }}',
    
    // Symptom Labels
    'tooltip_symptom_dizzy': '{{ __('participant.tooltip_symptom_dizzy') }}',
    'tooltip_symptom_nauseous': '{{ __('participant.tooltip_symptom_nauseous') }}',
    'tooltip_symptom_headache_migraine': '{{ __('participant.tooltip_symptom_headache_migraine') }}',
    'tooltip_symptom_bloated': '{{ __('participant.tooltip_symptom_bloated') }}',
    'tooltip_symptom_painful_sensitive_breasts': '{{ __('participant.tooltip_symptom_painful_sensitive_breasts') }}',
    'tooltip_symptom_acne': '{{ __('participant.tooltip_symptom_acne') }}',
    'tooltip_symptom_muscle_joint_pain': '{{ __('participant.tooltip_symptom_muscle_joint_pain') }}',
    'tooltip_symptom_fatigue': '{{ __('participant.tooltip_symptom_fatigue') }}',
    'tooltip_symptom_headache': '{{ __('participant.tooltip_symptom_headache') }}',
    'tooltip_symptom_weakness': '{{ __('participant.tooltip_symptom_weakness') }}',
    'tooltip_symptom_joint_pain': '{{ __('participant.tooltip_symptom_joint_pain') }}',
    'tooltip_symptom_muscle_pain': '{{ __('participant.tooltip_symptom_muscle_pain') }}',
    'tooltip_symptom_fever': '{{ __('participant.tooltip_symptom_fever') }}',
    'tooltip_symptom_chills': '{{ __('participant.tooltip_symptom_chills') }}',
    'tooltip_symptom_sweating': '{{ __('participant.tooltip_symptom_sweating') }}',
    
    // Mood Labels
    'tooltip_mood_calm': '{{ __('participant.tooltip_mood_calm') }}',
    'tooltip_mood_happy': '{{ __('participant.tooltip_mood_happy') }}',
    'tooltip_mood_excited': '{{ __('participant.tooltip_mood_excited') }}',
    'tooltip_mood_hopes': '{{ __('participant.tooltip_mood_hopes') }}',
    'tooltip_mood_anxious_stressed': '{{ __('participant.tooltip_mood_anxious_stressed') }}',
    'tooltip_mood_ashamed': '{{ __('participant.tooltip_mood_ashamed') }}',
    'tooltip_mood_angry_irritable': '{{ __('participant.tooltip_mood_angry_irritable') }}',
    'tooltip_mood_sad': '{{ __('participant.tooltip_mood_sad') }}',
    'tooltip_mood_mood_swings': '{{ __('participant.tooltip_mood_mood_swings') }}',
    'tooltip_mood_worthless_guilty': '{{ __('participant.tooltip_mood_worthless_guilty') }}',
    'tooltip_mood_overwhelmed': '{{ __('participant.tooltip_mood_overwhelmed') }}',
    'tooltip_mood_hopeless': '{{ __('participant.tooltip_mood_hopeless') }}',
    'tooltip_mood_depressed_sad_down': '{{ __('participant.tooltip_mood_depressed_sad_down') }}',
    
    // Diet Labels
    'tooltip_diet_vegetables': '{{ __('participant.tooltip_diet_vegetables') }}',
    'tooltip_diet_fruit': '{{ __('participant.tooltip_diet_fruit') }}',
    'tooltip_diet_potato_rice_bread': '{{ __('participant.tooltip_diet_potato_rice_bread') }}',
    'tooltip_diet_dairy': '{{ __('participant.tooltip_diet_dairy') }}',
    'tooltip_diet_nuts_tofu_tempe': '{{ __('participant.tooltip_diet_nuts_tofu_tempe') }}',
    'tooltip_diet_eggs': '{{ __('participant.tooltip_diet_eggs') }}',
    'tooltip_diet_fish': '{{ __('participant.tooltip_diet_fish') }}',
    'tooltip_diet_meat': '{{ __('participant.tooltip_diet_meat') }}',
    'tooltip_diet_snacks': '{{ __('participant.tooltip_diet_snacks') }}',
    'tooltip_diet_soda': '{{ __('participant.tooltip_diet_soda') }}',
    'tooltip_diet_water': '{{ __('participant.tooltip_diet_water') }}',
    'tooltip_diet_coffee': '{{ __('participant.tooltip_diet_coffee') }}',
    'tooltip_diet_alcohol': '{{ __('participant.tooltip_diet_alcohol') }}',
    
    // Tooltip Messages
    'tooltip_no_blood_loss_recorded': '{{ __('participant.tooltip_no_blood_loss_recorded') }}',
    'tooltip_amount': '{{ __('participant.tooltip_amount') }}',
    'tooltip_spotting_detected': '{{ __('participant.tooltip_spotting_detected') }}',
    'tooltip_severity': '{{ __('participant.tooltip_severity') }}',
    'tooltip_no_pain_recorded': '{{ __('participant.tooltip_no_pain_recorded') }}',
    'tooltip_pain_level': '{{ __('participant.tooltip_pain_level') }}',
    'tooltip_regions': '{{ __('participant.tooltip_regions') }}',
    'tooltip_no_impact_recorded': '{{ __('participant.tooltip_no_impact_recorded') }}',
    'tooltip_grade': '{{ __('participant.tooltip_grade') }}',
    'tooltip_limitations': '{{ __('participant.tooltip_limitations') }}',
    'tooltip_grade_your_day': '{{ __('participant.tooltip_grade_your_day') }}',
    'tooltip_no_energy_level_recorded': '{{ __('participant.tooltip_no_energy_level_recorded') }}',
    'tooltip_energy': '{{ __('participant.tooltip_energy') }}',
    'tooltip_symptoms': '{{ __('participant.tooltip_symptoms') }}',
    'tooltip_energy_level': '{{ __('participant.tooltip_energy_level') }}',
    'tooltip_no_mood_indicators_recorded': '{{ __('participant.tooltip_no_mood_indicators_recorded') }}',
    'tooltip_positive': '{{ __('participant.tooltip_positive') }}',
    'tooltip_negative': '{{ __('participant.tooltip_negative') }}',
    'tooltip_no_stool_urine_issues_recorded': '{{ __('participant.tooltip_no_stool_urine_issues_recorded') }}',
    'tooltip_issues': '{{ __('participant.tooltip_issues') }}',
    'tooltip_blood_in_urine': '{{ __('participant.tooltip_blood_in_urine') }}',
    'tooltip_blood_in_stool': '{{ __('participant.tooltip_blood_in_stool') }}',
    'tooltip_and': '{{ __('participant.tooltip_and') }}',
    'tooltip_no_sleep_data_recorded': '{{ __('participant.tooltip_no_sleep_data_recorded') }}',
    'tooltip_sleep': '{{ __('participant.tooltip_sleep') }}',
    'tooltip_hours': '{{ __('participant.tooltip_hours') }}',
    'tooltip_trouble_falling_asleep': '{{ __('participant.tooltip_trouble_falling_asleep') }}',
    'tooltip_woke_up_during_night': '{{ __('participant.tooltip_woke_up_during_night') }}',
    'tooltip_not_well_rested': '{{ __('participant.tooltip_not_well_rested') }}',
    'tooltip_no_diet_items_recorded': '{{ __('participant.tooltip_no_diet_items_recorded') }}',
    'tooltip_good': '{{ __('participant.tooltip_good') }}',
    'tooltip_poor': '{{ __('participant.tooltip_poor') }}',
    'tooltip_neutral': '{{ __('participant.tooltip_neutral') }}',
    'tooltip_no_exercise_recorded': '{{ __('participant.tooltip_no_exercise_recorded') }}',
    'tooltip_exercise_completed': '{{ __('participant.tooltip_exercise_completed') }}',
    'tooltip_duration': '{{ __('participant.tooltip_duration') }}',
    'tooltip_greater_60_minutes': '{{ __('participant.tooltip_greater_60_minutes') }}',
    'tooltip_30_60_minutes': '{{ __('participant.tooltip_30_60_minutes') }}',
    'tooltip_less_30_minutes': '{{ __('participant.tooltip_less_30_minutes') }}',
    'tooltip_type': '{{ __('participant.tooltip_type') }}',
    'tooltip_high_impact': '{{ __('participant.tooltip_high_impact') }}',
    'tooltip_low_impact': '{{ __('participant.tooltip_low_impact') }}',
    'tooltip_precision_exercise': '{{ __('participant.tooltip_precision_exercise') }}',
    'tooltip_no_sexual_activity_recorded': '{{ __('participant.tooltip_no_sexual_activity_recorded') }}',
    'tooltip_sexual_activity_avoided': '{{ __('participant.tooltip_sexual_activity_avoided') }}',
    'tooltip_sexual_activity_with_issues': '{{ __('participant.tooltip_sexual_activity_with_issues') }}',
    'tooltip_sexual_activity_satisfied': '{{ __('participant.tooltip_sexual_activity_satisfied') }}',
    'tooltip_sexual_activity_recorded': '{{ __('participant.tooltip_sexual_activity_recorded') }}',
    'tooltip_no_notes_recorded': '{{ __('participant.tooltip_no_notes_recorded') }}',
    'tooltip_note': '{{ __('participant.tooltip_note') }}',
    'tooltip_note_recorded': '{{ __('participant.tooltip_note_recorded') }}'
};

window.modalTranslations = {
    // Blood Loss
    'modal_blood_loss_title': '{{ __('participant.modal_blood_loss_title') }}',
    'modal_blood_loss_severity_title': '{{ __('participant.modal_blood_loss_severity_title') }}',
    'modal_blood_loss_severity': '{{ __('participant.modal_blood_loss_severity') }}',
    'modal_spotting_title': '{{ __('participant.modal_spotting_title') }}',
    'modal_spotting_description': '{{ __('participant.modal_spotting_description') }}',
    'modal_blood_loss_very_light': '{{ __('participant.modal_blood_loss_very_light') }}',
    'modal_blood_loss_light': '{{ __('participant.modal_blood_loss_light') }}',
    'modal_blood_loss_moderate': '{{ __('participant.modal_blood_loss_moderate') }}',
    'modal_blood_loss_heavy': '{{ __('participant.modal_blood_loss_heavy') }}',
    'modal_blood_loss_very_heavy': '{{ __('participant.modal_blood_loss_very_heavy') }}',
    'modal_blood_loss_recorded': '{{ __('participant.modal_blood_loss_recorded') }}',
    'modal_blood_loss_tracking_title': '{{ __('participant.modal_blood_loss_tracking_title') }}',
    'modal_blood_loss_indicators': '{{ __('participant.modal_blood_loss_indicators') }}',
    'modal_blood_loss_blood_clots': '{{ __('participant.modal_blood_loss_blood_clots') }}',
    'modal_blood_loss_double_protection': '{{ __('participant.modal_blood_loss_double_protection') }}',
    'modal_blood_loss_leaked_clothes': '{{ __('participant.modal_blood_loss_leaked_clothes') }}',
    'modal_blood_loss_changed_products': '{{ __('participant.modal_blood_loss_changed_products') }}',
    'modal_blood_loss_woke_up_night': '{{ __('participant.modal_blood_loss_woke_up_night') }}',
    
    // Pain
    'modal_pain_title': '{{ __('participant.modal_pain_title') }}',
    'modal_pain_level_title': '{{ __('participant.modal_pain_level_title') }}',
    'modal_pain_regions_title': '{{ __('participant.modal_pain_regions_title') }}',
    'modal_pain_level': '{{ __('participant.modal_pain_level') }}',
    'modal_pain_during_title': '{{ __('participant.modal_pain_during_title') }}',
    
    // Mood
    'modal_mood_title': '{{ __('participant.modal_mood_title') }}',
    'modal_mood_positive_title': '{{ __('participant.modal_mood_positive_title') }}',
    'modal_mood_negative_title': '{{ __('participant.modal_mood_negative_title') }}',
    'modal_mood_positive_day': '{{ __('participant.modal_mood_positive_day') }}',
    'modal_mood_challenging_day': '{{ __('participant.modal_mood_challenging_day') }}',
    'modal_mood_balanced_day': '{{ __('participant.modal_mood_balanced_day') }}',
    
    // Impact
    'modal_impact_title': '{{ __('participant.modal_impact_title') }}',
    'modal_impact_grade_title': '{{ __('participant.modal_impact_grade_title') }}',
    'modal_impact_limitations_title': '{{ __('participant.modal_impact_limitations_title') }}',
    'modal_impact_no_limitations_title': '{{ __('participant.modal_impact_no_limitations_title') }}',
    'modal_impact_your_day': '{{ __('participant.modal_impact_your_day') }}',
    'modal_impact_horrible_day': '{{ __('participant.modal_impact_horrible_day') }}',
    'modal_impact_normal_day': '{{ __('participant.modal_impact_normal_day') }}',
    'modal_impact_perfect_day': '{{ __('participant.modal_impact_perfect_day') }}',
    'modal_impact_complaints_title': '{{ __('participant.modal_impact_complaints_title') }}',
    'modal_impact_complaints_nothing': '{{ __('participant.modal_impact_complaints_nothing') }}',
    'modal_impact_complaints_half': '{{ __('participant.modal_impact_complaints_half') }}',
    'modal_impact_complaints_usual': '{{ __('participant.modal_impact_complaints_usual') }}',
    'modal_impact_medications_title': '{{ __('participant.modal_impact_medications_title') }}',
    'modal_impact_med_paracetamol': '{{ __('participant.modal_impact_med_paracetamol') }}',
    'modal_impact_med_diclofenac': '{{ __('participant.modal_impact_med_diclofenac') }}',
    'modal_impact_med_naproxen': '{{ __('participant.modal_impact_med_naproxen') }}',
    'modal_impact_med_iron_pills': '{{ __('participant.modal_impact_med_iron_pills') }}',
    'modal_impact_med_tramodol': '{{ __('participant.modal_impact_med_tramodol') }}',
    'modal_impact_med_oxynorm': '{{ __('participant.modal_impact_med_oxynorm') }}',
    'modal_impact_med_anticonception_pill': '{{ __('participant.modal_impact_med_anticonception_pill') }}',
    'modal_impact_med_other_hormones': '{{ __('participant.modal_impact_med_other_hormones') }}',
    'modal_impact_med_tranexamine_zuur': '{{ __('participant.modal_impact_med_tranexamine_zuur') }}',
    'modal_impact_med_other': '{{ __('participant.modal_impact_med_other') }}',
    'modal_impact_med_effectiveness': '{{ __('participant.modal_impact_med_effectiveness') }}',
    'modal_impact_med_not_effective': '{{ __('participant.modal_impact_med_not_effective') }}',
    'modal_impact_med_effective': '{{ __('participant.modal_impact_med_effective') }}',
    'modal_impact_med_very_effective': '{{ __('participant.modal_impact_med_very_effective') }}',
    'modal_daily_grade': '{{ __('participant.modal_daily_grade') }}',
    
    // General Health
    'modal_general_health_title': '{{ __('participant.modal_general_health_title') }}',
    'modal_general_health_energy_title': '{{ __('participant.modal_general_health_energy_title') }}',
    'modal_general_health_symptoms_title': '{{ __('participant.modal_general_health_symptoms_title') }}',
    'modal_general_health_no_symptoms_title': '{{ __('participant.modal_general_health_no_symptoms_title') }}',
    'modal_general_health_current_level': '{{ __('participant.modal_general_health_current_level') }}',
    
    // Stool/Urine
    'modal_stool_urine_title': '{{ __('participant.modal_stool_urine_title') }}',
    'modal_stool_urine_consistency_title': '{{ __('participant.modal_stool_urine_consistency_title') }}',
    'modal_stool_urine_blood_detection_title': '{{ __('participant.modal_stool_urine_blood_detection_title') }}',
    'modal_stool_urine_status': '{{ __('participant.modal_stool_urine_status') }}',
    'modal_stool_urine_blood_detected': '{{ __('participant.modal_stool_urine_blood_detected') }}',
    'modal_stool_urine_blood_in_stool': '{{ __('participant.modal_stool_urine_blood_in_stool') }}',
    
    // Sleep
    'modal_sleep_title': '{{ __('participant.modal_sleep_title') }}',
    'modal_sleep_schedule_title': '{{ __('participant.modal_sleep_schedule_title') }}',
    'modal_sleep_issues_title': '{{ __('participant.modal_sleep_issues_title') }}',
    'modal_sleep_no_issues_title': '{{ __('participant.modal_sleep_no_issues_title') }}',
    'modal_sleep_work_school_day': '{{ __('participant.modal_sleep_work_school_day') }}',
    'modal_sleep_free_day': '{{ __('participant.modal_sleep_free_day') }}',
    'modal_sleep_fell_asleep': '{{ __('participant.modal_sleep_fell_asleep') }}',
    'modal_sleep_woke_up': '{{ __('participant.modal_sleep_woke_up') }}',
    'modal_sleep_hours': '{{ __('participant.modal_sleep_hours') }}',
    
    // Diet
    'modal_diet_title': '{{ __('participant.modal_diet_title') }}',
    'modal_diet_total_items': '{{ __('participant.modal_diet_total_items') }}',
    
    // Exercise
    'modal_exercise_title': '{{ __('participant.modal_exercise_title') }}',
    'modal_exercise_duration_title': '{{ __('participant.modal_exercise_duration_title') }}',
    'modal_exercise_activity_types_title': '{{ __('participant.modal_exercise_activity_types_title') }}',
    'modal_exercise_no_exercise_title': '{{ __('participant.modal_exercise_no_exercise_title') }}',
    'modal_exercise_status': '{{ __('participant.modal_exercise_status') }}',
    'modal_exercise_completed': '{{ __('participant.modal_exercise_completed') }}',
    'modal_exercise_rest_day': '{{ __('participant.modal_exercise_rest_day') }}',
    
    // Sexual Health
    'modal_sexual_health_title': '{{ __('participant.modal_sexual_health_title') }}',
    'modal_sexual_health_activity_title': '{{ __('participant.modal_sexual_health_activity_title') }}',
    'modal_sexual_health_issues_title': '{{ __('participant.modal_sexual_health_issues_title') }}',
    'modal_sexual_health_status': '{{ __('participant.modal_sexual_health_status') }}',
    'modal_sexual_health_had_sex_today': '{{ __('participant.modal_sexual_health_had_sex_today') }}',
    'modal_sexual_health_avoided_sex': '{{ __('participant.modal_sexual_health_avoided_sex') }}',
    'modal_sexual_health_no_activity': '{{ __('participant.modal_sexual_health_no_activity') }}',
    
    // Notes
    'modal_notes_title': '{{ __('participant.modal_notes_title') }}',
    'modal_notes_content_title': '{{ __('participant.modal_notes_content_title') }}',
    'modal_notes_no_notes_title': '{{ __('participant.modal_notes_no_notes_title') }}',
    
    // General
    'modal_no_data_recorded': '{{ __('participant.modal_no_data_recorded') }}',
    'modal_great': '{{ __('participant.modal_great') }}',
    'modal_current_level': '{{ __('participant.modal_current_level') }}',
    'modal_overall': '{{ __('participant.modal_overall') }}',
    'modal_level': '{{ __('participant.modal_level') }}'
};
</script>
@endpush
