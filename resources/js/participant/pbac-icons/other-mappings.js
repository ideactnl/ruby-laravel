/**
 * Other PBAC Icon Mappings
 * Stool/Urine, Exercise, Diet, Sex, Sleep, Notes
 */

import { createIconResult } from './config.js';

// Stool/Urine Mapping
const STOOL_CONDITIONS = {
  'blood_in_stool': { icon: 'urine_stool.png', label: 'Blood in Stool' },
  'hard': { icon: 'urine_stool_1.png', label: 'Hard Stool' },
  'normal': { icon: 'urine_stool_2.png', label: 'Normal Stool' },
  'soft': { icon: 'urine_stool_3.png', label: 'Soft Stool' },
  'watery': { icon: 'urine_stool_4.png', label: 'Watery Stool' },
  'something_else': { icon: 'urine_stool_5.png', label: 'Something Else' },
  'no_stool': { icon: 'urine_stool_6.png', label: 'No Stool' }
};

export function getStoolUrineIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('urine_stool.png', 'Stool/Urine');
  }
  
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
    return createIconResult('urine_stool.png', tooltip);
  }
  
  // Map stool consistency
  if (stool?.consistency) {
    const condition = STOOL_CONDITIONS[stool.consistency] || STOOL_CONDITIONS['normal'];
    return createIconResult(condition.icon, `Stool: ${condition.label}`);
  }
  
  return createIconResult('urine_stool_2.png', 'Stool: Normal');
}

// Exercise Mapping
export function getExerciseIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('sport.png', 'Exercise');
  }
  
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
  return createIconResult('sport.png', timeRange + impactText);
}

// Diet Mapping
const DIET_ITEM_LABELS = {
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
};

export function getDietIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('diet.png', 'Diet');
  }
  
  const { positives, negatives, neutrals } = value;
  const total = (positives?.length || 0) + (negatives?.length || 0) + (neutrals?.length || 0);
  
  let tooltip;
  if (negatives?.length === 0 && positives?.length > 0) {
    tooltip = `Diet: (${total} items)`;
  } else if ((negatives?.length || 0) <= (positives?.length || 0)) {
    tooltip = `Diet: (${total} items)`;
  } else {
    tooltip = `Diet: (${total} items)`;
  }
  
  // Add consumed items to tooltip
  const allItems = [...(positives || []), ...(negatives || []), ...(neutrals || [])];
  if (allItems.length > 0) {
    const friendlyItems = allItems.map(item => DIET_ITEM_LABELS[item] || item);
    tooltip += `\nConsumed: ${friendlyItems.join(', ')}`;
  }
  
  return createIconResult('diet.png', tooltip);
}

// Sex Mapping
export function getSexIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('sex.png', 'Sexual Health');
  }
  
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
  
  return createIconResult('sex.png', label);
}

// Sleep Mapping
export function getSleepIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('sleep.png', 'Sleep');
  }
  
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
  
  return createIconResult('sleep.png', tooltip);
}

// Notes Mapping
export function getNotesIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('grid_notes.png', 'Notes');
  }
  
  const noteText = value.text || 'Note recorded';
  return createIconResult('grid_notes.png', `Note: ${noteText}`);
}
