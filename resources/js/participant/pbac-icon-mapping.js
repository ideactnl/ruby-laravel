/**
 * PBAC Icon Mapping Configuration
 * 
 * Maps PBAC field values to custom icon images based on Excel specification.
 * Handles slider-to-discrete-icon mapping for continuous scales.
 */

// Base path for all PBAC icons
const ICON_BASE_PATH = '/images/';

/**
 * Blood Loss Icon Mapping
 * Handles severity levels (1-5) and special spotting case
 */
export const BLOOD_LOSS_MAPPING = {
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}blood_loss.png`, label: 'Blood Loss' };
    
    const { severity, spotting, amount } = value;
    
    if (spotting) {
      const tooltip = `Blood Loss: Spotting\nAmount: ${amount || 0}`;
      return {
        src: `${ICON_BASE_PATH}spotting.png`,
        label: tooltip
      };
    }
    
    // Map severity to icon (1-5 levels)
    const severityMap = {
      'very_light': { icon: 'blood_loss_1.png', label: 'Very Light' },
      'light': { icon: 'blood_loss_2.png', label: 'Light' },
      'moderate': { icon: 'blood_loss_3.png', label: 'Moderate' },
      'heavy': { icon: 'blood_loss_4.png', label: 'Heavy' },
      'very_heavy': { icon: 'blood_loss_5.png', label: 'Very Heavy' },
      'none': { icon: 'no_blood_loss.png', label: 'None' }
    };
    
    const mapping = severityMap[severity] || severityMap['none'];
    const tooltip = `Blood Loss: ${mapping.label}\nAmount: ${amount || 0}`;
    
    return {
      src: `${ICON_BASE_PATH}${mapping.icon}`,
      label: tooltip
    };
  }
};

/**
 * Pain Icon Mapping
 * Maps 0-10 pain scale to 6 smile/face icons
 */
export const PAIN_MAPPING = {
  regionLabels: {
    'umbilical': 'Umbilical',
    'left_umbilical': 'Left Umbilical',
    'right_umbilical': 'Right Umbilical',
    'bladder': 'Bladder',
    'left_groin': 'Left Groin',
    'right_groin': 'Right Groin',
    'left_leg': 'Left Leg',
    'right_leg': 'Right Leg',
    'upper_back': 'Upper Back',
    'back': 'Back',
    'left_buttock': 'Left Buttock',
    'right_buttock': 'Right Buttock',
    'left_back_leg': 'Left Back Leg',
    'right_back_leg': 'Right Back Leg'
  },

  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}pain.png`, label: 'Pain' };
    
    const painValue = value.value || 0;
    const regions = value.regions || [];
    
    // Map 0-10 scale to 6 icons
    let iconNumber, label;
    if (painValue === 0 || painValue === 1) {
      iconNumber = 1;
      label = 'No/Minimal Pain';
    } else if (painValue === 2) {
      iconNumber = 2;
      label = 'Very Light Pain';
    } else if (painValue === 3 || painValue === 4) {
      iconNumber = 3;
      label = 'Light-Mild Pain';
    } else if (painValue === 5 || painValue === 6) {
      iconNumber = 4;
      label = 'Moderate Pain';
    } else if (painValue === 7 || painValue === 8) {
      iconNumber = 5;
      label = 'Severe Pain';
    } else { // 9-10
      iconNumber = 6;
      label = 'Extreme Pain';
    }
    
    // Enhanced tooltip with more detail
    let tooltip = `Pain Level: ${painValue}/10 (${label})`;
    if (regions.length > 0) {
      const regionText = regions.length === 1 ? 'Area' : 'Areas';
      const friendlyRegions = regions.map(region => 
        PAIN_MAPPING.regionLabels[region] || region
      );
      tooltip += `\nAffected ${regionText}: ${friendlyRegions.join(', ')}`;
    }
    
    return {
      src: `${ICON_BASE_PATH}smile_${iconNumber}.png`,
      label: tooltip
    };
  }
};

/**
 * Impact Icon Mapping
 * Maps boolean impact conditions to specific icons
 */
