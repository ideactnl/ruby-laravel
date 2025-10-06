/**
 * Mood Icon Mapping
 * Maps specific mood states to corresponding icons
 */

import { createIconResult } from './config.js';

const MOOD_STATES = {
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
};

const STATE_LABELS = {
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
};

function determinePrimaryMood(balance) {
  if (balance > 2) return 'happy';
  if (balance > 0) return 'excited';
  if (balance === 0) return 'calm';
  if (balance > -3) return 'sad';
  return 'depressed_sad_down';
}

export function getMoodIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('mood.png', 'Mood');
  }
  
  const { positives, negatives } = value;
  const balance = (positives?.length || 0) - (negatives?.length || 0);
  
  const primaryMood = determinePrimaryMood(balance);
  const moodData = MOOD_STATES[primaryMood] || MOOD_STATES['calm'];
  
  let tooltip = `Mood: ${moodData.label}`;
  
  if (positives?.length > 0) {
    const friendlyPositives = positives.map(state => STATE_LABELS[state] || state);
    tooltip += `\nPositive: ${friendlyPositives.join(', ')}`;
  }
  
  if (negatives?.length > 0) {
    const friendlyNegatives = negatives.map(state => STATE_LABELS[state] || state);
    tooltip += `\nNegative: ${friendlyNegatives.join(', ')}`;
  }
  
  if (balance > 0) {
    tooltip += `\nOverall: Positive day`;
  } else if (balance < 0) {
    tooltip += `\nOverall: Challenging day`;
  } else {
    tooltip += `\nOverall: Balanced day`;
  }
  
  return createIconResult(moodData.icon, tooltip);
}
