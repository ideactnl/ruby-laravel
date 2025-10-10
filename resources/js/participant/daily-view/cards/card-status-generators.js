/**
 * Card Status Generators
 * Centralized status color, text, and context generation for daily view cards
 * Each pillar type returns: { statusColor, statusText, context }
 */

import { STOOL_CONSISTENCY_MAP } from './pillar-constants.js';
import { cardStatusText } from '../data/index.js';

export class CardStatusGenerators {
  
  /**
   * Blood Loss Status - Based on severity and spotting
   */
  static getBloodLossStatus(pillar) {
    const amount = pillar?.amount ?? 0;
    const severity = pillar?.severity || 'none';
    const spotting = pillar?.flags?.spotting;
    
    let statusColor = 'bg-gray-300';
    let statusText = cardStatusText.bloodLoss.noData;
    let context = '';
    
    if (amount > 0) {
      if (spotting) {
        statusColor = 'bg-orange-400';
        statusText = cardStatusText.bloodLoss.spottingDetected;
        context = cardStatusText.bloodLoss.contexts.spotting;
      } else if (severity === 'very_heavy') {
        statusColor = 'bg-red-600';
        statusText = cardStatusText.bloodLoss.veryHeavy;
        context = cardStatusText.bloodLoss.contexts.veryHeavy;
      } else if (severity === 'heavy') {
        statusColor = 'bg-red-500';
        statusText = cardStatusText.bloodLoss.heavy;
        context = cardStatusText.bloodLoss.contexts.heavy;
      } else if (severity === 'moderate') {
        statusColor = 'bg-yellow-500';
        statusText = cardStatusText.bloodLoss.moderate;
        context = cardStatusText.bloodLoss.contexts.moderate;
      } else {
        statusColor = 'bg-green-400';
        statusText = cardStatusText.bloodLoss.light;
        context = cardStatusText.bloodLoss.contexts.light;
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
    let statusText = cardStatusText.pain.noPain;
    let context = '';
    let progressColor = 'bg-gray-400';
    
    if (value > 0) {
      if (value >= 8) {
        statusColor = 'bg-red-600';
        statusText = cardStatusText.pain.severePain;
        context = cardStatusText.pain.contexts.severe;
        progressColor = 'bg-red-500';
      } else if (value >= 6) {
        statusColor = 'bg-orange-500';
        statusText = cardStatusText.pain.moderatePain;
        context = cardStatusText.pain.contexts.moderate;
        progressColor = 'bg-orange-400';
      } else if (value >= 4) {
        statusColor = 'bg-yellow-500';
        statusText = cardStatusText.pain.mildPain;
        context = cardStatusText.pain.contexts.mild;
        progressColor = 'bg-yellow-400';
      } else {
        statusColor = 'bg-green-400';
        statusText = cardStatusText.pain.lightPain;
        context = cardStatusText.pain.contexts.light;
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
    let statusText = cardStatusText.impact.noImpact;
    let context = '';
    
    if (grade !== null) {
      if (grade >= 8) {
        statusColor = 'bg-green-500';
        statusText = cardStatusText.impact.greatDay;
        context = cardStatusText.impact.contexts.great;
      } else if (grade >= 6) {
        statusColor = 'bg-yellow-400';
        statusText = cardStatusText.impact.goodDay;
        context = cardStatusText.impact.contexts.good;
      } else if (grade >= 4) {
        statusColor = 'bg-orange-400';
        statusText = cardStatusText.impact.challengingDay;
        context = cardStatusText.impact.contexts.challenging;
      } else {
        statusColor = 'bg-red-500';
        statusText = cardStatusText.impact.difficultDay;
        context = cardStatusText.impact.contexts.difficult;
      }
    }
    
    return { statusColor, statusText, context };
  }

  /**
   * General Health Status - Based on energy level and symptoms
   */
  static getGeneralHealthStatus(pillar) {
    const energy = pillar?.energyLevel ?? 0;
    const symptoms = pillar?.symptoms || [];
    
    let statusColor = 'bg-gray-300';
    let statusText = cardStatusText.generalHealth.noEnergyData;
    let context = '';
    
    if (energy > 0) {
      context = cardStatusText.generalHealth.energyLevels[energy] || cardStatusText.generalHealth.unknown;
      
      if (energy >= 5) {
        statusColor = symptoms.length > 0 ? 'bg-yellow-500' : 'bg-green-500';
        statusText = symptoms.length > 0 ? cardStatusText.generalHealth.highEnergyWithSymptoms : cardStatusText.generalHealth.highEnergy;
      } else if (energy >= 4) {
        statusColor = symptoms.length > 0 ? 'bg-yellow-500' : 'bg-green-500';
        statusText = symptoms.length > 0 ? cardStatusText.generalHealth.goodEnergyWithSymptoms : cardStatusText.generalHealth.goodEnergy;
      } else if (energy >= 3) {
        statusColor = 'bg-yellow-400';
        statusText = cardStatusText.generalHealth.moderateEnergy;
      } else if (energy >= 2) {
        statusColor = 'bg-orange-400';
        statusText = cardStatusText.generalHealth.lowEnergy;
      } else {
        statusColor = 'bg-red-400';
        statusText = cardStatusText.generalHealth.veryLowEnergy;
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
    let statusText = cardStatusText.mood.balanced;
    let context = cardStatusText.mood.contexts.balanced;
    
    if (balance > 1) {
      statusColor = 'bg-green-500';
      statusText = cardStatusText.mood.positiveDay;
      context = cardStatusText.mood.contexts.positive;
    } else if (balance < -1) {
      statusColor = 'bg-red-400';
      statusText = cardStatusText.mood.challengingDay;
      context = cardStatusText.mood.contexts.challenging;
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
    let statusText = cardStatusText.stoolUrine.noData;
    let context = cardStatusText.stoolUrine.recorded;
    
    if (bloodInStool) {
      statusColor = 'bg-red-500';
      statusText = cardStatusText.stoolUrine.bloodDetected;
      if (hasUrineBlood && hasStoolBlood) {
        context = cardStatusText.stoolUrine.contexts.bloodInUrineAndStool;
      } else if (hasUrineBlood) {
        context = cardStatusText.stoolUrine.contexts.bloodInUrine;
      } else {
        context = cardStatusText.stoolUrine.contexts.bloodInStool;
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
      statusText: cardStatusText.sleep.sleepTracked,
      context: cardStatusText.sleep.hoursTemplate.replace('{hours}', hours)
    };
  }

  /**
   * Diet Status - Simple status for diet tracking
   */
  static getDietStatus(pillar) {
    return {
      statusColor: 'bg-green-400',
      statusText: cardStatusText.diet.itemsRecorded,
      context: cardStatusText.diet.dietTracked
    };
  }

  /**
   * Exercise Status - Based on duration levels
   */
  static getExerciseStatus(pillar) {
    const levels = pillar?.levels || [];
    
    let context = cardStatusText.exercise.exerciseCompleted;
    if (levels.includes('greater_sixty')) {
      context = cardStatusText.exercise.contexts.moreThan60;
    } else if (levels.includes('thirty_to_sixty')) {
      context = cardStatusText.exercise.contexts.thirtyTo60;
    } else if (levels.includes('less_thirty')) {
      context = cardStatusText.exercise.contexts.lessThan30;
    }
    
    return {
      statusColor: 'bg-orange-400',
      statusText: cardStatusText.exercise.exerciseCompleted,
      context: context
    };
  }

  /**
   * Sex Status - Based on activity and satisfaction
   */
  static getSexStatus(pillar) {
    let context = cardStatusText.sex.activityRecorded;
    let statusColor = 'bg-pink-400';
    
    if (pillar?.avoided) {
      context = cardStatusText.sex.avoided;
      statusColor = 'bg-gray-400';
    } else if (pillar?.satisfied) {
      context = cardStatusText.sex.satisfied;
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
      statusText: cardStatusText.notes.noteAvailable,
      context: cardStatusText.notes.noteRecorded
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
      default: return { statusColor: 'bg-gray-300', statusText: 'Unknown', context: '' };
    }
  }
}