export const IMPACT_MAPPING = {
  conditions: {
    'used_medication': { icon: 'impact_1.png', label: 'Used Medication' },
    'missed_work': { icon: 'impact_2.png', label: 'Missed Work' },
    'missed_school': { icon: 'impact_3.png', label: 'Missed School' },
    'could_not_sport': { icon: 'impact_4.png', label: 'Could Not Sport' },
    'missed_social_activities': { icon: 'impact_5.png', label: 'Missed Social Activities' },
    'missed_leisure_activities': { icon: 'impact_6.png', label: 'Missed Leisure Activities' },
    'had_to_sit_more': { icon: 'impact_7.png', label: 'Had to Sit More' },
    'had_to_lie_down': { icon: 'impact_8.png', label: 'Had to Lie Down' },
    'had_to_stay_longer_in_bed': { icon: 'impact_9.png', label: 'Had to Stay Longer in Bed' },
    'could_not_do_unpaid_work': { icon: 'impact_10.png', label: 'Could Not Do Unpaid Work' },
    'other': { icon: 'impact_11.png', label: 'Other Impact' }
  },
  
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}impact.png`, label: 'Impact' };
    
    const { gradeYourDay, limitations } = value;
    
    // Find the most significant limitation or use grade
    if (limitations && limitations.length > 0) {
      const primaryLimitation = limitations[0];
      const condition = IMPACT_MAPPING.conditions[primaryLimitation];
      if (condition) {
        let tooltip = `Daily Impact: ${gradeYourDay}/10`;
        tooltip += `\nPrimary Issue: ${condition.label}`;
        if (limitations.length > 1) {
          tooltip += `\nAdditional Issues: ${limitations.length - 1} more`;
        }
        
        return {
          src: `${ICON_BASE_PATH}${condition.icon}`,
          label: tooltip
        };
      }
    }
    
    // Fallback to grade-based icon
    const gradeIcons = {
      high: 'impact_1.png', // 8-10
      good: 'impact_2.png', // 6-7
      okay: 'impact_3.png', // 4-5
      difficult: 'impact_4.png' // 0-3
    };
    
    let category, icon;
    if (gradeYourDay >= 8) {
      category = 'Great Day';
      icon = gradeIcons.high;
    } else if (gradeYourDay >= 6) {
      category = 'Good Day';
      icon = gradeIcons.good;
    } else if (gradeYourDay >= 4) {
      category = 'Okay Day';
      icon = gradeIcons.okay;
    } else {
      category = 'Difficult Day';
      icon = gradeIcons.difficult;
    }
    
    const tooltip = `Daily Impact: ${gradeYourDay}/10 (${category})`;
    
    return {
      src: `${ICON_BASE_PATH}${icon}`,
      label: tooltip
    };
  }
};

/**
 * General Health Icon Mapping
 * Maps energy levels 1-5 to specific icons
 */
export const GENERAL_HEALTH_MAPPING = {
  // Convert raw symptom names to user-friendly text
  symptomLabels: {
    'dizzy': 'Dizzy',
    'nauseous': 'Nauseous',
    'headache_migraine': 'Headache/Migraine',
    'bloated': 'Bloated',
    'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
    'acne': 'Acne',
    'muscle_joint_pain': 'Muscle/Joint Pain'
  },

  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}general_health.png`, label: 'General Health' };
    
    const energyLevel = value.energyLevel || 1;
    const symptoms = value.symptoms || [];
    
    // Map energy levels to icons
    const energyMap = {
      1: { icon: 'sleep.png', label: 'Very Low Energy' },
      2: { icon: 'general_health_1.png', label: 'Low Energy' },
      3: { icon: 'general_health_2.png', label: 'Moderate Energy' },
      4: { icon: 'general_health_3.png', label: 'Good Energy' },
      5: { icon: 'general_health_4.png', label: 'High Energy' }
    };
    
    const mapping = energyMap[energyLevel] || energyMap[1];
    
    let tooltip = `Energy Level: ${energyLevel}/5 (${mapping.label})`;
    if (symptoms.length > 0) {
      const friendlySymptoms = symptoms.map(symptom => 
        GENERAL_HEALTH_MAPPING.symptomLabels[symptom] || symptom
      );
      tooltip += `\nSymptoms: ${friendlySymptoms.join(', ')}`;
    } else {
      tooltip += `\nNo symptoms reported`;
    }
    
    return {
      src: `${ICON_BASE_PATH}${mapping.icon}`,
      label: tooltip
    };
  }
};

