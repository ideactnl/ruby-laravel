/**
 * Date Management Module
 * Handles date navigation, formatting, and flatpickr integration
 */

export class DateManager {
  constructor(component) {
    this.component = component;
    this._fp = null;
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
   * Initialize flatpickr date picker
   */
  initDatePicker() {
    try {
      if (window.flatpickr && this.component.$refs?.datePick) {
        this._fp = window.flatpickr(this.component.$refs.datePick, {
          dateFormat: 'Y-m-d',
          defaultDate: this.component.date,
          allowInput: false,
          clickOpens: false,
          wrap: false,
          onChange: (sel) => {
            if (sel && sel[0]) {
              // Use local date formatting to avoid timezone issues
              const iso = this.formatDateToISO(sel[0]);
              
              if (iso !== this.component.date) {
                // Use updateDate method but skip flatpickr update to avoid loops
                this.updateDate(iso, true);
              }
            }
          },
        });
      }
    } catch (_) {}
  }

  /**
   * Open date picker
   */
  openDate() {
    if (this._fp && typeof this._fp.open === 'function') {
      try {
        this._fp.setDate(this.component.date, false);
        this._fp.open();
        return;
      } catch (_) {}
    }

    const el = this.component.$refs?.datePick;
    try {
      if (el && typeof el.showPicker === 'function') {
        el.showPicker();
      } else if (el) {
        el.click();
      }
    } catch (e) {
      if (el) el.click();
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
  updateDate(newDate, skipFlatpickrUpdate = false) {
    if (newDate === this.component.date) return;
    
    this.component.date = newDate;
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('date', newDate);
    window.history.replaceState({}, '', url);
    
    // Update flatpickr if it exists and we're not being called from flatpickr
    if (this._fp && !skipFlatpickrUpdate) {
      this._fp.setDate(newDate, false);
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
