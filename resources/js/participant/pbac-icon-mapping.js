/**
 * PBAC Icon Mapping Configuration
 * 
 * Maps PBAC field values to custom icon images based on Excel specification.
 * Handles slider-to-discrete-icon mapping for continuous scales.
 * 
 */

export { getPbacIcon } from './pbac-icons/index.js';

export { 
  getBloodLossIcon as BLOOD_LOSS_MAPPING,
  getPainIcon as PAIN_MAPPING,
  getImpactIcon as IMPACT_MAPPING,
  getGeneralHealthIcon as GENERAL_HEALTH_MAPPING,
  getMoodIcon as MOOD_MAPPING,
  getStoolUrineIcon as STOOL_URINE_MAPPING,
  getExerciseIcon as EXERCISE_MAPPING,
  getDietIcon as DIET_MAPPING,
  getSexIcon as SEX_MAPPING,
  getSleepIcon as SLEEP_MAPPING,
  getNotesIcon as NOTES_MAPPING
} from './pbac-icons/index.js';
