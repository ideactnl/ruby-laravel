/**
 * Pillar Constants
 * Shared constants and mappings used across cards, modals, and other pillar-related components
 */

import { getTooltipTranslation, getCardStatusTranslation } from '../../utils/translations.js';

export const BLOOD_LOSS_SEVERITY_LEVELS = ['very_light', 'light', 'moderate', 'heavy', 'very_heavy'];

export const BLOOD_LOSS_SEVERITY_LABELS = {
  'very_light': () => getCardStatusTranslation('card_blood_loss_very_light'),
  'light': () => getCardStatusTranslation('card_blood_loss_light'), 
  'moderate': () => getCardStatusTranslation('card_blood_loss_moderate'),
  'heavy': () => getCardStatusTranslation('card_blood_loss_heavy'),
  'very_heavy': () => getCardStatusTranslation('card_blood_loss_very_heavy'),
  'none': () => getCardStatusTranslation('card_blood_loss_no_data')
};

export const PAIN_REGION_LABELS = {
  'umbilical': () => getTooltipTranslation('tooltip_region_umbilical'),
  'left_umbilical': () => getTooltipTranslation('tooltip_region_left_umbilical'),
  'right_umbilical': () => getTooltipTranslation('tooltip_region_right_umbilical'),
  'bladder': () => getTooltipTranslation('tooltip_region_bladder'),
  'left_groin': () => getTooltipTranslation('tooltip_region_left_groin'),
  'right_groin': () => getTooltipTranslation('tooltip_region_right_groin'),
  'left_leg': () => getTooltipTranslation('tooltip_region_left_leg'),
  'right_leg': () => getTooltipTranslation('tooltip_region_right_leg'),
  'upper_back': () => getTooltipTranslation('tooltip_region_upper_back'),
  'back': () => getTooltipTranslation('tooltip_region_back'),
  'left_buttock': () => getTooltipTranslation('tooltip_region_left_buttock'),
  'right_buttock': () => getTooltipTranslation('tooltip_region_right_buttock'),
  'left_back_leg': () => getTooltipTranslation('tooltip_region_left_back_leg'),
  'right_back_leg': () => getTooltipTranslation('tooltip_region_right_back_leg'),
  'headache_migraine': () => getTooltipTranslation('tooltip_region_headache_migraine'),
  'pain_during_peeing': () => getTooltipTranslation('tooltip_region_pain_during_peeing'),
  'pain_during_pooping': () => getTooltipTranslation('tooltip_region_pain_during_pooping'),
  'pain_during_sex': () => getTooltipTranslation('tooltip_region_pain_during_sex')
};

export const IMPACT_LIMITATION_TYPES = [
  'used_medication', 'missed_work', 'missed_school', 'could_not_sport',
  'missed_social_activities', 'missed_leisure_activities', 'had_to_sit_more',
  'had_to_lie_down', 'had_to_stay_longer_in_bed', 'could_not_do_unpaid_work', 'other'
];

export const IMPACT_LIMITATION_LABELS = {
  'used_medication': () => getTooltipTranslation('tooltip_limitation_used_medication'),
  'missed_work': () => getTooltipTranslation('tooltip_limitation_missed_work'),
  'missed_school': () => getTooltipTranslation('tooltip_limitation_missed_school'),
  'could_not_sport': () => getTooltipTranslation('tooltip_limitation_could_not_sport'),
  'missed_social_activities': () => getTooltipTranslation('tooltip_limitation_missed_social_activities'),
  'missed_leisure_activities': () => getTooltipTranslation('tooltip_limitation_missed_leisure_activities'),
  'had_to_sit_more': () => getTooltipTranslation('tooltip_limitation_had_to_sit_more'),
  'had_to_lie_down': () => getTooltipTranslation('tooltip_limitation_had_to_lie_down'),
  'had_to_stay_longer_in_bed': () => getTooltipTranslation('tooltip_limitation_had_to_stay_longer_in_bed'),
  'could_not_do_unpaid_work': () => getTooltipTranslation('tooltip_limitation_could_not_do_unpaid_work'),
  'other': () => getTooltipTranslation('tooltip_limitation_other')
};

