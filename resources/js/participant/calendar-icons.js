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
      
    case 'impact':
      return getImpactIcon(value, baseSpec);
      
    case 'general_health':
      return getGeneralHealthIcon(value, baseSpec);
      
    case 'mood':
      return getMoodIcon(value, baseSpec);
      
    case 'stool_urine':
      return getStoolUrineIcon(value, baseSpec);
      
    case 'sleep':
      return getSleepIcon(value, baseSpec);
      
    case 'exercise':
      return getExerciseIcon(value, baseSpec);
      
    case 'diet':
      return getDietIcon(value, baseSpec);
      
    case 'sex':
      return getSexIcon(value, baseSpec);
      
    case 'notes':
      return getNotesIcon(value, baseSpec);
      
    default:
      return { iconClass: baseSpec.cls, tooltip: `${value}` };
  };
}

/**
 * Blood Loss - Severity-based droplet colors
 */
function getBloodLossIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const amount = value.amount || 0;
  const severity = value.severity || 'none';
  const spotting = value.spotting;
  
  let iconClass, tooltip;
  if (spotting) {
    iconClass = 'fa-droplet text-red-400';
    tooltip = `Blood Loss: Spotting (${amount})`;
  } else {
    // Use backend severity directly (user-selected, not threshold-based)
    switch(severity) {
      case 'very_heavy':
        iconClass = 'fa-droplet text-red-900';
        tooltip = `Blood Loss: Very Heavy (${amount})`;
        break;
      case 'heavy':
        iconClass = 'fa-droplet text-red-700';
        tooltip = `Blood Loss: Heavy (${amount})`;
        break;
      case 'moderate':
        iconClass = 'fa-droplet text-red-500';
        tooltip = `Blood Loss: Moderate (${amount})`;
        break;
      case 'light':
        iconClass = 'fa-droplet text-red-400';
        tooltip = `Blood Loss: Light (${amount})`;
        break;
      case 'very_light':
        iconClass = 'fa-droplet text-red-300';
        tooltip = `Blood Loss: Very Light (${amount})`;
        break;
      default: // 'none'
        iconClass = 'fa-droplet text-gray-400';
        tooltip = `Blood Loss: None (${amount})`;
    }
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
 * General Health - Battery level based on energy
 */
function getGeneralHealthIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const energyLevel = value.energyLevel || 0;
  const symptoms = value.symptoms || [];
  
  let iconClass, tooltip;
  if (energyLevel >= 8) {
    iconClass = 'fa-battery-full text-green-500';
    tooltip = `Energy: High (${energyLevel}/10)`;
  } else if (energyLevel >= 6) {
    iconClass = 'fa-battery-three-quarters text-green-400';
    tooltip = `Energy: Good (${energyLevel}/10)`;
  } else if (energyLevel >= 4) {
    iconClass = 'fa-battery-half text-yellow-500';
    tooltip = `Energy: Moderate (${energyLevel}/10)`;
  } else if (energyLevel >= 2) {
    iconClass = 'fa-battery-quarter text-orange-500';
    tooltip = `Energy: Low (${energyLevel}/10)`;
  } else {
    iconClass = 'fa-battery-empty text-red-500';
    tooltip = `Energy: Very Low (${energyLevel}/10)`;
  }
  
  if (symptoms.length > 0) {
    tooltip += ` - ${symptoms.length} symptom(s)`;
  }
  return { iconClass, tooltip };
}

/**
 * Mood - Face based on positive/negative balance
 */
function getMoodIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const positives = value.positives || [];
  const negatives = value.negatives || [];
  const balance = positives.length - negatives.length;
  
  let iconClass, tooltip;
  if (balance > 2) {
    iconClass = 'fa-face-grin text-green-500';
    tooltip = `Mood: Very Positive`;
  } else if (balance > 0) {
    iconClass = 'fa-face-smile text-green-400';
    tooltip = `Mood: Positive`;
  } else if (balance === 0) {
    iconClass = 'fa-face-meh text-yellow-500';
    tooltip = `Mood: Neutral`;
  } else if (balance > -3) {
    iconClass = 'fa-face-frown text-orange-500';
    tooltip = `Mood: Negative`;
  } else {
    iconClass = 'fa-face-sad-tear text-red-500';
    tooltip = `Mood: Very Negative`;
  }
  
  tooltip += ` (+${positives.length}/-${negatives.length})`;
  return { iconClass, tooltip };
}

/**
 * Stool/Urine - Toilet icon with issue indicators
 */
function getStoolUrineIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const urine = value.urine || {};
  const stool = value.stool || {};
  const hasBlood = urine.blood || stool.blood;
  const hasIssues = Object.keys(urine).length > 0 || Object.keys(stool).length > 0;
  
  let iconClass, tooltip;
  if (hasBlood) {
    iconClass = 'fa-toilet text-red-500';
    tooltip = `Stool/Urine: Blood detected`;
  } else if (hasIssues) {
    iconClass = 'fa-toilet text-yellow-500';
    tooltip = `Stool/Urine: Issues noted`;
  } else {
    iconClass = 'fa-toilet text-green-500';
    tooltip = `Stool/Urine: Normal`;
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
 * Diet - Utensils with positive/negative balance
 */
function getDietIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const positives = value.positives || [];
  const negatives = value.negatives || [];
  const neutrals = value.neutrals || [];
  const total = positives.length + negatives.length + neutrals.length;
  
  let iconClass, tooltip;
  if (negatives.length === 0 && positives.length > 0) {
    iconClass = 'fa-utensils text-green-500';
    tooltip = `Diet: Healthy (${total} items)`;
  } else if (negatives.length <= positives.length) {
    iconClass = 'fa-utensils text-yellow-500';
    tooltip = `Diet: Mixed (${total} items)`;
  } else {
    iconClass = 'fa-utensils text-red-500';
    tooltip = `Diet: Concerning (${total} items)`;
  }
  
  return { iconClass, tooltip };
}

/**
 * Sex - Heart with satisfaction indicators
 */
function getSexIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const today = value.today;
  const avoided = value.avoided;
  const issues = value.issues || [];
  const satisfied = value.satisfied;
  
  let iconClass, tooltip;
  if (avoided) {
    iconClass = 'fa-heart-crack text-red-500';
    tooltip = `Sex: Avoided due to pain`;
  } else if (today && satisfied) {
    iconClass = 'fa-heart text-green-500';
    tooltip = `Sex: Satisfying experience`;
  } else if (today && issues.length > 0) {
    iconClass = 'fa-heart text-yellow-500';
    tooltip = `Sex: With issues (${issues.length})`;
  } else if (today) {
    iconClass = 'fa-heart text-blue-500';
    tooltip = `Sex: Activity recorded`;
  } else {
    iconClass = 'fa-heart text-gray-400';
    tooltip = `Sex: No activity`;
  }
  
  return { iconClass, tooltip };
}

/**
 * Notes - Show actual note text
 */
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

function getNotesIcon(value, baseSpec) {
  if (typeof value !== 'object') return { iconClass: baseSpec.cls, tooltip: `${baseSpec.label}: ${value}` };
  
  const noteText = value.text || 'Note recorded';
  return { 
    iconClass: baseSpec.cls, 
    tooltip: `Note: ${noteText}` 
  };
}
