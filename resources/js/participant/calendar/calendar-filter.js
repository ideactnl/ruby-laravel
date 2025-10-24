/**
 * Calendar Filter Menu Module
 * Handles the filter dropdown for selecting calendar event types
 */

export function createFilterMenu() {
  return {
    open: false,
    options: [
      { value: 'blood_loss', label: 'Blood Loss', color: '#DC2626', iconSrc: '/images/grid_blood_loss.png' },
      { value: 'pain', label: 'Pain', color: '#F59E0B', iconSrc: '/images/grid_pain.png' },
      { value: 'impact', label: 'Impact', color: '#22C55E', iconSrc: '/images/grid_impact.png' },
      { value: 'general_health', label: 'General Health', color: '#10B981', iconSrc: '/images/grid_general_health.png' },
      { value: 'mood', label: 'Mood', color: '#8B5CF6', iconSrc: '/images/grid_mood.png' },
      { value: 'stool_urine', label: 'Stool/Urine', color: '#0EA5E9', iconSrc: '/images/grid_urine_stool.png' },
      { value: 'sleep', label: 'Sleep', color: '#6366F1', iconSrc: '/images/grid_sleep.png' },
      { value: 'diet', label: 'Diet', color: '#EAB308', iconSrc: '/images/grid_diet.png' },
      { value: 'exercise', label: 'Exercise', color: '#FB923C', iconSrc: '/images/grid_sport.png' },
      { value: 'sex', label: 'Sexual Health', color: '#F472B6', iconSrc: '/images/grid_sex.png' },
      { value: 'notes', label: 'Notes', color: '#64748B', iconSrc: '/images/grid_notes.png' },
    ],
    selected: [],
    shakeTarget: null,

    triggerHaptic(type = 'light') {
      if (window.innerWidth <= 768 && 'vibrate' in navigator) {
        try {
          const patterns = {
            light: 10,
            medium: 20,
            error: [50, 50, 50]
          };
          navigator.vibrate(patterns[type] || patterns.light);
        } catch (e) { }
      }
    },

    init() {
      const saved = localStorage.getItem('calendar_selected_types');
      this.selected = saved ? JSON.parse(saved) : ['blood_loss', 'pain', 'impact'];
      if (this.selected.length > 3) this.selected = this.selected.slice(0, 3);
      window.selectedCalendarTypes = new Set(this.selected);
    },

    apply() {
      window.selectedCalendarTypes = new Set(this.selected);
      localStorage.setItem('calendar_selected_types', JSON.stringify(this.selected));
      if (window.participantCalendar) {
        window.participantCalendar.refetchEvents();
        CalendarLayout.updateZeroDayClassForMobile();
      }
    },

    toggle(val) {
      const idx = this.selected.indexOf(val);
      if (idx >= 0) {
        this.selected.splice(idx, 1);
        this.triggerHaptic('light');
        this.apply();
      } else {
        if (this.selected.length >= 3) {
          this.triggerHaptic('error');
          return;
        }
        this.selected.push(val);
        this.triggerHaptic('light');
        this.apply();
      }
    },

    selectAll() {
      this.selected = this.options.map(o => o.value).slice(0, 3);
      this.triggerHaptic('medium');
      this.apply();
    },

    clearAll() {
      this.selected = [];
      this.triggerHaptic('medium');
      this.apply();
    }
  };
}
