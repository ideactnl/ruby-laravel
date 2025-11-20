/**
 * Card Status Generators
 * Centralized status color, text, and context generation for daily view cards
 * Each pillar type returns: { statusColor, statusText, context }
 */

import { STOOL_CONSISTENCY_MAP } from './pillar-constants.js';
import { getCardStatusTranslation } from '../../utils/translations.js';

export class CardStatusGenerators {
  
  /**
   * Blood Loss Status - Based on severity and spotting
   */
  static getBloodLossStatus(pillar) {
    const amount = pillar?.amount ?? 0;
    const severity = pillar?.severity || 'none';
    const spotting = pillar?.flags?.spotting;
    
    let statusColor = 'bg-gray-300';
    let statusText = getCardStatusTranslation('card_blood_loss_no_data');
    let context = '';
    
    if (amount > 0) {
      if (spotting) {
        statusColor = 'bg-orange-400';
        statusText = getCardStatusTranslation('card_blood_loss_spotting_detected');
        context = getCardStatusTranslation('card_blood_loss_spotting');
      } else if (severity === 'very_heavy') {
        statusColor = 'bg-red-600';
        statusText = getCardStatusTranslation('card_blood_loss_very_heavy');
        context = getCardStatusTranslation('card_blood_loss_very_heavy');
      } else if (severity === 'heavy') {
        statusColor = 'bg-red-500';
        statusText = getCardStatusTranslation('card_blood_loss_heavy');
        context = getCardStatusTranslation('card_blood_loss_heavy');
      } else if (severity === 'moderate') {
        statusColor = 'bg-yellow-500';
        statusText = getCardStatusTranslation('card_blood_loss_moderate');
        context = getCardStatusTranslation('card_blood_loss_moderate');
      } else {
        statusColor = 'bg-green-400';
        statusText = getCardStatusTranslation('card_blood_loss_light');
        context = getCardStatusTranslation('card_blood_loss_light');
      }
    }
    
    return { statusColor, statusText, context };
  }

  /**
   * Pain Status - Based on pain value (0-10)
   */
  static getPainStatus(pillar) {
    const value = pillar?.value ?? 0;
    
    let statusColor = 'bg-gray-300';
    let statusText = getCardStatusTranslation('card_pain_no_pain');
    let context = '';
    let progressColor = 'bg-gray-400';
    
    if (value > 0) {
      if (value >= 8) {
        statusColor = 'bg-red-600';
        statusText = getCardStatusTranslation('card_pain_severe_pain');
        context = getCardStatusTranslation('card_pain_severe');
        progressColor = 'bg-red-500';
      } else if (value >= 6) {
        statusColor = 'bg-orange-500';
        statusText = getCardStatusTranslation('card_pain_moderate_pain');
        context = getCardStatusTranslation('card_pain_moderate');
        progressColor = 'bg-orange-400';
      } else if (value >= 4) {
        statusColor = 'bg-yellow-500';
        statusText = getCardStatusTranslation('card_pain_mild_pain');
        context = getCardStatusTranslation('card_pain_mild');
        progressColor = 'bg-yellow-400';
      } else {
        statusColor = 'bg-green-400';
        statusText = getCardStatusTranslation('card_pain_light_pain');
        context = getCardStatusTranslation('card_pain_light');
        progressColor = 'bg-green-300';
      }
    }
    
    return { statusColor, statusText, context, progressColor };
  }

  /**
   * Impact Status - Based on grade your day (1-10)
   */
  static getImpactStatus(pillar) {
    const grade = pillar?.gradeYourDay ?? null;
    
    let statusColor = 'bg-gray-300';
    let statusText = getCardStatusTranslation('card_impact_no_impact');
    let context = '';
    
    if (grade !== null) {
      if (grade >= 8) {
        statusColor = 'bg-green-500';
        statusText = getCardStatusTranslation('card_impact_great_day');
        context = getCardStatusTranslation('card_impact_great');
      } else if (grade >= 6) {
        statusColor = 'bg-yellow-400';
        statusText = getCardStatusTranslation('card_impact_good_day');
        context = getCardStatusTranslation('card_impact_good');
      } else if (grade >= 4) {
        statusColor = 'bg-orange-400';
        statusText = getCardStatusTranslation('card_impact_challenging_day');
        context = getCardStatusTranslation('card_impact_challenging');
      } else {
        statusColor = 'bg-red-500';
        statusText = getCardStatusTranslation('card_impact_difficult_day');
        context = getCardStatusTranslation('card_impact_difficult');
      }
    }
    
    return { statusColor, statusText, context };
  }

