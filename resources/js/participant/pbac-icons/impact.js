/**
 * Impact Icon Mapping
 * Maps boolean impact conditions to specific icons
 */

import { createIconResult } from './config.js';

const CONDITIONS = {
  'used_medication': { icon: 'impact_1.png' },
  'missed_work': { icon: 'impact_2.png' },
  'missed_school': { icon: 'impact_3.png' },
  'could_not_sport': { icon: 'impact_4.png' },
  'missed_social_activities': { icon: 'impact_5.png' },
  'missed_leisure_activities': { icon: 'impact_6.png' },
  'had_to_sit_more': { icon: 'impact_7.png' },
  'had_to_lie_down': { icon: 'impact_8.png' },
  'had_to_stay_longer_in_bed': { icon: 'impact_9.png' },
  'could_not_do_unpaid_work': { icon: 'impact_10.png' },
  'other': { icon: 'impact_11.png' }
};

const GRADE_ICONS = {
  high: 'impact_1.png',     // 8-10
  good: 'impact_2.png',     // 6-7
  okay: 'impact_3.png',     // 4-5
  difficult: 'impact_4.png' // 0-3
};

function getGradeCategory(grade) {
  if (grade >= 8) return { icon: GRADE_ICONS.high };
  if (grade >= 6) return { icon: GRADE_ICONS.good };
  if (grade >= 4) return { icon: GRADE_ICONS.okay };
  return { icon: GRADE_ICONS.difficult };
}

export function getImpactIcon(value) {
  if (typeof value !== 'object') {
    return createIconResult('impact.png', `Daily Impact`);
  }

  const { gradeYourDay, limitations } = value;

  if (limitations && limitations.length > 0) {
    const primaryLimitation = limitations[0];
    const condition = CONDITIONS[primaryLimitation];
    if (condition) {
      const tooltip = `Daily Impact: ${gradeYourDay}/10`;
      return createIconResult(condition.icon, tooltip);
    }
  }

  const { icon } = getGradeCategory(gradeYourDay);
  const tooltip = `Daily Impact: ${gradeYourDay}/10`;

  return createIconResult(icon, tooltip);
}