export const ENERGY_LEVEL_LABELS = {
  [-2]: () => getCardStatusTranslation('card_general_health_energy_very_low'),
  [-1]: () => getCardStatusTranslation('card_general_health_energy_low'),
  [0]: () => getCardStatusTranslation('card_general_health_energy_moderate'),
  [1]: () => getCardStatusTranslation('card_general_health_energy_good'),
  [2]: () => getCardStatusTranslation('card_general_health_energy_high')
};

export const SYMPTOM_LABELS = {
  'fatigue': () => getTooltipTranslation('tooltip_symptom_fatigue'),
  'headache': () => getTooltipTranslation('tooltip_symptom_headache'),
  'nausea': () => getTooltipTranslation('tooltip_symptom_nauseous'),
  'nauseous': () => getTooltipTranslation('tooltip_symptom_nauseous'),
  'dizziness': () => getTooltipTranslation('tooltip_symptom_dizzy'),
  'dizzy': () => getTooltipTranslation('tooltip_symptom_dizzy'),
  'weakness': () => getTooltipTranslation('tooltip_symptom_weakness'),
  'joint_pain': () => getTooltipTranslation('tooltip_symptom_joint_pain'),
  'muscle_pain': () => getTooltipTranslation('tooltip_symptom_muscle_pain'),
  'fever': () => getTooltipTranslation('tooltip_symptom_fever'),
  'chills': () => getTooltipTranslation('tooltip_symptom_chills'),
  'sweating': () => getTooltipTranslation('tooltip_symptom_sweating'),
  'bloated': () => getTooltipTranslation('tooltip_symptom_bloated'),
  'painful_sensitive_breasts': () => getTooltipTranslation('tooltip_symptom_painful_sensitive_breasts'),
  'acne': () => getTooltipTranslation('tooltip_symptom_acne'),
  'muscle_joint_pain': () => getTooltipTranslation('tooltip_symptom_muscle_joint_pain'),
  'headache_migraine': () => getTooltipTranslation('tooltip_symptom_headache_migraine')
};

export const SYMPTOM_ICON_MAP = {
  'dizzy': 'general_health_1.png',
  'nauseous': 'general_health_2.png',
  'headache_migraine': 'general_health_3.png',
  'bloated': 'general_health_4.png',
  'painful_sensitive_breasts': 'general_health_5.png',
  'acne': 'general_health_6.png',
  'muscle_joint_pain': 'general_health_7.png'
};

export const SYMPTOM_KEYS = [
  'dizzy', 'nauseous', 'headache_migraine', 'bloated', 
  'painful_sensitive_breasts', 'acne', 'muscle_joint_pain'
];

export const MOOD_ICON_MAP = {
  'calm': 'mood_1.png',
  'happy': 'mood_2.png',
  'excited': 'mood_3.png',
  'anxious': 'mood_4.png',
  'stressed': 'mood_4.png',
  'ashamed': 'mood_5.png',
  'angry': 'mood_6.png',
  'irritable': 'mood_6.png',
  'sad': 'mood_7.png',
  'mood_swings': 'mood_8.png',
  'worthless': 'mood_9.png',
  'guilty': 'mood_9.png',
  'overwhelmed': 'mood_10.png',
  'hopeless': 'mood_11.png',
  'depressed': 'mood_12.png',
  'hopes': 'mood_2.png',
  'mood_hopes': 'mood_2.png'
};

export const MOOD_KEYS = ['calm', 'happy', 'excited', 'anxious', 'ashamed', 'angry', 'sad', 'mood_swings', 'worthless', 'overwhelmed', 'hopeless', 'depressed', 'hopes', 'mood_hopes'];

