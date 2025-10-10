/**
 * Impact Icon Mapping
 * Maps boolean impact conditions to specific icons
 */

import { createIconResult } from './config.js';

const CONDITIONS = {
  'used_medication': { icon: 'impact_1.png', label: 'Used Medication' },
  'missed_work': { icon: 'impact_2.png', label: 'Missed Work' },
  'missed_school': { icon: 'impact_3.png', label: 'Missed School' },
  'could_not_sport': { icon: 'impact_4.png', label: 'Could Not Sport' },
  'missed_social_activities': { icon: 'impact_5.png', label: 'Missed Social Activities' },
  'missed_leisure_activities': { icon: 'impact_6.png', label: 'Missed Leisure Activities' },
  'had_to_sit_more': { icon: 'impact_7.png', label: 'Had to Sit More' },
  'had_to_lie_down': { icon: 'impact_8.png', label: 'Had to Lie Down' },
  'had_to_stay_longer_in_bed': { icon: 'impact_9.png', label: 'Had to Stay Longer in Bed' },
  'could_not_do_unpaid_work': { icon: 'impact_10.png', label: 'Could Not Do Unpaid Work' },
  'other': { icon: 'impact_11.png', label: 'Other Impact' }
};

const GRADE_ICONS = {
  high: 'impact_1.png',     // 8-10
  good: 'impact_2.png',     // 6-7
  okay: 'impact_3.png',     // 4-5
  difficult: 'impact_4.png' // 0-3
};

function getGradeCategory(grade) {
  if (grade >= 8) return { category: 'Great Day', icon: GRADE_ICONS.high };
  if (grade >= 6) return { category: 'Good Day', icon: GRADE_ICONS.good };
  if (grade >= 4) return { category: 'Okay Day', icon: GRADE_ICONS.okay };
  return { category: 'Difficult Day', icon: GRADE_ICONS.difficult };
}

export function getImpactIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('impact.png', 'Impact');
  }
  
  const { gradeYourDay, limitations } = value;
  
  if (limitations && limitations.length > 0) {
    const primaryLimitation = limitations[0];
    const condition = CONDITIONS[primaryLimitation];
    
    if (condition) {
      let tooltip = `Daily Impact: ${gradeYourDay}/10`;
      tooltip += `\nPrimary Issue: ${condition.label}`;
      if (limitations.length > 1) {
        tooltip += `\nAdditional Issues: ${limitations.length - 1} more`;
      }
      
      return createIconResult(condition.icon, tooltip);
    }
  }
  
  const { category, icon } = getGradeCategory(gradeYourDay);
  const tooltip = `Daily Impact: ${gradeYourDay}/10 (${category})`;
  
  return createIconResult(icon, tooltip);
}
