/**
 * Calendar Icon and Tooltip Utilities
 * 
 * Handles dynamic icon selection and tooltip generation for calendar events
 * based on pillar data and severity levels.
 */

// Base icon specifications for each pillar type
export const PILLAR_ICONS = {
  blood_loss: { cls: 'fa-droplet text-red-600', label: 'Blood Loss' },
  pain: { cls: 'fa-burst text-amber-500', label: 'Pain' },
  impact: { cls: 'fa-heart-pulse text-green-500', label: 'Impact' },
  general_health: { cls: 'fa-battery-half text-emerald-500', label: 'General Health' },
  mood: { cls: 'fa-face-smile text-violet-500', label: 'Mood' },
  stool_urine: { cls: 'fa-toilet text-sky-500', label: 'Stool/Urine' },
  sleep: { cls: 'fa-bed text-indigo-500', label: 'Sleep' },
  exercise: { cls: 'fa-person-running text-orange-400', label: 'Exercise' },
  diet: { cls: 'fa-utensils text-yellow-500', label: 'Diet' },
  sex: { cls: 'fa-venus-mars text-pink-400', label: 'Sexual Health' },
  notes: { cls: 'fa-note-sticky text-slate-500', label: 'Notes' },
};

/**
 * Generate dynamic icon class and tooltip based on pillar type and data
 * @param {string} type - The pillar type (blood_loss, pain, etc.)
 * @param {*} value - The pillar data (object or primitive)
 * @returns {Object} { iconClass, tooltip }
 */
export function getDynamicIconAndTooltip(type, value) {
  const baseSpec = PILLAR_ICONS[type] || { cls: 'fa-circle text-gray-400', label: type };
  
  switch(type) {
    case 'blood_loss':
      return getBloodLossIcon(value, baseSpec);
      
    case 'pain':
      return getPainIcon(value, baseSpec);
      
    case 'exercise':
      return getExerciseIcon(value, baseSpec);
      
    case 'notes':
      return getNotesIcon(value, baseSpec);
      
    case 'mood':
      return getMoodIcon(value, baseSpec);
      
    case 'impact':
      return getImpactIcon(value, baseSpec);
      
    case 'general_health':
      return getGeneralHealthIcon(value, baseSpec);
      
    case 'stool_urine':
      return getStoolUrineIcon(value, baseSpec);
      
    case 'sleep':
      return getSleepIcon(value, baseSpec);
      
    case 'diet':
      return getDietIcon(value, baseSpec);
      
    case 'sex':
      return getSexIcon(value, baseSpec);
  }
  
  // Default fallback for simple values
  return { 
    iconClass: baseSpec.cls, 
    tooltip: `${baseSpec.label}: ${value}` 
  };
}

/**
 * Blood Loss - Severity-based droplet colors
 */
function getBloodLossIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const amount = value.amount || 0;
  const severity = value.severity || 'light';
  const spotting = value.spotting;
  
  let iconClass, tooltip;
  if (spotting) {
    iconClass = 'fa-droplet text-red-400';
    tooltip = `Blood Loss: Spotting (${amount})`;
  } else if (severity === 'heavy' || amount > 20) {
    iconClass = 'fa-droplet text-red-700';
    tooltip = `Blood Loss: Heavy (${amount})`;
  } else if (severity === 'moderate' || amount > 10) {
    iconClass = 'fa-droplet text-red-500';
    tooltip = `Blood Loss: Moderate (${amount})`;
  } else {
    iconClass = 'fa-droplet text-red-300';
    tooltip = `Blood Loss: Light (${amount})`;
  }
  return { iconClass, tooltip };
}

/**
 * Pain - Face emojis based on pain level
 */
function getPainIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const painValue = value.value || 0;
  const regions = value.regions || [];
  
  let iconClass, tooltip;
  if (painValue >= 8) {
    iconClass = 'fa-face-dizzy text-red-600'; // 😵
    tooltip = `Pain: Severe (${painValue}/10)`;
  } else if (painValue >= 6) {
    iconClass = 'fa-face-frown text-orange-500'; // 😞
    tooltip = `Pain: Moderate (${painValue}/10)`;
  } else if (painValue >= 4) {
    iconClass = 'fa-face-meh text-yellow-500'; // 😐
    tooltip = `Pain: Mild (${painValue}/10)`;
  } else {
    iconClass = 'fa-face-smile text-green-500'; // 🙂
    tooltip = `Pain: Light (${painValue}/10)`;
  }
  
  if (regions.length > 0) {
    tooltip += ` - ${regions.join(', ')}`;
  }
  return { iconClass, tooltip };
}

