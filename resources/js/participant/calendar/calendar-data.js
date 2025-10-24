/**
 * Calendar Data Processing Utilities
 * 
 * Handles extraction and transformation of pillar data for calendar events.
 */

/**
 * Build calendar events from PBAC rows with rich pillar data
 * @param {Array} rows - Array of PBAC report rows with pillar data
 * @param {Set} selected - Set of selected pillar types to include
 * @returns {Array} Array of calendar event objects
 */
export function buildEventsFromRows(rows, selected) {
  const evts = [];
  const datesSeen = new Set();
  const pushIf = (date, cond, type, value, isPlaceholder = false) => {
    if (selected.has(type)) {
      if (cond || isPlaceholder) {
        evts.push({
          start: date,
          allDay: true,
          display: 'list-item',
          extendedProps: { type, value, isPlaceholder }
        });
      }
    }
  };

  for (const r of rows) {
    const date = r.reported_date;
    datesSeen.add(date);
    const pillars = r.pillars || {};

    if ((pillars.blood_loss?.amount ?? 0) > 0 || pillars.blood_loss?.flags?.spotting) {
      pushIf(date, true, 'blood_loss', {
        amount: pillars.blood_loss.amount,
        severity: pillars.blood_loss.severity,
        spotting: pillars.blood_loss.flags?.spotting
      });
    }
    if ((pillars.pain?.value ?? 0) > 0)
      pushIf(date, true, 'pain', { ...pillars.pain });
    if (pillars.exercise?.any ?? false)
      pushIf(date, true, 'exercise', { ...pillars.exercise });
    if (pillars.notes?.hasNote ?? false)
      pushIf(date, true, 'notes', { ...pillars.notes });
    if ((pillars.impact?.gradeYourDay ?? 0) > 0)
      pushIf(date, true, 'impact', { ...pillars.impact });
    if (pillars.general_health?.answered)
      pushIf(date, true, 'general_health', { ...pillars.general_health });
    if (pillars.mood?.answered)
      pushIf(date, true, 'mood', { ...pillars.mood });
    if (pillars.stool_urine?.answered)
      pushIf(date, true, 'stool_urine', { ...pillars.stool_urine });
    if (pillars.sleep?.answered)
      pushIf(date, true, 'sleep', { ...pillars.sleep });
    if (
      (pillars.diet?.positives?.length ?? 0) > 0 ||
      (pillars.diet?.negatives?.length ?? 0) > 0 ||
      (pillars.diet?.neutrals?.length ?? 0) > 0
    )
      pushIf(date, true, 'diet', { ...pillars.diet });
    if (pillars.sex?.today ?? false)
      pushIf(date, true, 'sex', { ...pillars.sex });
  }

  for (const date of datesSeen) {
    for (const type of selected) {
      const hasEvent = evts.some(e => e.start === date && e.extendedProps.type === type);
      if (!hasEvent) pushIf(date, false, type, {}, true);
    }
  }

  const selectedOrder = Array.from(selected);

  evts.sort((a, b) => {
    if (a.start < b.start) return -1;
    if (a.start > b.start) return 1;
    const ia = selectedOrder.indexOf(a.extendedProps.type);
    const ib = selectedOrder.indexOf(b.extendedProps.type);
    return ia - ib;
  });

  return evts;
}

