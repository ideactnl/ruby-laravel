/**
 * Pain Icon Mapping
 * Maps 0-10 pain scale to 6 smile/face icons
 */

import { createIconResult } from './config.js';

const REGION_LABELS = {
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
};

function mapPainValueToIcon(painValue) {
  if (painValue === 0 || painValue === 1) {
    return { iconNumber: 1, label: 'No/Minimal Pain' };
  } else if (painValue === 2) {
    return { iconNumber: 2, label: 'Very Light Pain' };
  } else if (painValue === 3 || painValue === 4) {
    return { iconNumber: 3, label: 'Light-Mild Pain' };
  } else if (painValue === 5 || painValue === 6) {
    return { iconNumber: 4, label: 'Moderate Pain' };
  } else if (painValue === 7 || painValue === 8) {
    return { iconNumber: 5, label: 'Severe Pain' };
  } else {
    return { iconNumber: 6, label: 'Extreme Pain' };
  }
}

export function getPainIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('pain.png', 'Pain');
  }
  
  const painValue = value.value || 0;
  const regions = value.regions || [];
  
  const { iconNumber, label } = mapPainValueToIcon(painValue);
  
  let tooltip = `Pain Level: ${painValue}/10 (${label})`;
  if (regions.length > 0) {
    const regionText = regions.length === 1 ? 'Area' : 'Areas';
    const friendlyRegions = regions.map(region => REGION_LABELS[region] || region);
    tooltip += `\nAffected ${regionText}: ${friendlyRegions.join(', ')}`;
  }
  
  return createIconResult(`smile_${iconNumber}.png`, tooltip);
}
