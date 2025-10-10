/**
 * Tooltip Generators Module
 * Generates tooltips for each PBAC pillar type
 */

const REGION_LABELS = {
  'umbilical': 'Umbilical', 'left_umbilical': 'Left Umbilical', 'right_umbilical': 'Right Umbilical',
  'bladder': 'Bladder', 'left_groin': 'Left Groin', 'right_groin': 'Right Groin',
  'left_leg': 'Left Leg', 'right_leg': 'Right Leg', 'upper_back': 'Upper Back',
  'back': 'Back', 'left_buttock': 'Left Buttock', 'right_buttock': 'Right Buttock',
  'left_back_leg': 'Left Back Leg', 'right_back_leg': 'Right Back Leg'
};

const LIMITATION_LABELS = {
  'used_medication': 'Used Medication', 'missed_work': 'Missed Work', 'missed_school': 'Missed School',
  'could_not_sport': 'Could Not Sport', 'missed_social_activities': 'Missed Social Activities',
  'missed_leisure_activities': 'Missed Leisure Activities', 'had_to_sit_more': 'Had to Sit More',
  'had_to_lie_down': 'Had to Lie Down', 'had_to_stay_longer_in_bed': 'Had to Stay Longer in Bed',
  'could_not_do_unpaid_work': 'Could Not Do Unpaid Work', 'other': 'Other Impact'
};

const SYMPTOM_LABELS = {
  'dizzy': 'Dizzy', 'nauseous': 'Nauseous', 'headache_migraine': 'Headache/Migraine',
  'bloated': 'Bloated', 'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
  'acne': 'Acne', 'muscle_joint_pain': 'Muscle/Joint Pain'
};

const MOOD_LABELS = {
  'calm': 'Calm', 'happy': 'Happy', 'excited': 'Excited', 'hopes': 'Hopeful',
  'anxious_stressed': 'Anxious/Stressed', 'ashamed': 'Ashamed', 'angry_irritable': 'Angry/Irritable',
  'sad': 'Sad', 'mood_swings': 'Mood Swings', 'worthless_guilty': 'Worthless/Guilty',
  'overwhelmed': 'Overwhelmed', 'hopeless': 'Hopeless', 'depressed_sad_down': 'Depressed/Sad/Down'
};

const DIET_LABELS = {
  'vegetables': 'Vegetables', 'fruit': 'Fruit', 'potato_rice_bread': 'Carbohydrates',
  'dairy': 'Dairy Products', 'nuts_tofu_tempe': 'Protein Alternatives', 'eggs': 'Eggs',
  'fish': 'Fish', 'meat': 'Meat', 'snacks': 'Snacks', 'soda': 'Soda',
  'water': 'Water', 'coffee': 'Coffee', 'alcohol': 'Alcohol'
};

export class TooltipGenerators {
  static getBloodLossTooltip(pillar) {
    if (!pillar || !pillar.amount) return 'No blood loss recorded';
    const amount = pillar.amount;
    const severity = pillar.severity || 'light';
    const spotting = pillar.flags?.spotting;

    if (spotting) return `Amount: ${amount} (spotting detected)`;
    return `Amount: ${amount} | Severity: ${severity}`;
  }

  static getPainTooltip(pillar) {
    if (!pillar || !pillar.value) return 'No pain recorded';
    const value = pillar.value;
    const regions = pillar.regions || [];

    if (regions.length > 0) {
      const friendlyRegions = regions.map(region => REGION_LABELS[region] || region);
      return `Pain Level: ${value}/10 | Regions: ${friendlyRegions.join(', ')}`;
    }
    return `Pain Level: ${value}/10`;
  }

  static getImpactTooltip(pillar) {
    if (!pillar || !pillar.gradeYourDay) return 'No impact recorded';
    const grade = pillar.gradeYourDay;
    const limitations = pillar.limitations || [];

    if (limitations.length > 0) {
      const friendlyLimitations = limitations.map(limitation => LIMITATION_LABELS[limitation] || limitation);
      return `Grade: ${grade}/10 | Limitations: ${friendlyLimitations.join(', ')}`;
    }
    return `Grade Your Day: ${grade}/10`;
  }

  static getEnergyTooltip(pillar) {
    if (!pillar || !pillar.energyLevel) return 'No energy level recorded';
    const energy = pillar.energyLevel;
    const symptoms = pillar.symptoms || [];

    if (symptoms.length > 0) {
      const friendlySymptoms = symptoms.map(symptom => SYMPTOM_LABELS[symptom] || symptom);
      return `Energy: ${energy}/5 | Symptoms: ${friendlySymptoms.join(', ')}`;
    }
    return `Energy Level: ${energy}/5`;
  }

