/**
 * Other PBAC Icon Mappings
 * Stool/Urine, Exercise, Diet, Sex, Sleep, Notes
 */

import { createIconResult, getTranslatedTooltip } from './config.js';

// Stool/Urine Mapping
const STOOL_CONDITIONS = {
  'blood_in_stool': { icon: 'urine_stool.png', label: () => getTranslatedTooltip('stool_urine_blood') },
  'hard': { icon: 'urine_stool_1.png', label: () => getTranslatedTooltip('stool_urine_hard') },
  'normal': { icon: 'urine_stool_2.png', label: () => getTranslatedTooltip('stool_urine_normal') },
  'soft': { icon: 'urine_stool_3.png', label: () => getTranslatedTooltip('stool_urine_soft') },
  'watery': { icon: 'urine_stool_4.png', label: () => getTranslatedTooltip('stool_urine_watery') },
  'something_else': { icon: 'urine_stool_5.png', label: () => getTranslatedTooltip('stool_urine_something_else') },
  'no_stool': { icon: 'urine_stool_6.png', label: () => getTranslatedTooltip('stool_urine_no_stool') }
};

export function getStoolUrineIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('urine_stool.png', getTranslatedTooltip('tooltip_stool_urine'));
  }

  const { urine, stool } = value;

  // Priority: blood > specific conditions
  if (urine?.blood || stool?.blood) {
    let tooltip = getTranslatedTooltip('stool_urine_blood_detected');
    if (urine?.blood && stool?.blood) {
      tooltip += `\n${getTranslatedTooltip('stool_urine_both')}`;
    } else if (urine?.blood) {
      tooltip += `\n${getTranslatedTooltip('stool_urine_in_urine')}`;
    } else {
      tooltip += `\n${getTranslatedTooltip('stool_urine_in_stool')}`;
    }
    return createIconResult('urine_stool.png', tooltip);
  }

  // Map stool consistency
  if (stool?.consistency) {
    const condition = STOOL_CONDITIONS[stool.consistency] || STOOL_CONDITIONS['normal'];
    return createIconResult(condition.icon, `${getTranslatedTooltip('tooltip_stool_urine')}: ${condition.label()}`);
  }

  return createIconResult('urine_stool_2.png', `${getTranslatedTooltip('tooltip_stool_urine')}: ${getTranslatedTooltip('stool_urine_normal')}`);
}

// Exercise Mapping
export function getExerciseIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('sport.png', getTranslatedTooltip('tooltip_exercise'));
  }

  const { levels, impacts } = value;

  let timeRange = getTranslatedTooltip('tooltip_exercise');
  if (levels?.includes('greater_sixty')) {
    timeRange = `${getTranslatedTooltip('tooltip_exercise')}: ${getTranslatedTooltip('exercise_duration_greater_sixty')}`;
  } else if (levels?.includes('thirty_to_sixty')) {
    timeRange = `${getTranslatedTooltip('tooltip_exercise')}: ${getTranslatedTooltip('exercise_duration_thirty_to_sixty')}`;
  } else if (levels?.includes('less_thirty')) {
    timeRange = `${getTranslatedTooltip('tooltip_exercise')}: ${getTranslatedTooltip('exercise_duration_less_thirty')}`;
  }

  const impactText = impacts?.length > 0 ? ` (${impacts.join(', ')})` : '';
  return createIconResult('sport.png', timeRange + impactText);
}

// Diet Mapping
const DIET_ITEM_LABELS = {
  'vegetables': () => getTranslatedTooltip('tooltip_diet_vegetables'),
  'fruit': () => getTranslatedTooltip('tooltip_diet_fruit'),
  'potato_rice_bread': () => getTranslatedTooltip('tooltip_diet_potato_rice_bread'),
  'dairy': () => getTranslatedTooltip('tooltip_diet_dairy'),
  'nuts_tofu_tempe': () => getTranslatedTooltip('tooltip_diet_nuts_tofu_tempe'),
  'eggs': () => getTranslatedTooltip('tooltip_diet_eggs'),
  'fish': () => getTranslatedTooltip('tooltip_diet_fish'),
  'meat': () => getTranslatedTooltip('tooltip_diet_meat'),
  'snacks': () => getTranslatedTooltip('tooltip_diet_snacks'),
  'soda': () => getTranslatedTooltip('tooltip_diet_soda'),
  'water': () => getTranslatedTooltip('tooltip_diet_water'),
  'coffee': () => getTranslatedTooltip('tooltip_diet_coffee'),
  'alcohol': () => getTranslatedTooltip('tooltip_diet_alcohol')
};

