import { ICON_BASE_PATH, createIconResult } from './config.js';

const SEVERITY_MAP = {
  'very_light': { icon: 'blood_loss_1.png' },
  'light': { icon: 'blood_loss_2.png' },
  'moderate': { icon: 'blood_loss_3.png' },
  'heavy': { icon: 'blood_loss_4.png' },
  'very_heavy': { icon: 'blood_loss_5.png' },
  'none': { icon: 'no_blood_loss.png' }
};

export function getBloodLossIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('blood_loss.png', 'Blood Loss');
  }

  const { severity, spotting, amount } = value;

  if (spotting) {
    const tooltip = `Blood Loss Amount: ${amount || 0}\nSpotting Detected`;
    return createIconResult('spotting.png', tooltip);
  }

  const mapping = SEVERITY_MAP[severity] || SEVERITY_MAP['none'];
  const tooltip = `Blood Loss Amount: ${amount || 0}`;

  return createIconResult(mapping.icon, tooltip);
}