/**
 * Mood Icon Mapping
 * Maps specific mood states to corresponding icons
 */
export const MOOD_MAPPING = {
  states: {
    'calm': { icon: 'mood_1.png', label: 'Calm' },
    'happy': { icon: 'mood_2.png', label: 'Happy' },
    'excited': { icon: 'mood_3.png', label: 'Excited' },
    'anxious_stressed': { icon: 'mood_4.png', label: 'Anxious/Stressed' },
    'ashamed': { icon: 'mood_5.png', label: 'Ashamed' },
    'angry_irritable': { icon: 'mood_6.png', label: 'Angry/Irritable' },
    'sad': { icon: 'mood_7.png', label: 'Sad' },
    'mood_swings': { icon: 'mood_8.png', label: 'Mood Swings' },
    'worthless_guilty': { icon: 'mood_9.png', label: 'Worthless/Guilty' },
    'overwhelmed': { icon: 'mood_10.png', label: 'Overwhelmed' },
    'hopeless': { icon: 'mood_11.png', label: 'Hopeless' },
    'depressed_sad_down': { icon: 'mood_12.png', label: 'Depressed/Sad/Down' }
  },

  // Convert raw mood state names to user-friendly text
  stateLabels: {
    'calm': 'Calm',
    'happy': 'Happy',
    'excited': 'Excited',
    'anxious_stressed': 'Anxious/Stressed',
    'ashamed': 'Ashamed',
    'angry_irritable': 'Angry/Irritable',
    'sad': 'Sad',
    'mood_swings': 'Mood Swings',
    'worthless_guilty': 'Worthless/Guilty',
    'overwhelmed': 'Overwhelmed',
    'hopeless': 'Hopeless',
    'hopes': 'Hopeful',
    'depressed_sad_down': 'Depressed/Sad/Down'
  },
  
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}mood.png`, label: 'Mood' };
    
    const { positives, negatives } = value;
    const balance = (positives?.length || 0) - (negatives?.length || 0);
    
    // Determine primary mood based on balance and specific states
    let primaryMood = 'calm';
    if (balance > 2) primaryMood = 'happy';
    else if (balance > 0) primaryMood = 'excited';
    else if (balance === 0) primaryMood = 'calm';
    else if (balance > -3) primaryMood = 'sad';
    else primaryMood = 'depressed_sad_down';
    
    const moodData = MOOD_MAPPING.states[primaryMood] || MOOD_MAPPING.states['calm'];
    
    let tooltip = `Mood: ${moodData.label}`;
    
    if (positives?.length > 0) {
      const friendlyPositives = positives.map(state => 
        MOOD_MAPPING.stateLabels[state] || state
      );
      tooltip += `\nPositive: ${friendlyPositives.join(', ')}`;
    }
    
    if (negatives?.length > 0) {
      const friendlyNegatives = negatives.map(state => 
        MOOD_MAPPING.stateLabels[state] || state
      );
      tooltip += `\nNegative: ${friendlyNegatives.join(', ')}`;
    }
    
    if (balance > 0) {
      tooltip += `\nOverall: Positive day`;
    } else if (balance < 0) {
      tooltip += `\nOverall: Challenging day`;
    } else {
      tooltip += `\nOverall: Balanced day`;
    }
    
    return {
      src: `${ICON_BASE_PATH}${moodData.icon}`,
      label: tooltip
    };
  }
};

/**
 * Stool/Urine Icon Mapping
 * Maps stool consistency and urine conditions to icons
 */
export const STOOL_URINE_MAPPING = {
  conditions: {
    'blood_in_stool': { icon: 'urine_stool.png', label: 'Blood in Stool' },
    'hard': { icon: 'urine_stool_1.png', label: 'Hard Stool' },
    'normal': { icon: 'urine_stool_2.png', label: 'Normal Stool' },
    'soft': { icon: 'urine_stool_3.png', label: 'Soft Stool' },
    'watery': { icon: 'urine_stool_4.png', label: 'Watery Stool' },
    'something_else': { icon: 'urine_stool_5.png', label: 'Something Else' },
    'no_stool': { icon: 'urine_stool_6.png', label: 'No Stool' }
  },
  
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}urine_stool.png`, label: 'Stool/Urine' };
    
    const { urine, stool } = value;
    
    // Priority: blood > specific conditions
    if (urine?.blood || stool?.blood) {
      let tooltip = `Blood Detected`;
      if (urine?.blood && stool?.blood) {
        tooltip += `\nBoth urine and stool`;
      } else if (urine?.blood) {
        tooltip += `\nIn urine`;
      } else {
        tooltip += `\nIn stool`;
      }
      
      return {
        src: `${ICON_BASE_PATH}urine_stool.png`,
        label: tooltip
      };
    }
    
    // Map stool consistency
    if (stool?.consistency) {
      const condition = STOOL_URINE_MAPPING.conditions[stool.consistency] || STOOL_URINE_MAPPING.conditions['normal'];
      const tooltip = `Stool: ${condition.label}`;
      
      return {
        src: `${ICON_BASE_PATH}${condition.icon}`,
        label: tooltip
      };
    }
    
    return {
      src: `${ICON_BASE_PATH}urine_stool_2.png`,
      label: 'Stool: Normal'
    };
  }
};