export function getDietIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('diet.png', getTranslatedTooltip('tooltip_diet'));
  }

  const { positives, negatives, neutrals } = value;
  const total = (positives?.length || 0) + (negatives?.length || 0) + (neutrals?.length || 0);

  let tooltip = `${getTranslatedTooltip('tooltip_diet')}: (${total} ${getTranslatedTooltip('tooltip_items_consumed')})`;

  // Add consumed items to tooltip
  const allItems = [...(positives || []), ...(negatives || []), ...(neutrals || [])];
  if (allItems.length > 0) {
    const friendlyItems = allItems.map(item => DIET_ITEM_LABELS[item] ? DIET_ITEM_LABELS[item]() : item);
    tooltip += `\n${getTranslatedTooltip('tooltip_items_consumed')}: ${friendlyItems.join(', ')}`;
  }

  return createIconResult('diet.png', tooltip);
}

// Sex Mapping
export function getSexIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('sex.png', getTranslatedTooltip('tooltip_sexual_activity'));
  }

  const { today, avoided, issues, satisfied } = value;

  let label = `${getTranslatedTooltip('tooltip_sexual_activity')}: `;
  if (avoided) {
    label += getTranslatedTooltip('sex_avoided');
  } else if (today && satisfied) {
    label += getTranslatedTooltip('sex_satisfied');
  } else if (today && issues?.length > 0) {
    label += `${getTranslatedTooltip('sex_with_issues')} (${issues.length})`;
  } else if (today) {
    label += getTranslatedTooltip('tooltip_sexual_activity_recorded');
  } else {
    label += getTranslatedTooltip('tooltip_no_sexual_activity_recorded');
  }

  return createIconResult('sex.png', label);
}

// Sleep Mapping
export function getSleepIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('sleep.png', getTranslatedTooltip('tooltip_sleep'));
  }

  const { calculatedHours, troubleAsleep, wakeUpDuringNight, tiredRested } = value;
  const hours = calculatedHours || 0;

  const issues = [];
  if (troubleAsleep) issues.push(getTranslatedTooltip('sleep_issue_trouble_asleep'));
  if (wakeUpDuringNight) issues.push(getTranslatedTooltip('sleep_issue_wake_up'));
  if (!tiredRested) issues.push(getTranslatedTooltip('sleep_issue_not_tired_rested'));

  let quality = getTranslatedTooltip('sleep_quality_good');
  if (hours >= 7 && hours <= 9 && issues.length === 0) {
    quality = getTranslatedTooltip('sleep_quality_good');
  } else if (hours >= 6 && hours <= 10 && issues.length <= 1) {
    quality = getTranslatedTooltip('sleep_quality_okay');
  } else {
    quality = getTranslatedTooltip('sleep_quality_poor');
  }

  let tooltip = `${getTranslatedTooltip('tooltip_sleep')}: ${quality} (${hours}h)`;
  if (issues.length > 0) {
    tooltip += `\n${getTranslatedTooltip('tooltip_sleep_issues')}: ${issues.join(', ')}`;
  } else {
    tooltip += `\n${getTranslatedTooltip('tooltip_no_sleep_issues')}`;
  }

  return createIconResult('sleep.png', tooltip);
}

// Notes Mapping
export function getNotesIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('grid_notes.png', getTranslatedTooltip('tooltip_note'));
  }

  const noteText = value.text || getTranslatedTooltip('tooltip_note_recorded');
  return createIconResult('grid_notes.png', `${getTranslatedTooltip('tooltip_note')}: ${noteText}`);
}
