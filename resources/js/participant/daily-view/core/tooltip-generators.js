/**
 * Tooltip Generators Module
 * Generates tooltips for each PBAC pillar type
 */

import { getTooltipTranslation } from '../../utils/translations.js';

// Helper functions to get translated labels
function getRegionLabel(region) {
  return getTooltipTranslation(`tooltip_region_${region}`, region);
}

function getLimitationLabel(limitation) {
  return getTooltipTranslation(`tooltip_limitation_${limitation}`, limitation);
}

function getSymptomLabel(symptom) {
  return getTooltipTranslation(`tooltip_symptom_${symptom}`, symptom);
}

function getMoodLabel(mood) {
  return getTooltipTranslation(`tooltip_mood_${mood}`, mood);
}

function getDietLabel(diet) {
  return getTooltipTranslation(`tooltip_diet_${diet}`, diet);
}

export class TooltipGenerators {
  static getBloodLossTooltip(pillar) {
    if (!pillar || !pillar.amount) return getTooltipTranslation('tooltip_no_blood_loss_recorded');
    const amount = pillar.amount;
    const severity = pillar.severity || 'light';
    const spotting = pillar.flags?.spotting;

    if (spotting) return `${getTooltipTranslation('tooltip_amount')} ${amount} (${getTooltipTranslation('tooltip_spotting_detected')})`;
    return `${getTooltipTranslation('tooltip_amount')} ${amount} | ${getTooltipTranslation('tooltip_severity')} ${severity}`;
  }

  static getPainTooltip(pillar) {
    if (!pillar || !pillar.value) return getTooltipTranslation('tooltip_no_pain_recorded');
    const value = pillar.value;
    const regions = pillar.regions || [];

    if (regions.length > 0) {
      const friendlyRegions = regions.map(region => getRegionLabel(region));
      return `${getTooltipTranslation('tooltip_pain_level')} ${value}/10 | ${getTooltipTranslation('tooltip_regions')} ${friendlyRegions.join(', ')}`;
    }
    return `${getTooltipTranslation('tooltip_pain_level')} ${value}/10`;
  }

  static getImpactTooltip(pillar) {
    if (!pillar || !pillar.gradeYourDay) return getTooltipTranslation('tooltip_no_impact_recorded');
    const grade = pillar.gradeYourDay;
    const limitations = pillar.limitations || [];

    if (limitations.length > 0) {
      const friendlyLimitations = limitations.map(limitation => getLimitationLabel(limitation));
      return `${getTooltipTranslation('tooltip_grade')} ${grade}/10 | ${getTooltipTranslation('tooltip_limitations')} ${friendlyLimitations.join(', ')}`;
    }
    return `${getTooltipTranslation('tooltip_grade_your_day')} ${grade}/10`;
  }

  static getEnergyTooltip(pillar) {
    if (!pillar || pillar.energyLevel === null || pillar.energyLevel === undefined) return getTooltipTranslation('tooltip_no_energy_level_recorded');
    const energy = pillar.energyLevel;
    const symptoms = pillar.symptoms || [];

    if (symptoms.length > 0) {
      const friendlySymptoms = symptoms.map(symptom => getSymptomLabel(symptom));
      return `${getTooltipTranslation('tooltip_energy')} ${energy} | ${getTooltipTranslation('tooltip_symptoms')} ${friendlySymptoms.join(', ')}`;
    }
    return `${getTooltipTranslation('tooltip_energy_level')} ${energy}`;
  }

  static getMoodTooltip(pillar) {
    if (!pillar) return getTooltipTranslation('tooltip_no_mood_indicators_recorded');
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];

    if (positives.length === 0 && negatives.length === 0) return getTooltipTranslation('tooltip_no_mood_indicators_recorded');