/**
 * Exercise/Sport Icon Mapping
 * Maps exercise duration and type to icons
 */
export const EXERCISE_MAPPING = {
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}sport.png`, label: 'Exercise' };
    
    const { levels, impacts } = value;
    
    let timeRange = 'Exercise';
    if (levels?.includes('greater_sixty')) {
      timeRange = 'Exercise: >60 min';
    } else if (levels?.includes('thirty_to_sixty')) {
      timeRange = 'Exercise: 30-60 min';
    } else if (levels?.includes('less_thirty')) {
      timeRange = 'Exercise: <30 min';
    }
    
    const impactText = impacts?.length > 0 ? ` (${impacts.join(', ')})` : '';
    
    return {
      src: `${ICON_BASE_PATH}sport.png`,
      label: timeRange + impactText
    };
  }
};

/**
 * Diet Icon Mapping
 * Maps food items to specific diet icons
 */
export const DIET_MAPPING = {
  items: {
    'vegetables': { icon: 'diet_1.png', label: 'Vegetables' },
    'fruit': { icon: 'diet_2.png', label: 'Fruit' },
    'potato_rice_bread': { icon: 'diet_3.png', label: 'Carbohydrates' },
    'dairy_products': { icon: 'diet_4.png', label: 'Dairy Products' },
    'nuts_tofu_tempe': { icon: 'diet_5.png', label: 'Protein Alternatives' },
    'egg': { icon: 'diet_6.png', label: 'Eggs' },
    'fish': { icon: 'diet_7.png', label: 'Fish' },
    'meat': { icon: 'diet_8.png', label: 'Meat' },
    'snacks': { icon: 'diet_9.png', label: 'Snacks' },
    'water': { icon: 'diet_10.png', label: 'Water' },
    'coffee': { icon: 'diet_11.png', label: 'Coffee' },
    'alcohol': { icon: 'diet_12.png', label: 'Alcohol' }
  },

  // Convert raw diet item names to user-friendly text
  itemLabels: {
    'vegetables': 'Vegetables',
    'fruit': 'Fruit',
    'potato_rice_bread': 'Carbohydrates',
    'dairy': 'Dairy Products',
    'nuts_tofu_tempe': 'Protein Alternatives',
    'eggs': 'Eggs',
    'fish': 'Fish',
    'meat': 'Meat',
    'snacks': 'Snacks',
    'soda': 'Soda',
    'water': 'Water',
    'coffee': 'Coffee',
    'alcohol': 'Alcohol'
  },
  
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}diet.png`, label: 'Diet' };
    
    const { positives, negatives, neutrals } = value;
    const total = (positives?.length || 0) + (negatives?.length || 0) + (neutrals?.length || 0);
    
    // Determine primary diet item or overall assessment
    let primaryItem = 'diet.png';
    let tooltip = 'Diet';
    
    if (negatives?.length === 0 && positives?.length > 0) {
      tooltip = `Diet: Healthy (${total} items)`;
    } else if ((negatives?.length || 0) <= (positives?.length || 0)) {
      tooltip = `Diet: Mixed (${total} items)`;
    } else {
      tooltip = `Diet: Concerning (${total} items)`;
    }
    
    // Add consumed items to tooltip
    const allItems = [...(positives || []), ...(negatives || []), ...(neutrals || [])];
    if (allItems.length > 0) {
      const friendlyItems = allItems.map(item => 
        DIET_MAPPING.itemLabels[item] || item
      );
      tooltip += `\nConsumed: ${friendlyItems.join(', ')}`;
    }
    
    return {
      src: `${ICON_BASE_PATH}${primaryItem}`,
      label: tooltip
    };
  }
};