  /**
   * General Health Status - Based on energy level and symptoms
   */
  static getGeneralHealthStatus(pillar) {
    const energy = pillar?.energyLevel;
    const symptoms = pillar?.symptoms || [];
    
    let statusColor = 'bg-gray-300';
    let statusText = getCardStatusTranslation('card_general_health_no_energy_data');
    let context = '';
    
    if (energy !== null && energy !== undefined) {
      const eRaw = Number(energy);
      const e = (eRaw === -3) ? 0 : eRaw;
      const energyLevelMap = {
        [-2]: 'card_general_health_energy_very_low',
        [-1]: 'card_general_health_energy_low',
        [0]: 'card_general_health_energy_moderate',
        [1]: 'card_general_health_energy_good',
        [2]: 'card_general_health_energy_high'
      };
      context = getCardStatusTranslation(energyLevelMap[e] || 'card_general_health_unknown');

      if (e >= 2) {
        statusColor = symptoms.length > 0 ? 'bg-yellow-500' : 'bg-green-500';
        statusText = symptoms.length > 0 ? getCardStatusTranslation('card_general_health_high_energy_with_symptoms') : getCardStatusTranslation('card_general_health_high_energy');
      } else if (e === 1) {
        statusColor = symptoms.length > 0 ? 'bg-yellow-500' : 'bg-green-500';
        statusText = symptoms.length > 0 ? getCardStatusTranslation('card_general_health_good_energy_with_symptoms') : getCardStatusTranslation('card_general_health_good_energy');
      } else if (e === 0) {
        statusColor = 'bg-yellow-400';
        statusText = getCardStatusTranslation('card_general_health_moderate_energy');
      } else if (e === -1) {
        statusColor = 'bg-orange-400';
        statusText = getCardStatusTranslation('card_general_health_low_energy');
      } else {
        statusColor = 'bg-red-400';
        statusText = getCardStatusTranslation('card_general_health_very_low_energy');
      }
    }
    
    return { statusColor, statusText, context };
  }

  /**
   * Mood Status - Based on positive/negative mood balance
   */
  static getMoodStatus(pillar) {
    const positives = pillar?.positives || [];
    const negatives = pillar?.negatives || [];
    
    const balance = positives.length - negatives.length;
    let statusColor = 'bg-gray-300';
    let statusText = getCardStatusTranslation('card_mood_balanced');
    let context = getCardStatusTranslation('card_mood_balanced');
    
    if (balance > 1) {
      statusColor = 'bg-green-500';
      statusText = getCardStatusTranslation('card_mood_positive_day');
      context = getCardStatusTranslation('card_mood_positive');
    } else if (balance < -1) {
      statusColor = 'bg-red-400';
      statusText = getCardStatusTranslation('card_mood_challenging_day');
      context = getCardStatusTranslation('card_mood_challenging');
    }
    
    return { statusColor, statusText, context };
  }

  /**
   * Stool/Urine Status - Based on blood detection and consistency
   */
  static getStoolUrineStatus(pillar) {
    const hasUrineBlood = pillar?.urine?.blood ?? false;
    const hasStoolBlood = pillar?.stool?.blood ?? false;
    const bloodInStool = hasUrineBlood || hasStoolBlood;
    const consistency = pillar?.stool?.consistency;
    
    let statusColor = 'bg-gray-300';
    let statusText = getCardStatusTranslation('card_stool_urine_no_data');
    let context = getCardStatusTranslation('card_stool_urine_recorded');
    
    if (bloodInStool) {
      statusColor = 'bg-red-500';
      statusText = getCardStatusTranslation('card_stool_urine_blood_detected');
      if (hasUrineBlood && hasStoolBlood) {
        context = getCardStatusTranslation('card_stool_urine_blood_in_urine_and_stool');
      } else if (hasUrineBlood) {
        context = getCardStatusTranslation('card_stool_urine_blood_in_urine');
      } else {
        context = getCardStatusTranslation('card_stool_urine_blood_in_stool');
      }
    } else if (consistency && STOOL_CONSISTENCY_MAP[consistency]) {
      statusColor = STOOL_CONSISTENCY_MAP[consistency].color;
      statusText = STOOL_CONSISTENCY_MAP[consistency].text;
      context = STOOL_CONSISTENCY_MAP[consistency].text;
    }
    
    return { statusColor, statusText, context };
  }

