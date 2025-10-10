/**
 * PBAC Icons Main Index
 * Centralized icon mapping system
 */

import { getBloodLossIcon } from './blood-loss.js';
import { getPainIcon } from './pain.js';
import { getImpactIcon } from './impact.js';
import { getGeneralHealthIcon } from './general-health.js';
import { getMoodIcon } from './mood.js';
import { 
  getStoolUrineIcon, 
  getExerciseIcon, 
  getDietIcon, 
  getSexIcon, 
  getSleepIcon, 
  getNotesIcon 
} from './other-mappings.js';
import { ICON_BASE_PATH } from './config.js';

/**
 * Main mapping function - determines which mapper to use
 */
export function getPbacIcon(type, value) {
  const mappers = {
    'blood_loss': getBloodLossIcon,
    'pain': getPainIcon,
    'impact': getImpactIcon,
    'general_health': getGeneralHealthIcon,
    'mood': getMoodIcon,
    'stool_urine': getStoolUrineIcon,
    'exercise': getExerciseIcon,
    'diet': getDietIcon,
    'sex': getSexIcon,
    'sleep': getSleepIcon,
    'notes': getNotesIcon
  };
  
  const mapper = mappers[type];
  if (mapper) {
    return mapper(value);
  }
  
  return {
    src: `${ICON_BASE_PATH}${type}.png`,
    label: `${type}: ${JSON.stringify(value)}`
  };
}

export {
  getBloodLossIcon,
  getPainIcon,
  getImpactIcon,
  getGeneralHealthIcon,
  getMoodIcon,
  getStoolUrineIcon,
  getExerciseIcon,
  getDietIcon,
  getSexIcon,
  getSleepIcon,
  getNotesIcon
};