  static getMoodTooltip(pillar) {
    if (!pillar) return 'No mood indicators recorded';
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];

    if (positives.length === 0 && negatives.length === 0) return 'No mood indicators recorded';

    let tooltip = '';
    if (positives.length > 0) {
      const friendlyPositives = positives.map(mood => MOOD_LABELS[mood] || mood);
      tooltip += `Positive: ${friendlyPositives.join(', ')}`;
    }
    if (negatives.length > 0) {
      if (tooltip) tooltip += ' | ';
      const friendlyNegatives = negatives.map(mood => MOOD_LABELS[mood] || mood);
      tooltip += `Negative: ${friendlyNegatives.join(', ')}`;
    }
    return tooltip;
  }

  static getStoolTooltip(pillar) {
    if (!pillar) return 'No stool/urine issues recorded';
    const issues = [];
    if (pillar.urine?.blood) issues.push('blood in urine');
    if (pillar.stool?.blood) issues.push('blood in stool');

    if (issues.length === 0) return 'No stool/urine issues recorded';
    return `Issues: ${issues.join(' and ')}`;
  }

  static getSleepTooltip(pillar) {
    if (!pillar || !pillar.calculatedHours) return 'No sleep data recorded';
    const hours = pillar.calculatedHours;
    const issues = [];

    if (pillar.troubleAsleep) issues.push('trouble falling asleep');
    if (pillar.wakeUpDuringNight) issues.push('woke up during night');
    if (!pillar.tiredRested) issues.push('not well rested');

    let tooltip = `Sleep: ${hours} hours`;
    if (issues.length > 0) {
      tooltip += ` | Issues: ${issues.join(', ')}`;
    }
    return tooltip;
  }

  static getDietTooltip(pillar) {
    if (!pillar) return 'No diet items recorded';
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    const neutrals = pillar.neutrals || [];

    if (positives.length === 0 && negatives.length === 0 && neutrals.length === 0) return 'No diet items recorded';

    let tooltip = '';
    if (positives.length > 0) {
      const friendlyPositives = positives.map(item => DIET_LABELS[item] || item);
      tooltip += `Good: ${friendlyPositives.join(', ')}`;
    }
    if (negatives.length > 0) {
      if (tooltip) tooltip += ' | ';
      const friendlyNegatives = negatives.map(item => DIET_LABELS[item] || item);
      tooltip += `Poor: ${friendlyNegatives.join(', ')}`;
    }
    if (neutrals.length > 0) {
      if (tooltip) tooltip += ' | ';
      const friendlyNeutrals = neutrals.map(item => DIET_LABELS[item] || item);
      tooltip += `Neutral: ${friendlyNeutrals.join(', ')}`;
    }
    return tooltip;
  }

  static getExerciseTooltip(pillar) {
    if (!pillar || !pillar.any) return 'No exercise recorded';
    const levels = pillar.levels || [];
    const impacts = pillar.impacts || [];

    let timeRange = 'Exercise completed';
    if (levels.includes('greater_sixty')) timeRange = 'Duration: >60 minutes';
    else if (levels.includes('thirty_to_sixty')) timeRange = 'Duration: 30-60 minutes';
    else if (levels.includes('less_thirty')) timeRange = 'Duration: <30 minutes';

    if (impacts.length > 0) {
      const impactLabels = {
        'high_impact': 'High Impact',
        'low_impact': 'Low Impact',
        'precision_exercise': 'Precision Exercise'
      };
      const formattedImpacts = impacts.map(impact => impactLabels[impact] || impact.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
      return `${timeRange} | Type: ${formattedImpacts.join(', ')}`;
    }
    return timeRange;
  }

  static getSexTooltip(pillar) {
    if (!pillar || !pillar.today) return 'No sexual activity recorded';
    const avoided = pillar.avoided;
    const issues = pillar.issues || [];
    const satisfied = pillar.satisfied;

    if (avoided) return 'Sexual activity avoided';
    if (issues.length > 0) return `Sexual activity with issues: ${issues.join(', ')}`;
    if (satisfied) return 'Sexual activity - satisfied';
    return 'Sexual activity recorded';
  }

  static getNotesTooltip(pillar) {
    if (!pillar || !pillar.hasNote) return 'No notes recorded';
    const text = pillar.text || 'Note recorded';
    return `Note: ${text}`;
  }
}
