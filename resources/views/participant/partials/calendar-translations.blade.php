@push('scripts')
<script>
// Set current locale for JavaScript
window.appLocale = '{{ app()->getLocale() }}';

// Health domain translations
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

// General UI translations for JavaScript
window.translations = {
    'loading': '{{ __('participant.loading') }}',
    'more': '{{ __('participant.more') }}',
    'no_symptom_data_recorded': '{{ __('participant.no_symptom_data_recorded') }}',
    'previous': '{{ __('participant.previous') }}',
    'next': '{{ __('participant.next') }}',
    'select_date': '{{ __('participant.select_date') }}',
    'back_to_current_month': '{{ __('participant.back_to_current_month') }}',
    'previous_month': '{{ __('participant.previous_month') }}',
    'next_month': '{{ __('participant.next_month') }}',
    'domains': '{{ __('participant.domains') }}',
    'reset': '{{ __('participant.reset') }}',
    'clear': '{{ __('participant.clear') }}'
};

// Tooltip translations for PBAC icons
window.tooltipTranslations = {
    // Main domain tooltips
    'tooltip_blood_loss': '{{ __('participant.tooltip_blood_loss') }}',
    'tooltip_pain': '{{ __('participant.tooltip_pain') }}',
    'tooltip_impact': '{{ __('participant.tooltip_impact') }}',
    'tooltip_general_health': '{{ __('participant.tooltip_general_health') }}',
    'tooltip_mood': '{{ __('participant.tooltip_mood') }}',
    'tooltip_stool_urine': '{{ __('participant.tooltip_stool_urine') }}',
    'tooltip_sleep': '{{ __('participant.tooltip_sleep') }}',
    'tooltip_diet': '{{ __('participant.tooltip_diet') }}',
    'tooltip_exercise': '{{ __('participant.tooltip_exercise') }}',
    'tooltip_sexual_activity': '{{ __('participant.tooltip_sexual_activity') }}',
    'tooltip_note': '{{ __('participant.tooltip_note') }}',

    // Blood Loss tooltips
    'tooltip_amount': '{{ __('participant.tooltip_amount') }}',
    'tooltip_spotting_detected': '{{ __('participant.tooltip_spotting_detected') }}',

    // Pain region tooltips
    'tooltip_pain_level': '{{ __('participant.tooltip_pain_level') }}',
    'tooltip_regions': '{{ __('participant.tooltip_regions') }}',
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

    // Impact tooltips
    'tooltip_grade_your_day': '{{ __('participant.tooltip_grade_your_day') }}',

    // General Health tooltips
    'tooltip_energy_level': '{{ __('participant.tooltip_energy_level') }}',
    'tooltip_symptoms': '{{ __('participant.tooltip_symptoms') }}',
    'tooltip_no_symptoms': '{{ __('participant.tooltip_no_symptoms') }}',
    'tooltip_symptom_dizzy': '{{ __('participant.tooltip_symptom_dizzy') }}',
    'tooltip_symptom_nauseous': '{{ __('participant.tooltip_symptom_nauseous') }}',
    'tooltip_symptom_headache_migraine': '{{ __('participant.tooltip_symptom_headache_migraine') }}',
    'tooltip_symptom_bloated': '{{ __('participant.tooltip_symptom_bloated') }}',
    'tooltip_symptom_painful_sensitive_breasts': '{{ __('participant.tooltip_symptom_painful_sensitive_breasts') }}',
    'tooltip_symptom_acne': '{{ __('participant.tooltip_symptom_acne') }}',
    'tooltip_symptom_muscle_joint_pain': '{{ __('participant.tooltip_symptom_muscle_joint_pain') }}',

    // Stool/Urine tooltips
    'stool_urine_blood': '{{ __('participant.stool_urine_blood') }}',
    'stool_urine_hard': '{{ __('participant.stool_urine_hard') }}',
    'stool_urine_normal': '{{ __('participant.stool_urine_normal') }}',
    'stool_urine_soft': '{{ __('participant.stool_urine_soft') }}',
    'stool_urine_watery': '{{ __('participant.stool_urine_watery') }}',
    'stool_urine_something_else': '{{ __('participant.stool_urine_something_else') }}',
    'stool_urine_no_stool': '{{ __('participant.stool_urine_no_stool') }}',
    'stool_urine_blood_detected': '{{ __('participant.stool_urine_blood_detected') }}',
    'stool_urine_both': '{{ __('participant.stool_urine_both') }}',
    'stool_urine_in_urine': '{{ __('participant.stool_urine_in_urine') }}',
    'stool_urine_in_stool': '{{ __('participant.stool_urine_in_stool') }}',

    // Exercise tooltips
    'exercise_duration_less_thirty': '{{ __('participant.exercise_duration_less_thirty') }}',
    'exercise_duration_thirty_to_sixty': '{{ __('participant.exercise_duration_thirty_to_sixty') }}',
    'exercise_duration_greater_sixty': '{{ __('participant.exercise_duration_greater_sixty') }}',

    // Diet tooltips
    'tooltip_items_consumed': '{{ __('participant.tooltip_items_consumed') }}',
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

    // Sexual Health tooltips
    'tooltip_sexual_activity_recorded': '{{ __('participant.tooltip_sexual_activity_recorded') }}',
    'tooltip_no_sexual_activity_recorded': '{{ __('participant.tooltip_no_sexual_activity_recorded') }}',
    'sex_avoided': '{{ __('participant.sex_avoided') }}',
    'sex_satisfied': '{{ __('participant.sex_satisfied') }}',
    'sex_with_issues': '{{ __('participant.sex_with_issues') }}',

    // Sleep tooltips
    'tooltip_sleep_quality': '{{ __('participant.tooltip_sleep_quality') }}',
    'tooltip_sleep_duration': '{{ __('participant.tooltip_sleep_duration') }}',
    'tooltip_sleep_issues': '{{ __('participant.tooltip_sleep_issues') }}',
    'tooltip_no_sleep_issues': '{{ __('participant.tooltip_no_sleep_issues') }}',
    'sleep_quality_good': '{{ __('participant.sleep_quality_good') }}',
    'sleep_quality_okay': '{{ __('participant.sleep_quality_okay') }}',
    'sleep_quality_poor': '{{ __('participant.sleep_quality_poor') }}',
    'sleep_issue_trouble_asleep': '{{ __('participant.sleep_issue_trouble_asleep') }}',
    'sleep_issue_wake_up': '{{ __('participant.sleep_issue_wake_up') }}',
    'sleep_issue_not_tired_rested': '{{ __('participant.sleep_issue_not_tired_rested') }}',

    // Notes tooltips
    'tooltip_note_recorded': '{{ __('participant.tooltip_note_recorded') }}'
};
</script>
@endpush