    let tooltip = '';
    if (positives.length > 0) {
      const friendlyPositives = positives.map(mood => getMoodLabel(mood));
      tooltip += `${getTooltipTranslation('tooltip_positive')} ${friendlyPositives.join(', ')}`;
    }
    if (negatives.length > 0) {
      if (tooltip) tooltip += ' | ';
      const friendlyNegatives = negatives.map(mood => getMoodLabel(mood));
      tooltip += `${getTooltipTranslation('tooltip_negative')} ${friendlyNegatives.join(', ')}`;
    }
    return tooltip;
  }

  static getStoolTooltip(pillar) {
    if (!pillar) return getTooltipTranslation('tooltip_no_stool_urine_issues_recorded');
    const issues = [];
    if (pillar.urine?.blood) issues.push(getTooltipTranslation('tooltip_blood_in_urine'));
    if (pillar.stool?.blood) issues.push(getTooltipTranslation('tooltip_blood_in_stool'));

    if (issues.length === 0) return getTooltipTranslation('tooltip_no_stool_urine_issues_recorded');
    return `${getTooltipTranslation('tooltip_issues')} ${issues.join(` ${getTooltipTranslation('tooltip_and')} `)}`;
  }

  static getSleepTooltip(pillar) {
    if (!pillar || !pillar.calculatedHours) return getTooltipTranslation('tooltip_no_sleep_data_recorded');
    const hours = pillar.calculatedHours;
    const issues = [];

    if (pillar.troubleAsleep) issues.push(getTooltipTranslation('tooltip_trouble_falling_asleep'));
    if (pillar.wakeUpDuringNight) issues.push(getTooltipTranslation('tooltip_woke_up_during_night'));
    if (!pillar.tiredRested) issues.push(getTooltipTranslation('tooltip_not_well_rested'));

    let tooltip = `${getTooltipTranslation('tooltip_sleep')} ${hours} ${getTooltipTranslation('tooltip_hours')}`;
    if (issues.length > 0) {
      tooltip += ` | ${getTooltipTranslation('tooltip_issues')} ${issues.join(', ')}`;
    }
    return tooltip;
  }

  static getDietTooltip(pillar) {
    if (!pillar) return getTooltipTranslation('tooltip_no_diet_items_recorded');
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    const neutrals = pillar.neutrals || [];

    if (positives.length === 0 && negatives.length === 0 && neutrals.length === 0) return getTooltipTranslation('tooltip_no_diet_items_recorded');

    let tooltip = '';
    if (positives.length > 0) {
      const friendlyPositives = positives.map(item => getDietLabel(item));
      tooltip += `${getTooltipTranslation('tooltip_good')} ${friendlyPositives.join(', ')}`;
    }
    if (negatives.length > 0) {
      if (tooltip) tooltip += ' | ';
      const friendlyNegatives = negatives.map(item => getDietLabel(item));
      tooltip += `${getTooltipTranslation('tooltip_poor')} ${friendlyNegatives.join(', ')}`;
    }
    if (neutrals.length > 0) {
      if (tooltip) tooltip += ' | ';
      const friendlyNeutrals = neutrals.map(item => getDietLabel(item));
      tooltip += `${getTooltipTranslation('tooltip_neutral')} ${friendlyNeutrals.join(', ')}`;
    }
    return tooltip;
  }

  static getExerciseTooltip(pillar) {
    if (!pillar || !pillar.any) return getTooltipTranslation('tooltip_no_exercise_recorded');
    const levels = pillar.levels || [];
    const impacts = pillar.impacts || [];

    let timeRange = getTooltipTranslation('tooltip_exercise_completed');
    if (levels.includes('greater_sixty')) timeRange = `${getTooltipTranslation('tooltip_duration')} ${getTooltipTranslation('tooltip_greater_60_minutes')}`;
    else if (levels.includes('thirty_to_sixty')) timeRange = `${getTooltipTranslation('tooltip_duration')} ${getTooltipTranslation('tooltip_30_60_minutes')}`;
    else if (levels.includes('less_thirty')) timeRange = `${getTooltipTranslation('tooltip_duration')} ${getTooltipTranslation('tooltip_less_30_minutes')}`;

    if (impacts.length > 0) {
      const impactLabels = {
        'high_impact': getTooltipTranslation('tooltip_high_impact'),
        'low_impact': getTooltipTranslation('tooltip_low_impact'),
        'precision_exercise': getTooltipTranslation('tooltip_precision_exercise')
      };
      const formattedImpacts = impacts.map(impact => impactLabels[impact] || impact.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
      return `${timeRange} | ${getTooltipTranslation('tooltip_type')} ${formattedImpacts.join(', ')}`;
    }
    return timeRange;
  }

  static getSexTooltip(pillar) {
    if (!pillar || !pillar.today) return getTooltipTranslation('tooltip_no_sexual_activity_recorded');
    const avoided = pillar.avoided;
    const issues = pillar.issues || [];
    const satisfied = pillar.satisfied;

    if (avoided) return getTooltipTranslation('tooltip_sexual_activity_avoided');
    if (issues.length > 0) return `${getTooltipTranslation('tooltip_sexual_activity_with_issues')} ${issues.join(', ')}`;
    if (satisfied) return getTooltipTranslation('tooltip_sexual_activity_satisfied');
    return getTooltipTranslation('tooltip_sexual_activity_recorded');
  }

  static getNotesTooltip(pillar) {
    if (!pillar || !pillar.hasNote) return getTooltipTranslation('tooltip_no_notes_recorded');
    const text = pillar.text || getTooltipTranslation('tooltip_note_recorded');
    return `${getTooltipTranslation('tooltip_note')} ${text}`;
  }
}
