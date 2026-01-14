/**
 * Card Generators Module
 * Creates visual card objects for each PBAC pillar type
 */

import { CardIconGenerators } from './card-icon-generators.js';
import { PillarDataValidators } from './pillar-data-validators.js';
import { CardStatusGenerators } from './card-status-generators.js';
import { __ } from '../../utils/translations.js';
import { 
  PAIN_REGION_LABELS,
  IMPACT_LIMITATION_LABELS,
  SYMPTOM_LABELS,
  EXERCISE_TYPE_LABELS
} from './pillar-constants.js';

export class CardGenerators {
  constructor(component) {
    this.component = component;
  }

  /**
   * Generate all cards from pillars data
   */
  generateCards(pillars) {
    if (!pillars || typeof pillars !== 'object') {
      return [];
    }
    
    const allCards = [
      this.createBloodLossCard(pillars.blood_loss),
      this.createPainCard(pillars.pain),
      this.createImpactCard(pillars.impact),
      this.createGeneralHealthCard(pillars.general_health),
      this.createMoodCard(pillars.mood),
      this.createStoolCard(pillars.stool_urine),
      this.createSleepCard(pillars.sleep),
      this.createDietCard(pillars.diet),
      this.createExerciseCard(pillars.exercise),
      this.createSexCard(pillars.sex),
      this.createNotesCard(pillars.notes),
    ];
    
    return allCards.filter(card => card !== null);
  }

  createBloodLossCard(pillar) {
    if (!PillarDataValidators.hasBloodLossData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getBloodLossStatus(pillar);
    const iconData = CardIconGenerators.getBloodLossIcons(pillar);
    const translations = window.healthDomainTranslations || {};

    return {
      key: 'blood_loss',
      label: translations.blood_loss || __('blood_loss', 'Blood Loss'),
      iconSrc: '/images/grid_blood_loss.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getBloodLossTooltip(pillar),
      pillar: pillar
    };
  }

  createPainCard(pillar) {
    if (!PillarDataValidators.hasPainData(pillar)) {
      return null;
    }

    const regions = pillar.regions || [];
    
    const statusInfo = CardStatusGenerators.getPainStatus(pillar);
    const iconData = CardIconGenerators.getPainIcons(pillar);
    const translations = window.healthDomainTranslations || {};

    const formattedRegions = regions.map(region => PAIN_REGION_LABELS[region] ? PAIN_REGION_LABELS[region]() : region).join(', ');
    
    return {
      key: 'pain',
      label: translations.pain || __('pain', 'Pain'),
      iconSrc: '/images/grid_pain.png',
      context: statusInfo.context, 
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getPainTooltip(pillar),
      pillar: pillar
    };
  }

  createImpactCard(pillar) {
    if (!PillarDataValidators.hasImpactData(pillar)) {
      return null;
    }

    const limitations = pillar.limitations || [];
    
    const statusInfo = CardStatusGenerators.getImpactStatus(pillar);
    const iconData = CardIconGenerators.getImpactIcons(pillar);
    const translations = window.healthDomainTranslations || {};

    const formattedLimitations = limitations
      .filter(limitation => limitation && limitation !== '_' && limitation.trim() !== '')
      .map(limitation => IMPACT_LIMITATION_LABELS[limitation] ? IMPACT_LIMITATION_LABELS[limitation]() : limitation.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))
      .join(', ');


    return {
      key: 'impact',
      label: translations.impact || __('impact', 'Impact'),
      iconSrc:  limitations.length > 1 ? '/images/grid_impact_new.png' : '/images/grid_impact.png',
      context: statusInfo.context, 
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getImpactTooltip(pillar),
      pillar: pillar
    };
  }

  createGeneralHealthCard(pillar) {
    if (!PillarDataValidators.hasGeneralHealthData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getGeneralHealthStatus(pillar);
    const iconData = CardIconGenerators.getGeneralHealthIcons(pillar);
    const translations = window.healthDomainTranslations || {};
    
    const symptoms = pillar?.symptoms || [];
    const symptomsText = symptoms
      .filter(symptom => symptom && symptom !== '_' && symptom.trim() !== '')
      .map(symptom => SYMPTOM_LABELS[symptom] ? SYMPTOM_LABELS[symptom]() : symptom.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))
      .join(', ');
    
    return {
      key: 'general_health',
      label: translations.general_health || __('general_health', 'General Health'),
      iconSrc: '/images/grid_general_health.png',
      context: statusInfo.context, 
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getEnergyTooltip(pillar),
      pillar: pillar
    };
  }

