/**
 * Calendar UI Module
 * Handles UI elements like month display, back button, and resize handling
 */

export class CalendarUI {
  constructor(calendar) {
    this.calendar = calendar;
    this.setupUI();
  }

  /**
   * Setup all UI components
   */
  setupUI() {
    this.setupMonthDisplay();
    this.setupBackButton();
    this.setupResizeHandling();
  }

  /**
   * Setup month label display
   */
  setupMonthDisplay() {
    const monthEl = document.getElementById('cal-month-label');
    if (monthEl) {
      const fmt = new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' });
      const setMonth = () => { 
        monthEl.textContent = fmt.format(this.calendar.getDate()); 
      };
      setMonth();
      this.calendar.on('datesSet', setMonth);
    }
  }

  /**
   * Setup back to current month button
   */
  setupBackButton() {
    const btnBackCurrent = document.getElementById('btn-back-current');
    
    const updateBackButton = () => {
      const now = new Date();
      const cur = this.calendar.getDate();
      const isCurrentMonth = cur && 
        (cur.getFullYear() === now.getFullYear()) && 
        (cur.getMonth() === now.getMonth());
      
      if (isCurrentMonth) {
        if (btnBackCurrent) {
          btnBackCurrent.classList.add('hidden');
          btnBackCurrent.style.display = 'none';
          btnBackCurrent.setAttribute('aria-hidden', 'true');
        }
      } else {
        if (btnBackCurrent) {
          btnBackCurrent.classList.remove('hidden');
          btnBackCurrent.style.display = 'inline-flex';
          btnBackCurrent.removeAttribute('aria-hidden');
        }
      }
    };
    
    this.calendar.on('datesSet', updateBackButton);
    updateBackButton();
    
    if (btnBackCurrent) {
      btnBackCurrent.addEventListener('click', () => this.calendar.today());
    }
  }

  /**
   * Setup resize and orientation change handling
   */
  setupResizeHandling() {
    const handleResize = () => {
      setTimeout(() => {
        if (window.participantCalendar) {
          window.participantCalendar.updateSize();
        }
      }, 100);
    };
    
    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleResize);
  }

  /**
   * Handle date click navigation
   */
  static handleDateClick(info) {
    if (window.isScrolling || window.touchMoved) {
      return false;
    }
    window.location.href = `/participant/daily-view?date=${info.dateStr}`;
  }
}