/**
 * Sex Icon Mapping
 * Maps sexual health conditions to icons
 */
export const SEX_MAPPING = {
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}sex.png`, label: 'Sexual Health' };
    
    const { today, avoided, issues, satisfied } = value;
    
    let label = 'Sex: ';
    if (avoided) {
      label += 'Avoided due to pain';
    } else if (today && satisfied) {
      label += 'Satisfying experience';
    } else if (today && issues?.length > 0) {
      label += `With issues (${issues.length})`;
    } else if (today) {
      label += 'Activity recorded';
    } else {
      label += 'No activity';
    }
    
    return {
      src: `${ICON_BASE_PATH}sex.png`,
      label
    };
  }
};

/**
 * Sleep Icon Mapping
 */
export const SLEEP_MAPPING = {
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}sleep.png`, label: 'Sleep' };
    
    const { calculatedHours, troubleAsleep, wakeUpDuringNight, tiredRested } = value;
    const hours = calculatedHours || 0;
    
    const issues = [];
    if (troubleAsleep) issues.push('trouble falling asleep');
    if (wakeUpDuringNight) issues.push('woke up during night');
    if (!tiredRested) issues.push('not well rested');
    
    let quality = 'Good';
    if (hours >= 7 && hours <= 9 && issues.length === 0) {
      quality = 'Good';
    } else if (hours >= 6 && hours <= 10 && issues.length <= 1) {
      quality = 'Okay';
    } else {
      quality = 'Poor';
    }
    
    let tooltip = `Sleep: ${quality} (${hours}h)`;
    if (issues.length > 0) {
      tooltip += `\nIssues: ${issues.join(', ')}`;
    } else {
      tooltip += `\nNo sleep issues`;
    }
    
    return {
      src: `${ICON_BASE_PATH}sleep.png`,
      label: tooltip
    };
  }
};

/**
 * Notes Icon Mapping
 */
export const NOTES_MAPPING = {
  getIcon: (value) => {
    if (typeof value !== 'object') return { src: `${ICON_BASE_PATH}grid_notes.png`, label: 'Notes' };
    
    const noteText = value.text || 'Note recorded';
    return {
      src: `${ICON_BASE_PATH}grid_notes.png`,
      label: `Note: ${noteText}`
    };
  }
};

/**
 * Main mapping function - determines which mapper to use
 */
export function getPbacIcon(type, value) {
  const mappers = {
    'blood_loss': BLOOD_LOSS_MAPPING,
    'pain': PAIN_MAPPING,
    'impact': IMPACT_MAPPING,
    'general_health': GENERAL_HEALTH_MAPPING,
    'mood': MOOD_MAPPING,
    'stool_urine': STOOL_URINE_MAPPING,
    'exercise': EXERCISE_MAPPING,
    'diet': DIET_MAPPING,
    'sex': SEX_MAPPING,
    'sleep': SLEEP_MAPPING,
    'notes': NOTES_MAPPING
  };
  
  const mapper = mappers[type];
  if (mapper && mapper.getIcon) {
    return mapper.getIcon(value);
  }
  
  // Fallback
  return {
    src: `${ICON_BASE_PATH}${type}.png`,
    label: `${type}: ${JSON.stringify(value)}`
  };
}