/**
 * Exercise - Time range display
 */
function getExerciseIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const levels = value.levels || [];
  const impacts = value.impacts || [];
  
  let timeRange = 'Exercise';
  if (levels.includes('greater_sixty')) {
    timeRange = 'Exercise: >60 min';
  } else if (levels.includes('thirty_to_sixty')) {
    timeRange = 'Exercise: 30-60 min';
  } else if (levels.includes('less_thirty')) {
    timeRange = 'Exercise: <30 min';
  }
  
  const impactText = impacts.length > 0 ? ` (${impacts.join(', ')})` : '';
  return { 
    iconClass: baseSpec.cls, 
    tooltip: timeRange + impactText 
  };
}

/**
 * Notes - Show actual note text
 */
function getNotesIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const noteText = value.text || 'Note recorded';
  return { 
    iconClass: baseSpec.cls, 
    tooltip: `Note: ${noteText}` 
  };
}

/**
 * Mood - Face emojis based on positive/negative balance
 */
function getMoodIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const positives = value.positives || [];
  const negatives = value.negatives || [];
  const totalCount = positives.length + negatives.length;
  
  let iconClass, tooltip;
  if (negatives.length > positives.length) {
    iconClass = 'fa-face-sad-tear text-blue-500'; // 😢
    tooltip = `Mood: Challenging (${negatives.length} negative, ${positives.length} positive)`;
  } else if (positives.length > negatives.length) {
    iconClass = 'fa-face-grin text-green-500'; // 😁
    tooltip = `Mood: Good (${positives.length} positive, ${negatives.length} negative)`;
  } else if (totalCount > 0) {
    iconClass = 'fa-face-meh text-yellow-500'; // 😐
    tooltip = `Mood: Mixed (${positives.length} positive, ${negatives.length} negative)`;
  } else {
    iconClass = 'fa-face-smile text-gray-500'; // 🙂
    tooltip = 'Mood: Neutral';
  }
  return { iconClass, tooltip };
}

/**
 * Impact - Heart icons based on grade your day
 */
function getImpactIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const grade = value.gradeYourDay || 0;
  const limitations = value.limitations || [];
  const medications = value.medications;
  
  let iconClass, tooltip;
  if (grade >= 8) {
    iconClass = 'fa-heart-pulse text-green-600'; // Great day
    tooltip = `Impact: Great Day (${grade}/10)`;
  } else if (grade >= 6) {
    iconClass = 'fa-heart text-green-400'; // Good day
    tooltip = `Impact: Good Day (${grade}/10)`;
  } else if (grade >= 4) {
    iconClass = 'fa-heart-crack text-yellow-500'; // Okay day
    tooltip = `Impact: Okay Day (${grade}/10)`;
  } else {
    iconClass = 'fa-heart-circle-xmark text-red-500'; // Difficult day
    tooltip = `Impact: Difficult Day (${grade}/10)`;
  }
  
  if (limitations.length > 0) {
    tooltip += ` - ${limitations.length} limitation(s)`;
  }
  if (medications?.used) {
    tooltip += ` - Used medication`;
  }
  return { iconClass, tooltip };
}

/**
 * General Health - Battery icons based on energy level
 */
function getGeneralHealthIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const energy = value.energyLevel || 0;
  const symptoms = value.symptoms || [];
  
  let iconClass, tooltip;
  if (energy >= 8) {
    iconClass = 'fa-battery-full text-green-600'; // High energy
    tooltip = `Energy: High (${energy}/10)`;
  } else if (energy >= 6) {
    iconClass = 'fa-battery-three-quarters text-green-400'; // Good energy
    tooltip = `Energy: Good (${energy}/10)`;
  } else if (energy >= 4) {
    iconClass = 'fa-battery-half text-yellow-500'; // Medium energy
    tooltip = `Energy: Medium (${energy}/10)`;
  } else {
    iconClass = 'fa-battery-quarter text-red-500'; // Low energy
    tooltip = `Energy: Low (${energy}/10)`;
  }
  
  if (symptoms.length > 0) {
    tooltip += ` - ${symptoms.length} symptom(s)`;
  }
  return { iconClass, tooltip };
}

