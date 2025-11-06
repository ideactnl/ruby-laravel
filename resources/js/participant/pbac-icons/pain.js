/**
 * Pain Icon Mapping
 * Maps 0-10 pain scale to 6 smile/face icons
 */

import { createIconResult, getTranslatedTooltip } from './config.js';

const REGION_LABELS = {
  'umbilical': () => getTranslatedTooltip('tooltip_region_umbilical'),
  'left_umbilical': () => getTranslatedTooltip('tooltip_region_left_umbilical'),
  'right_umbilical': () => getTranslatedTooltip('tooltip_region_right_umbilical'),
  'bladder': () => getTranslatedTooltip('tooltip_region_bladder'),
  'left_groin': () => getTranslatedTooltip('tooltip_region_left_groin'),
  'right_groin': () => getTranslatedTooltip('tooltip_region_right_groin'),
  'left_leg': () => getTranslatedTooltip('tooltip_region_left_leg'),
  'right_leg': () => getTranslatedTooltip('tooltip_region_right_leg'),
  'upper_back': () => getTranslatedTooltip('tooltip_region_upper_back'),
  'back': () => getTranslatedTooltip('tooltip_region_back'),
  'left_buttock': () => getTranslatedTooltip('tooltip_region_left_buttock'),
  'right_buttock': () => getTranslatedTooltip('tooltip_region_right_buttock'),
  'left_back_leg': () => getTranslatedTooltip('tooltip_region_left_back_leg'),
  'right_back_leg': () => getTranslatedTooltip('tooltip_region_right_back_leg')
};

function mapPainValueToIcon(painValue) {
  if (painValue === 0 || painValue === 1) return 1;
  if (painValue === 2) return 2;
  if (painValue === 3 || painValue === 4) return 3;
  if (painValue === 5 || painValue === 6) return 4;
  if (painValue === 7 || painValue === 8) return 5;
  return 6; // 9–10
}

export function getPainIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('pain.png', getTranslatedTooltip('tooltip_pain'));
  }

  const painValue = value.value || 0;
  const regions = value.regions || [];
  const iconNumber = mapPainValueToIcon(painValue);

  let tooltip = `${getTranslatedTooltip('tooltip_pain_level')}: ${painValue}/10`;
  if (regions.length > 0) {
    const friendlyRegions = regions.map(r => REGION_LABELS[r] ? REGION_LABELS[r]() : r);
    tooltip += `\n${getTranslatedTooltip('tooltip_regions')}: ${friendlyRegions.join(', ')}`;
  }

  return createIconResult(`smile_${iconNumber}.png`, tooltip);
}
