/**
 * Calendar Data Processing Utilities
 * 
 * Handles extraction and transformation of pillar data for calendar events.
 */

/**
 * Build calendar events from PBAC rows with rich pillar data
 * @param {Array} rows - Array of PBAC records with pillar data
 * @param {Set} selected - Set of selected pillar types to display
 * @returns {Array} Array of FullCalendar event objects
 */
export function buildEventsFromRows(rows, selected) {
  const evts = [];
  const pushIf = (date, cond, type, value) => {
    if (cond && selected.has(type)) {
      evts.push({ start: date, allDay: true, display: 'list-item', extendedProps: { type, value } });
    }
  };

  for (const r of rows) {
    const date = r.reported_date;
    const pillars = r.pillars || {};
    
    // Blood Loss - pass full data for severity-based icons
    if ((pillars.blood_loss?.amount ?? 0) > 0) {
      pushIf(date, true, 'blood_loss', {
        amount: pillars.blood_loss.amount,
        severity: pillars.blood_loss.severity,
        spotting: pillars.blood_loss.flags?.spotting
      });
    }
    
    // Pain - pass value for custom icon selection
    if ((pillars.pain?.value ?? 0) > 0) {
      pushIf(date, true, 'pain', {
        value: pillars.pain.value,
        regions: pillars.pain.regions || []
      });
    }
    
    // Exercise - pass levels for time range display
    if (pillars.exercise?.any ?? false) {
      pushIf(date, true, 'exercise', {
        levels: pillars.exercise.levels || [],
        impacts: pillars.exercise.impacts || []
      });
    }
    
    // Notes - pass actual note text
    if (pillars.notes?.hasNote ?? false) {
      pushIf(date, true, 'notes', {
        text: pillars.notes.text || 'Note recorded',
        hasNote: true
      });
    }
    
    // Impact - pass grade and limitations
    if ((pillars.impact?.gradeYourDay ?? 0) > 0) {
      pushIf(date, true, 'impact', {
        gradeYourDay: pillars.impact.gradeYourDay,
        limitations: pillars.impact.limitations || [],
        medications: pillars.impact.medications
      });
    }
    
    // General Health - pass energy level and symptoms (show if answered)
    if (pillars.general_health?.answered) {
      pushIf(date, true, 'general_health', {
        energyLevel: pillars.general_health.energyLevel,
        symptoms: pillars.general_health.symptoms || []
      });
    }
    
    // Mood - pass positive and negative indicators (show if answered)
    if (pillars.mood?.answered) {
      pushIf(date, true, 'mood', {
        positives: pillars.mood.positives || [],
        negatives: pillars.mood.negatives || []
      });
    }
    
    // Stool/Urine - pass detailed info (show if answered)
    if (pillars.stool_urine?.answered) {
      pushIf(date, true, 'stool_urine', {
        urine: pillars.stool_urine.urine || {},
        stool: pillars.stool_urine.stool || {}
      });
    }
    
    // Sleep - pass sleep hours and quality indicators (show if answered)
    if (pillars.sleep?.answered) {
      pushIf(date, true, 'sleep', {
        calculatedHours: pillars.sleep.calculatedHours,
        fellAsleepTime: pillars.sleep.fellAsleepTime,
        wokeUpTime: pillars.sleep.wokeUpTime,
        troubleAsleep: pillars.sleep.troubleAsleep,
        tiredRested: pillars.sleep.tiredRested,
        wakeUpDuringNight: pillars.sleep.wakeUpDuringNight
      });
    }
    
    // Diet - pass positive, negative, and neutral items
    if ((pillars.diet?.positives?.length ?? 0) > 0 || (pillars.diet?.negatives?.length ?? 0) > 0 || 
        (pillars.diet?.neutrals?.length ?? 0) > 0) {
      pushIf(date, true, 'diet', {
        positives: pillars.diet.positives || [],
        negatives: pillars.diet.negatives || [],
        neutrals: pillars.diet.neutrals || []
      });
    }
    
    // Sex - pass detailed info
    if (pillars.sex?.today ?? false) {
      pushIf(date, true, 'sex', {
        today: pillars.sex.today,
        avoided: pillars.sex.avoided,
        issues: pillars.sex.issues || [],
        satisfied: pillars.sex.satisfied
      });
    }
  }
  return evts;
}
