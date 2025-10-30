/**
 * Mood Icon Mapping
 * Maps specific mood states to corresponding icons
 */

import { createIconResult } from './config.js';

const MOOD_STATES = {
  'calm': { icon: 'mood_1.png' },
  'happy': { icon: 'mood_2.png' },
  'excited': { icon: 'mood_3.png' },
  'anxious_stressed': { icon: 'mood_4.png' },
  'ashamed': { icon: 'mood_5.png' },
  'angry_irritable': { icon: 'mood_6.png' },
  'sad': { icon: 'mood_7.png' },
  'mood_swings': { icon: 'mood_8.png' },
  'worthless_guilty': { icon: 'mood_9.png' },
  'overwhelmed': { icon: 'mood_10.png' },
  'hopeless': { icon: 'mood_11.png' },
  'depressed_sad_down': { icon: 'mood_12.png' }
};

export function getMoodIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('mood.png', 'Mood');
  }

  if (Array.isArray(value.positives) && value.positives.length > 0) {
    const firstPositive = value.positives.find(p => p.value === 1);
    if (firstPositive) {
      const stateKey = firstPositive.key;
      const moodData = MOOD_STATES[stateKey] || MOOD_STATES['calm'];
      return createIconResult(moodData.icon, `Mood: ${stateKey}`);
    }
  }

  if (Array.isArray(value.negatives) && value.negatives.length > 0) {
    const firstNegative = value.negatives.find(n => n.value === 1);
    if (firstNegative) {
      const stateKey = firstNegative.key;
      const moodData = MOOD_STATES[stateKey] || MOOD_STATES['calm'];
      return createIconResult(moodData.icon, `Mood: ${stateKey}`);
    }
  }

  const balance = (value.positives?.length || 0) - (value.negatives?.length || 0);
  let primaryMood;
  if (balance > 2) primaryMood = 'happy';
  else if (balance > 0) primaryMood = 'excited';
  else if (balance === 0) primaryMood = 'calm';
  else if (balance > -3) primaryMood = 'sad';
  else primaryMood = 'depressed_sad_down';

  const moodData = MOOD_STATES[primaryMood] || MOOD_STATES['calm'];
  return createIconResult(moodData.icon, `Mood: ${primaryMood}`);
}