  /**
   * Sleep Status - Simple status for sleep tracking
   */
  static getSleepStatus(pillar) {
    const hours = pillar?.calculatedHours ?? 0;
    
    return {
      statusColor: 'bg-indigo-400',
      statusText: getCardStatusTranslation('card_sleep_sleep_tracked'),
      context: getCardStatusTranslation('card_sleep_hours_template').replace('{hours}', hours)
    };
  }

  /**
   * Diet Status - Simple status for diet tracking
   */
  static getDietStatus(pillar) {
    return {
      statusColor: 'bg-green-400',
      statusText: getCardStatusTranslation('card_diet_items_recorded'),
      context: getCardStatusTranslation('card_diet_diet_tracked')
    };
  }

  /**
   * Exercise Status - Based on duration levels
   */
  static getExerciseStatus(pillar) {
    const levels = pillar?.levels || [];
    
    let context = getCardStatusTranslation('card_exercise_exercise_completed');
    if (levels.includes('greater_sixty')) {
      context = getCardStatusTranslation('card_exercise_more_than_60');
    } else if (levels.includes('thirty_to_sixty')) {
      context = getCardStatusTranslation('card_exercise_thirty_to_60');
    } else if (levels.includes('less_thirty')) {
      context = getCardStatusTranslation('card_exercise_less_than_30');
    }
    
    return {
      statusColor: 'bg-orange-400',
      statusText: getCardStatusTranslation('card_exercise_exercise_completed'),
      context: context
    };
  }

  /**
   * Sex Status - Based on activity and satisfaction
   */
  static getSexStatus(pillar) {
    let context = getCardStatusTranslation('card_sex_activity_recorded');
    let statusColor = 'bg-pink-400';
    
    if (pillar?.avoided) {
      context = getCardStatusTranslation('card_sex_avoided');
      statusColor = 'bg-gray-400';
    } else if (pillar?.satisfied) {
      context = getCardStatusTranslation('card_sex_satisfied');
      statusColor = 'bg-green-400';
    }
    
    return {
      statusColor: statusColor,
      statusText: context,
      context: context
    };
  }

  /**
   * Notes Status - Simple status for notes
   */
  static getNotesStatus(pillar) {
    return {
      statusColor: 'bg-gray-500',
      statusText: getCardStatusTranslation('card_notes_note_available'),
      context: getCardStatusTranslation('card_notes_note_recorded')
    };
  }

  /**
   * Generic status generator - dynamically calls the appropriate method
   */
  static getPillarStatus(pillar, pillarType) {
    switch (pillarType) {
      case 'blood_loss': return this.getBloodLossStatus(pillar);
      case 'pain': return this.getPainStatus(pillar);
      case 'impact': return this.getImpactStatus(pillar);
      case 'general_health': return this.getGeneralHealthStatus(pillar);
      case 'mood': return this.getMoodStatus(pillar);
      case 'stool_urine': return this.getStoolUrineStatus(pillar);
      case 'sleep': return this.getSleepStatus(pillar);
      case 'diet': return this.getDietStatus(pillar);
      case 'exercise': return this.getExerciseStatus(pillar);
      case 'sex': return this.getSexStatus(pillar);
      case 'notes': return this.getNotesStatus(pillar);
      default: return { statusColor: 'bg-gray-300', statusText: getCardStatusTranslation('card_common_unknown'), context: '' };
    }
  }
}
