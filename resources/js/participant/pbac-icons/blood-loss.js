/**
 * Blood Loss Icon Mapping
 * Handles severity levels (1-5) and special spotting case
 */

import { ICON_BASE_PATH, createIconResult } from './config.js';

const SEVERITY_MAP = {
  'very_light': { icon: 'blood_loss_1.png', label: 'Very Light' },
  'light': { icon: 'blood_loss_2.png', label: 'Light' },
  'moderate': { icon: 'blood_loss_3.png', label: 'Moderate' },
  'heavy': { icon: 'blood_loss_4.png', label: 'Heavy' },
  'very_heavy': { icon: 'blood_loss_5.png', label: 'Very Heavy' },
  'none': { icon: 'no_blood_loss.png', label: 'None' }
};

export function getBloodLossIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('blood_loss.png', 'Blood Loss');
  }
  
  const { severity, spotting, amount } = value;
  
  if (spotting) {
    const tooltip = `Blood Loss: Spotting\nAmount: ${amount || 0}`;
    return createIconResult('spotting.png', tooltip);
  }
  
  const mapping = SEVERITY_MAP[severity] || SEVERITY_MAP['none'];
  const tooltip = `Blood Loss: ${mapping.label}\nAmount: ${amount || 0}`;
  
  return createIconResult(mapping.icon, tooltip);
}
