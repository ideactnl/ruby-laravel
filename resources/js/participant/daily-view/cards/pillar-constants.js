/**
 * Pillar Constants
 * Shared constants and mappings used across cards, modals, and other pillar-related components
 */

// Blood Loss Constants
export const BLOOD_LOSS_SEVERITY_LEVELS = ['very_light', 'light', 'moderate', 'heavy', 'very_heavy'];

export const BLOOD_LOSS_SEVERITY_LABELS = {
  'very_light': 'Very Light',
  'light': 'Light', 
  'moderate': 'Moderate',
  'heavy': 'Heavy',
  'very_heavy': 'Very Heavy',
  'none': 'None'
};

// Pain Constants
export const PAIN_REGION_LABELS = {
  'left_umbilical': 'Left Umbilical',
  'right_umbilical': 'Right Umbilical', 
  'left_iliac': 'Left Iliac',
  'right_iliac': 'Right Iliac',
  'hypogastric': 'Hypogastric',
  'epigastric': 'Epigastric',
  'left_hypochondriac': 'Left Hypochondriac',
  'right_hypochondriac': 'Right Hypochondriac',
  'umbilical': 'Umbilical',
  'back': 'Back',
  'pelvis': 'Pelvis',
  'legs': 'Legs'
};

// Impact Constants
export const IMPACT_LIMITATION_TYPES = [
  'used_medication', 'missed_work', 'missed_school', 'could_not_sport',
  'missed_social_activities', 'missed_leisure_activities', 'had_to_sit_more',
  'had_to_lie_down', 'had_to_stay_longer_in_bed', 'could_not_do_unpaid_work', 'other'
];

export const IMPACT_LIMITATION_LABELS = {
  'used_medication': 'Used Medication',
  'missed_work': 'Missed Work',
  'missed_school': 'Missed School',
  'could_not_sport': 'Could Not Exercise',
  'missed_social_activities': 'Missed Social Activities',
  'missed_leisure_activities': 'Missed Leisure Activities',
  'had_to_sit_more': 'Had to Sit More',
  'had_to_lie_down': 'Had to Lie Down',
  'had_to_stay_longer_in_bed': 'Stayed Longer in Bed',
  'could_not_do_unpaid_work': 'Could Not Do Unpaid Work',
  'other': 'Other Limitations'
};

// General Health/Energy Constants
export const ENERGY_LEVEL_LABELS = {
  1: 'Very Low',
  2: 'Low', 
  3: 'Moderate',
  4: 'Good',
  5: 'High'
};

export const SYMPTOM_LABELS = {
  'fatigue': 'Fatigue',
  'headache': 'Headache',
  'nausea': 'Nausea',
  'nauseous': 'Nauseous',
  'dizziness': 'Dizziness',
  'dizzy': 'Dizzy',
  'weakness': 'Weakness',
  'joint_pain': 'Joint Pain',
  'muscle_pain': 'Muscle Pain',
  'fever': 'Fever',
  'chills': 'Chills',
  'sweating': 'Sweating',
  'bloated': 'Bloated',
  'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
  'acne': 'Acne',
  'muscle_joint_pain': 'Muscle/Joint Pain',
  'headache_migraine': 'Headache/Migraine'
};

// Mood Constants
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
  'depressed': 'mood_12.png'
};

export const MOOD_KEYS = ['calm', 'happy', 'excited', 'anxious', 'ashamed', 'angry', 'sad', 'mood_swings', 'worthless', 'overwhelmed', 'hopeless', 'depressed'];

// Stool/Urine Constants
export const STOOL_CONSISTENCY_MAP = {
  'hard': { color: 'bg-orange-400', text: 'Hard stool', icon: '/images/urine_stool_1.png' },
  'normal': { color: 'bg-green-400', text: 'Normal', icon: '/images/urine_stool_2.png' },
  'soft': { color: 'bg-yellow-400', text: 'Soft stool', icon: '/images/urine_stool_3.png' },
  'watery': { color: 'bg-blue-400', text: 'Watery', icon: '/images/urine_stool_4.png' },
  'something_else': { color: 'bg-purple-400', text: 'Other', icon: '/images/urine_stool_5.png' },
  'no_stool': { color: 'bg-gray-400', text: 'No stool', icon: '/images/urine_stool_6.png' }
};

// Diet Constants
export const DIET_ICON_MAP = {
  'vegetables': 'diet_1.png',
  'fruit': 'diet_2.png',
  'potato_rice_bread': 'diet_3.png',
  'dairy_products': 'diet_4.png',
  'nuts_tofu_tempe': 'diet_5.png',
  'egg': 'diet_6.png',
  'fish': 'diet_7.png',
  'meat': 'diet_8.png',
  'snacks': 'diet_9.png',
  'water': 'diet_10.png',
  'coffee': 'diet_11.png',
  'alcohol': 'diet_12.png'
};

export const DIET_KEYS = ['vegetables', 'fruit', 'potato_rice_bread', 'dairy_products', 'nuts_tofu_tempe', 'egg', 'fish', 'meat', 'snacks', 'water', 'coffee', 'alcohol'];

// Exercise Constants
export const EXERCISE_TYPE_LABELS = {
  'high_impact': 'High Impact',
  'precision_exercise': 'Precision Exercise',
  'low_impact': 'Low Impact',
  'cardio': 'Cardio',
  'strength': 'Strength Training',
  'flexibility': 'Flexibility',
  'yoga': 'Yoga',
  'walking': 'Walking',
  'running': 'Running',
  'swimming': 'Swimming',
  'cycling': 'Cycling'
};

export const EXERCISE_LEVELS = ['less_thirty', 'thirty_to_sixty', 'greater_sixty', 'high_impact'];

export const EXERCISE_DURATION_LABELS = {
  'less_thirty': 'Less than 30 minutes',
  'thirty_to_sixty': '30-60 minutes', 
  'greater_sixty': 'More than 60 minutes'
};

// Sleep Constants
export const SLEEP_QUALITY_LABELS = {
  'good': 'Good Sleep',
  'okay': 'Okay Sleep',
  'poor': 'Poor Sleep'
};

export const SLEEP_ISSUE_LABELS = {
  'trouble_asleep': 'Trouble falling asleep',
  'wake_up_during_night': 'Woke up during night',
  'not_tired_rested': 'Not well rested'
};

// Sexual Health Constants
export const SEX_ISSUE_LABELS = {
  'pain': 'Pain during activity',
  'discomfort': 'Discomfort',
  'bleeding': 'Bleeding',
  'dryness': 'Dryness',
  'fatigue': 'Fatigue affecting activity'
};

export const SEX_STATUS_LABELS = {
  'satisfied': 'Satisfying Experience',
  'unsatisfied': 'Unsatisfying Experience',
  'avoided': 'Activity Avoided',
  'no_activity': 'No Activity'
};
