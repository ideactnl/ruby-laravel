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
   * Setup month label display for both desktop and mobile
   */
  setupMonthDisplay() {
    // Desktop month label
    const monthEl = document.getElementById('cal-month-label');
    if (monthEl) {
      const fmt = new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' });
      const setMonth = () => {
        monthEl.textContent = fmt.format(this.calendar.getDate());
      };
      setMonth();
      this.calendar.on('datesSet', setMonth);
    }

    // Mobile date display
    this.setupMobileDateDisplay();
  }

  /**
   * Setup mobile date display that updates on calendar navigation
   */
  setupMobileDateDisplay() {
    const mobileDate = document.getElementById('mobile-date');
    const mobileMonth = document.getElementById('mobile-month');
    const mobileYear = document.getElementById('mobile-year');
    const mobileContainer = document.getElementById('mobile-date-container');
    
    if (mobileDate && mobileMonth && mobileYear && mobileContainer) {
      const updateMobileDate = () => {
        const currentCalendarDate = this.calendar.getDate();
        const today = new Date();
        
        // Check if we're viewing current month
        const isCurrentMonth = currentCalendarDate.getFullYear() === today.getFullYear() &&
          currentCalendarDate.getMonth() === today.getMonth();

        if (isCurrentMonth) {
          // Show today's date when viewing current month (zero-padded)
          mobileDate.textContent = today.getDate().toString().padStart(2, '0');
          mobileDate.classList.add('text-primary');
          mobileDate.classList.remove('text-gray-500');
        } else {
          // Show "01" when viewing other months, keep primary styling
          mobileDate.textContent = '01';
          mobileDate.classList.add('text-primary');
          mobileDate.classList.remove('text-gray-500');
        }

        // Always show the calendar's current month/year
        mobileMonth.textContent = currentCalendarDate.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        mobileYear.textContent = currentCalendarDate.getFullYear();
      };

      // Update on calendar navigation
      updateMobileDate();
      this.calendar.on('datesSet', updateMobileDate);
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

        // Update icon cursor styles based on current screen size
        this.updateIconCursorStyles();
      }, 100);
    };

    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleResize);
  }

  /**
   * Update icon cursor styles based on mobile/desktop state
   */
  updateIconCursorStyles() {
    const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const icons = document.querySelectorAll('.pbac-calendar-icon');

    icons.forEach(icon => {
      if (isMobile) {
        icon.style.cursor = 'pointer';
        if (icon.tagName === 'IMG') {
          icon.classList.add('hover:opacity-80', 'transition-opacity');
        } else {
          icon.classList.add('hover:bg-gray-300', 'transition-colors');
        }
      } else {
        icon.style.cursor = 'default';
        if (icon.tagName === 'IMG') {
          icon.classList.remove('hover:opacity-80', 'transition-opacity');
        } else {
          icon.classList.remove('hover:bg-gray-300', 'transition-colors');
        }
      }
    });
  }

  /**
   * Handle date click navigation with improved mobile support
   */
  static handleDateClick(info) {
    // Prevent navigation during scrolling or swiping
    if (window.isScrolling || window.touchMoved) {
      return false;
    }

    // Add small delay to ensure touch events have settled
    setTimeout(() => {
      if (!window.isScrolling && !window.touchMoved) {
        window.location.href = `/participant/daily-view?date=${info.dateStr}`;
      }
    }, 50);

    return false; // Prevent immediate navigation
  }

  /**
   * Handle icon click navigation to daily view
   */
  static handleIconClick(event, dateStr) {
    // Prevent event bubbling
    event.preventDefault();
    event.stopPropagation();

    if (window.isScrolling || window.touchMoved) {
      return false;
    }

    // Navigate to daily view for this specific date
    setTimeout(() => {
      if (!window.isScrolling && !window.touchMoved) {
        window.location.href = `/participant/daily-view?date=${dateStr}`;
      }
    }, 50);

    return false;
  }
}
