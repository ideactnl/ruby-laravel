/**
 * Date Management Module
 * Handles date navigation, formatting, and native date picker integration
 */

export class DateManager {
  constructor(component) {
    this.component = component;
  }

  /**
   * Format date object to YYYY-MM-DD string without timezone issues
   */
  formatDateToISO(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  /**
   * Initialize native date picker for all devices
   */
  initDatePicker() {
    try {
      const el = this.component.$refs?.datePick;
      if (!el) return;

      this.setupNativeDatePicker(el);
    } catch (e) {
      // Silent fail
    }
  }

  /**
   * Setup native HTML5 date picker
   */
  setupNativeDatePicker(el) {
    // Configure as date input
    el.type = 'date';
    el.value = this.component.date;
    
    // Add change listener
    const changeHandler = (e) => {
      if (e.target.value && e.target.value !== this.component.date) {
        this.updateDate(e.target.value, true);
      }
    };
    
    // Remove any existing listeners to avoid duplicates
    el.removeEventListener('change', changeHandler);
    el.addEventListener('change', changeHandler);
  }


  /**
   * Open native date picker
   */
  openDate() {
    const el = this.component.$refs?.datePick;
    if (!el) return;
    
    try {
      // Set current date
      el.value = this.component.date;
      
      // Try showPicker first (modern browsers)
      if (typeof el.showPicker === 'function') {
        el.showPicker();
      } else {
        // Fallback for older browsers
        el.focus();
        el.click();
      }
    } catch (e) {
      // Final fallback
      try {
        el.click();
      } catch (clickError) {
        // Silent fail
      }
    }
  }

  /**
   * Navigate to previous day
   */
  prevDay() {
    try {
      const d = new Date(this.component.date);
      d.setDate(d.getDate() - 1);
      this.updateDate(this.formatDateToISO(d));
    } catch (e) {}
  }

  /**
   * Navigate to next day
   */
  nextDay() {
    try {
      const d = new Date(this.component.date);
      d.setDate(d.getDate() + 1);
      this.updateDate(this.formatDateToISO(d));
    } catch (e) {}
  }

  /**
   * Update date and sync all related components
   */
  updateDate(newDate, skipPickerUpdate = false) {
    if (newDate === this.component.date) return;
    
    this.component.date = newDate;
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('date', newDate);
    window.history.replaceState({}, '', url);
    
    // Update the native date input if not being called from the picker itself
    if (!skipPickerUpdate) {
      const el = this.component.$refs?.datePick;
      if (el) {
        el.value = newDate;
      }
    }
    
    // Fetch new data
    this.component.fetchData();
  }

  /**
   * Format date as heading
   */
  getHeading(date) {
    try {
      const d = new Date(date);
      return d.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        weekday: 'long'
      });
    } catch (e) {
      return date;
    }
  }

  /**
   * Format date as short date
   */
  getShortDate(date) {
    try {
      const d = new Date(date);
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const yyyy = d.getFullYear();
      return `${dd}/${mm}/${yyyy}`;
    } catch (e) {
      return date;
    }
  }

  /**
   * Get date from URL parameters
   */
  getDateFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('date');
  }
}