/**
 * Stool/Urine - Color-coded based on issues
 */
function getStoolUrineIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const urineBlood = value.urine?.blood;
  const stoolBlood = value.stool?.blood;
  const issues = [];
  
  if (urineBlood) issues.push('blood in urine');
  if (stoolBlood) issues.push('blood in stool');
  
  let iconClass = 'fa-toilet text-sky-500';
  if (issues.length > 1) {
    iconClass = 'fa-toilet text-red-500'; // Multiple issues
  } else if (issues.length === 1) {
    iconClass = 'fa-toilet text-orange-500'; // Single issue
  }
  
  const tooltip = issues.length > 0 ? 
    `Stool/Urine: ${issues.join(', ')}` : 
    'Stool/Urine: Issues noted';
  return { iconClass, tooltip };
}

/**
 * Sleep - Color-coded based on sleep duration and quality
 */
function getSleepIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const hours = value.calculatedHours || 0;
  const troubleAsleep = value.troubleAsleep;
  const wakeUpDuringNight = value.wakeUpDuringNight;
  const tiredRested = value.tiredRested;
  
  let iconClass, tooltip;
  const issues = [];
  if (troubleAsleep) issues.push('trouble falling asleep');
  if (wakeUpDuringNight) issues.push('woke up during night');
  if (!tiredRested) issues.push('not well rested');
  
  // Color based on sleep duration and quality
  if (hours >= 7 && hours <= 9 && issues.length === 0) {
    iconClass = 'fa-bed text-green-500'; // Good sleep
    tooltip = `Sleep: Good (${hours}h)`;
  } else if (hours >= 6 && hours <= 10 && issues.length <= 1) {
    iconClass = 'fa-bed text-yellow-500'; // Okay sleep
    tooltip = `Sleep: Okay (${hours}h)`;
  } else {
    iconClass = 'fa-bed text-red-500'; // Poor sleep
    tooltip = `Sleep: Poor (${hours}h)`;
  }
  
  if (issues.length > 0) {
    tooltip += ` - ${issues.join(', ')}`;
  }
  return { iconClass, tooltip };
}

/**
 * Diet - Color-coded based on healthy vs unhealthy items
 */
function getDietIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const positives = value.positives || [];
  const negatives = value.negatives || [];
  const neutrals = value.neutrals || [];
  const total = positives.length + negatives.length + neutrals.length;
  
  let iconClass, tooltip;
  if (positives.length > negatives.length) {
    iconClass = 'fa-utensils text-green-500'; // Healthy diet
    tooltip = `Diet: Healthy (${positives.length} good, ${negatives.length} poor items)`;
  } else if (negatives.length > positives.length) {
    iconClass = 'fa-utensils text-red-500'; // Poor diet
    tooltip = `Diet: Poor (${negatives.length} poor, ${positives.length} good items)`;
  } else {
    iconClass = 'fa-utensils text-yellow-500'; // Mixed diet
    tooltip = `Diet: Mixed (${total} items total)`;
  }
  return { iconClass, tooltip };
}

/**
 * Sexual Health - Status-based colors
 */
function getSexIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const today = value.today;
  const avoided = value.avoided;
  const issues = value.issues || [];
  const satisfied = value.satisfied;
  
  let iconClass, tooltip;
  if (avoided) {
    iconClass = 'fa-venus-mars text-gray-500'; // Avoided
    tooltip = 'Sexual Health: Avoided intimacy';
  } else if (issues.length > 0) {
    iconClass = 'fa-venus-mars text-orange-500'; // Issues
    tooltip = `Sexual Health: ${issues.length} issue(s) noted`;
  } else if (satisfied) {
    iconClass = 'fa-venus-mars text-green-500'; // Satisfied
    tooltip = 'Sexual Health: Satisfied';
  } else {
    iconClass = 'fa-venus-mars text-pink-400'; // Active
    tooltip = 'Sexual Health: Active';
  }
  return { iconClass, tooltip };
}
