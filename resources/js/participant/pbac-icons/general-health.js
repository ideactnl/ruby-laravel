/**
 * General Health Icon Mapping
 * Maps energy levels 1-5 to specific icons
 */

import { createIconResult } from './config.js';

const SYMPTOM_LABELS = {
  'dizzy': 'Dizzy',
  'nauseous': 'Nauseous',
  'headache_migraine': 'Headache/Migraine',
  'bloated': 'Bloated',
  'painful_sensitive_breasts': 'Painful/Sensitive Breasts',
  'acne': 'Acne',
  'muscle_joint_pain': 'Muscle/Joint Pain'
};

const ENERGY_MAP = {
  1: { icon: 'sleep.png' },
  2: { icon: 'general_health_1.png' },
  3: { icon: 'general_health_2.png' },
  4: { icon: 'general_health_3.png' },
  5: { icon: 'general_health_4.png' }
};

export function getGeneralHealthIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('general_health.png', 'General Health');
  }

  const energyLevel = value.energyLevel || 1;
  const symptoms = value.symptoms || [];

  const mapping = ENERGY_MAP[energyLevel] || ENERGY_MAP[1];

  let tooltip = `Energy Level: ${energyLevel}/5`;

  if (symptoms.length > 0) {
    const friendlySymptoms = symptoms.map(symptom =>
      SYMPTOM_LABELS[symptom] || symptom
    );
    tooltip += `\nSymptoms: ${friendlySymptoms.join(', ')}`;
  } else {
    tooltip += `\nNo symptoms reported`;
  }

  return createIconResult(mapping.icon, tooltip);
}
