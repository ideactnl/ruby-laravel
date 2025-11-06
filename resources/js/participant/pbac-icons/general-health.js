/**
 * General Health Icon Mapping
 * Maps energy levels 1-5 to specific icons
 */

import { createIconResult, getTranslatedTooltip } from './config.js';

const SYMPTOM_LABELS = {
  'dizzy': () => getTranslatedTooltip('tooltip_symptom_dizzy'),
  'nauseous': () => getTranslatedTooltip('tooltip_symptom_nauseous'),
  'headache_migraine': () => getTranslatedTooltip('tooltip_symptom_headache_migraine'),
  'bloated': () => getTranslatedTooltip('tooltip_symptom_bloated'),
  'painful_sensitive_breasts': () => getTranslatedTooltip('tooltip_symptom_painful_sensitive_breasts'),
  'acne': () => getTranslatedTooltip('tooltip_symptom_acne'),
  'muscle_joint_pain': () => getTranslatedTooltip('tooltip_symptom_muscle_joint_pain')
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
    return createIconResult('general_health.png', getTranslatedTooltip('tooltip_general_health'));
  }

  const energyLevel = value.energyLevel || 1;
  const symptoms = value.symptoms || [];

  const mapping = ENERGY_MAP[energyLevel] || ENERGY_MAP[1];

  let tooltip = `${getTranslatedTooltip('tooltip_energy_level')}: ${energyLevel}/5`;

  if (symptoms.length > 0) {
    const friendlySymptoms = symptoms.map(symptom =>
      SYMPTOM_LABELS[symptom] ? SYMPTOM_LABELS[symptom]() : symptom
    );
    tooltip += `\n${getTranslatedTooltip('tooltip_symptoms')}: ${friendlySymptoms.join(', ')}`;
  } else {
    tooltip += `\n${getTranslatedTooltip('tooltip_no_symptoms')}`;
  }

  return createIconResult(mapping.icon, tooltip);
}