  createMoodCard(pillar) {
    if (!PillarDataValidators.hasMoodData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getMoodStatus(pillar);
    const iconData = CardIconGenerators.getMoodIcons(pillar);
    const translations = window.healthDomainTranslations || {};

    return {
      key: 'mood',
      label: translations.mood || __('mood', 'Mood'),
      iconSrc: '/images/grid_mood.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getMoodTooltip(pillar),
      pillar: pillar
    };
  }

  createStoolCard(pillar) {
    if (!PillarDataValidators.hasStoolUrineData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getStoolUrineStatus(pillar);
    const iconData = CardIconGenerators.getStoolUrineIcons(pillar);
    const translations = window.healthDomainTranslations || {};
    
    return {
      key: 'stool_urine',
      label: translations.stool_urine || __('stool_urine', 'Stool/Urine'),
      iconSrc: '/images/grid_urine_stool.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getStoolTooltip(pillar),
      pillar: pillar
    };
  }

  createSleepCard(pillar) {
    if (!PillarDataValidators.hasSleepData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getSleepStatus(pillar);
    const iconData = CardIconGenerators.getSleepIcons(pillar);
    const translations = window.healthDomainTranslations || {};
    
    return {
      key: 'sleep',
      label: translations.sleep || __('sleep', 'Sleep'),
      iconSrc: '/images/grid_sleep.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getSleepTooltip(pillar),
      pillar: pillar
    };
  }

  createDietCard(pillar) {
    if (!PillarDataValidators.hasDietData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getDietStatus(pillar);
    const iconData = CardIconGenerators.getDietIcons(pillar);
    const translations = window.healthDomainTranslations || {};
    
    return {
      key: 'diet',
      label: translations.diet || __('diet', 'Diet'),
      iconSrc: '/images/grid_diet.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getDietTooltip(pillar),
      pillar: pillar
    };
  }

  createExerciseCard(pillar) {
    if (!PillarDataValidators.hasExerciseData(pillar)) {
      return null;
    }

    const types = pillar.types || [];
    
    const formattedTypes = types
      .filter(type => type && type !== '_' && type.trim() !== '')
      .map(type => EXERCISE_TYPE_LABELS[type] || type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()))
      .join(', ');
    
    const statusInfo = CardStatusGenerators.getExerciseStatus(pillar);
    const iconData = CardIconGenerators.getExerciseIcons(pillar);
    const translations = window.healthDomainTranslations || {};

    return {
      key: 'exercise',
      label: translations.exercise || __('exercise', 'Exercise'),
      iconSrc: '/images/grid_sport.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getExerciseTooltip(pillar),
      pillar: pillar
    };
  }

  createSexCard(pillar) {
    if (!PillarDataValidators.hasSexData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getSexStatus(pillar);
    const iconData = CardIconGenerators.getSexIcons(pillar);
    const translations = window.healthDomainTranslations || {};
    
    return {
      key: 'sex',
      label: translations.sex || __('sex', 'Sexual Health'),
      iconSrc: '/images/grid_sex.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getSexTooltip(pillar),
      pillar: pillar
    };
  }

  createNotesCard(pillar) {
    if (!PillarDataValidators.hasNotesData(pillar)) {
      return null;
    }

    const statusInfo = CardStatusGenerators.getNotesStatus(pillar);
    const iconData = CardIconGenerators.getNotesIcons(pillar);
    const translations = window.healthDomainTranslations || {};
    
    return {
      key: 'notes',
      label: translations.notes || __('notes', 'Notes'),
      iconSrc: '/images/grid_notes.png',
      context: statusInfo.context,
      statusColor: statusInfo.statusColor,
      statusText: statusInfo.statusText,
      severityIcons: iconData.severityIcons,
      statusIcon: iconData.statusIcon,
      tooltip: this.component.getNotesTooltip(pillar),
      pillar: pillar
    };
  }
}