export const MOOD_LABELS = {
  'calm': () => getTooltipTranslation('mood_calm'),
  'happy': () => getTooltipTranslation('mood_happy'),
  'excited': () => getTooltipTranslation('mood_excited'),
  'anxious': () => getTooltipTranslation('mood_anxious'),
  'stressed': () => getTooltipTranslation('mood_stressed'),
  'ashamed': () => getTooltipTranslation('mood_ashamed'),
  'angry': () => getTooltipTranslation('mood_angry'),
  'irritable': () => getTooltipTranslation('mood_irritable'),
  'sensitive': () => getTooltipTranslation('mood_sensitive'),
  'mood_swings': () => getTooltipTranslation('mood_mood_swings'),
  'worthless': () => getTooltipTranslation('mood_worthless'),
  'guilty': () => getTooltipTranslation('mood_guilty'),
  'overwhelmed': () => getTooltipTranslation('mood_overwhelmed'),
  'hopeless': () => getTooltipTranslation('mood_hopeless'),
  'depressed': () => getTooltipTranslation('mood_depressed'),
  'sad': () => getTooltipTranslation('mood_depressed'),
  'anxious_stressed': () => getTooltipTranslation('mood_anxious'),
  'angry_irritable': () => getTooltipTranslation('mood_angry'),
  'worthless_guilty': () => getTooltipTranslation('mood_worthless'),
  'hopes': () => getTooltipTranslation('tooltip_mood_hopes'),
  'mood_hopes': () => getTooltipTranslation('tooltip_mood_hopes'),
  'depressed_sad_down': () => getTooltipTranslation('mood_depressed')
};

export const STOOL_CONSISTENCY_MAP = {
  'hard': { color: 'bg-orange-400', text: () => getTooltipTranslation('stool_urine_hard'), icon: '/images/urine_stool_1.png' },
  'normal': { color: 'bg-green-400', text: () => getTooltipTranslation('stool_urine_normal'), icon: '/images/urine_stool_2.png' },
  'soft': { color: 'bg-yellow-400', text: () => getTooltipTranslation('stool_urine_soft'), icon: '/images/urine_stool_3.png' },
  'watery': { color: 'bg-blue-400', text: () => getTooltipTranslation('stool_urine_watery'), icon: '/images/urine_stool_4.png' },
  'something_else': { color: 'bg-purple-400', text: () => getTooltipTranslation('stool_urine_something_else'), icon: '/images/urine_stool_5.png' },
  'no_stool': { color: 'bg-gray-400', text: () => getTooltipTranslation('stool_urine_no_stool'), icon: '/images/urine_stool_6.png' }
};

export const STOOL_URINE_LABELS = {
  'normal': () => getTooltipTranslation('stool_urine_normal'),
  'hard': () => getTooltipTranslation('stool_urine_hard'),
  'soft': () => getTooltipTranslation('stool_urine_soft'),
  'watery': () => getTooltipTranslation('stool_urine_watery'),
  'something_else': () => getTooltipTranslation('stool_urine_something_else'),
  'no_stool': () => getTooltipTranslation('stool_urine_no_stool'),
  'blood': () => getTooltipTranslation('stool_urine_blood')
};

export const DIET_ICON_MAP = {
  'vegetables': 'diet_1.png',
  'fruit': 'diet_2.png',
  'potato_rice_bread': 'diet_3.png',
  'dairy_products': 'diet_4.png',
  'nuts_tofu_tempe': 'diet_5.png',
  'egg': 'diet_6.png',
  'fish': 'diet_7.png',
  'meat': 'diet_8.png',
  'snacks': 'diet.png',
  'water': 'diet_10.png',
  'coffee': 'diet_11.png',
  'soda': 'diet_9.png',
  'alcohol': 'diet_12.png'
};

