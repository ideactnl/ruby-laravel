import { getPbacIcon } from '../pbac-icon-mapping.js';

/**
 * Generate dynamic icon source and tooltip based on pillar type and data
 * @param {string} type - The pillar type (blood_loss, pain, etc.)
 * @param {*} value - The pillar data (object or primitive)
 * @returns {Object} { iconSrc, tooltip }
 */
export function getDynamicIconAndTooltip(type, value) {
  try {
    const iconData = getPbacIcon(type, value);
    
    return {
      iconSrc: iconData.src,
      tooltip: iconData.label
    };
  } catch (error) {
    console.warn(`Error getting icon for ${type}:`, error);
    
    const gridIcons = {
      blood_loss: 'grid_blood_loss.png',
      pain: 'grid_pain.png',
      impact:'grid_impact_new.png',
      general_health: 'grid_general_health.png',
      mood: 'grid_mood.png',
      stool_urine: 'grid_urine_stool.png',
      sleep: 'grid_sleep.png',
      exercise: 'grid_sport.png',
      diet: 'grid_diet.png',
      sex: 'grid_sex.png',
      notes: 'grid_notes.png'
    };
    
    const fallbackIcon = gridIcons[type] || 'question.png';
    return { 
      iconSrc: `/images/${fallbackIcon}`,
      tooltip: `${type}: ${JSON.stringify(value)}`
    };
  }
}