export const DIET_KEYS = ['vegetables', 'fruit', 'potato_rice_bread', 'dairy_products', 'nuts_tofu_tempe', 'egg', 'fish', 'meat', 'snacks', 'water', 'coffee', 'soda', 'alcohol'];

export const DIET_LABELS = {
  'vegetables': () => getTooltipTranslation('diet_vegetables'),
  'fruit': () => getTooltipTranslation('diet_fruit'),
  'potato_rice_bread': () => getTooltipTranslation('diet_potato_rice_bread'),
  'dairy_products': () => getTooltipTranslation('diet_dairy_products'),
  'nuts_tofu_tempe': () => getTooltipTranslation('diet_nuts_tofu_tempe'),
  'egg': () => getTooltipTranslation('diet_egg'),
  'fish': () => getTooltipTranslation('diet_fish'),
  'meat': () => getTooltipTranslation('diet_meat'),
  'snacks': () => getTooltipTranslation('diet_snacks'),
  'water': () => getTooltipTranslation('diet_water'),
  'coffee': () => getTooltipTranslation('diet_coffee'),
  'soda': () => getTooltipTranslation('diet_soda'),
  'alcohol': () => getTooltipTranslation('diet_alcohol')
};

export const EXERCISE_TYPE_LABELS = {
  'high_impact': () => getTooltipTranslation('exercise_high_impact'),
  'low_impact': () => getTooltipTranslation('exercise_low_impact'),
  'relaxation_exercise': () => getTooltipTranslation('exercise_relaxation_exercise'),
  'cardio': () => getTooltipTranslation('exercise_cardio'),
  'strength': () => getTooltipTranslation('exercise_strength'),
  'flexibility': () => getTooltipTranslation('exercise_flexibility'),
  'yoga': () => getTooltipTranslation('exercise_yoga'),
  'walking': () => getTooltipTranslation('exercise_walking'),
  'running': () => getTooltipTranslation('exercise_running'),
  'cycling': () => getTooltipTranslation('exercise_cycling')
};

export const EXERCISE_LEVELS = ['less_thirty', 'thirty_to_sixty', 'greater_sixty'];

export const EXERCISE_DURATION_LABELS = {
  'less_thirty': () => getTooltipTranslation('exercise_duration_less_thirty'),
  'thirty_to_sixty': () => getTooltipTranslation('exercise_duration_thirty_to_sixty'), 
  'greater_sixty': () => getTooltipTranslation('exercise_duration_greater_sixty')
};

export const SLEEP_QUALITY_LABELS = {
  'good': () => getTooltipTranslation('sleep_quality_good'),
  'okay': () => getTooltipTranslation('sleep_quality_okay'),
  'poor': () => getTooltipTranslation('sleep_quality_poor')
};

export const SLEEP_ISSUE_LABELS = {
  'trouble_asleep': () => getTooltipTranslation('sleep_issue_trouble_asleep'),
  'wake_up_during_night': () => getTooltipTranslation('sleep_issue_wake_up_during_night'),
  'not_tired_rested': () => getTooltipTranslation('sleep_issue_not_tired_rested')
};

export const SEX_ISSUE_LABELS = {
  'pain': () => getTooltipTranslation('sex_issue_pain'),
  'discomfort': () => getTooltipTranslation('sex_issue_discomfort'),
  'bleeding': () => getTooltipTranslation('sex_issue_bleeding'),
  'dryness': () => getTooltipTranslation('sex_issue_dryness'),
  'fatigue': () => getTooltipTranslation('sex_issue_fatigue'),
  'discomfort_pelvic_area': () => getTooltipTranslation('sex_issue_discomfort_pelvic_area')
};

export const SEX_STATUS_LABELS = {
  'satisfied': () => getTooltipTranslation('sex_status_satisfied'),
  'unsatisfied': () => getTooltipTranslation('sex_status_unsatisfied'),
  'avoided': () => getTooltipTranslation('sex_status_avoided'),
  'no_activity': () => getTooltipTranslation('sex_status_no_activity')
};
