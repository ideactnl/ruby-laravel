/**
 * Calendar Navigation Module
 * Handles keyboard, mouse, touch, and wheel navigation
 */

export class CalendarNavigation {
  constructor(calendar, calendarElement) {
    this.calendar = calendar;
    this.calendarElement = calendarElement;
    this.setupNavigation();
  }

  /**
   * Throttle function to prevent rapid navigation
   */
  throttle(fn, wait) {
    let inFlight = false;
    return function(...args) {
      if (inFlight) return;
      inFlight = true;
      try { 
        fn.apply(this, args); 
      } finally {
        setTimeout(() => { inFlight = false; }, wait);
      }
    };
  }

  /**
   * Setup all navigation event listeners
   */
  setupNavigation() {
    const goPrevThrottled = this.throttle(() => this.calendar.prev(), 500);
    const goNextThrottled = this.throttle(() => this.calendar.next(), 500);

    this.setupKeyboardNavigation(goPrevThrottled, goNextThrottled);
    this.setupWheelNavigation(goPrevThrottled, goNextThrottled);
    this.setupMouseNavigation(goPrevThrottled, goNextThrottled);
    this.setupTouchNavigation(goPrevThrottled, goNextThrottled);
  }

  /**
   * Setup keyboard navigation (arrow keys)
   */
  setupKeyboardNavigation(goPrevThrottled, goNextThrottled) {
    window.addEventListener('keydown', (e) => {
      const tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
      if (tag === 'input' || tag === 'textarea' || e.metaKey || e.ctrlKey || e.altKey) return;
      
      if (e.key === 'ArrowUp') {
        e.preventDefault();
        goPrevThrottled();
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        goNextThrottled();
      }
    });
  }

  /**
   * Setup wheel navigation
   */
  setupWheelNavigation(goPrevThrottled, goNextThrottled) {
    let wheelAccum = 0;
    const wheelThreshold = 30;
    
    this.calendarElement.addEventListener('wheel', (e) => {
      if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;
      e.preventDefault();
      e.stopPropagation();
      
      wheelAccum += e.deltaY;
      if (Math.abs(wheelAccum) >= wheelThreshold) {
        if (wheelAccum > 0) {
          goNextThrottled();
        } else {
          goPrevThrottled();
        }
        wheelAccum = 0;
      }
    }, { passive: false });
  }

  /**
   * Setup mouse drag navigation
   */
  setupMouseNavigation(goPrevThrottled, goNextThrottled) {
    let dragStartY = null;
    let dragging = false;
    const dragThreshold = 40;
    
    this.calendarElement.addEventListener('mousedown', (e) => {
      if (e.button !== 0) return;
      dragging = true;
      dragStartY = e.clientY;
    });
    
    window.addEventListener('mousemove', (e) => {
      if (!dragging) return;
      const diff = e.clientY - dragStartY;
      if (Math.abs(diff) > dragThreshold) {
        dragging = false;
        if (diff < 0) {
          goNextThrottled();
        } else {
          goPrevThrottled();
        }
      }
    });
    
    window.addEventListener('mouseup', () => { 
      dragging = false; 
    });
  }

  /**
   * Setup touch navigation with scroll detection
   */
  setupTouchNavigation(goPrevThrottled, goNextThrottled) {
    let touchStartY = null;
    let touchStartX = null;
    let touchActive = false;
    let touchMoved = false;
    const mobileThreshold = 60;
    
    window.isScrolling = false;
    let scrollTimeout;
    
    this.calendarElement.addEventListener('touchstart', (e) => {
      if (!e.touches || e.touches.length !== 1) return;
      touchActive = true;
      touchMoved = false;
      touchStartY = e.touches[0].clientY;
      touchStartX = e.touches[0].clientX;
    }, { passive: true });
    
    this.calendarElement.addEventListener('touchmove', (e) => {
      if (!touchActive || !e.touches || e.touches.length !== 1) return;
      
      const currentY = e.touches[0].clientY;
      const currentX = e.touches[0].clientX;
      const diffY = currentY - touchStartY;
      const diffX = currentX - touchStartX;
      
      if (Math.abs(diffY) > 5 || Math.abs(diffX) > 5) {
        window.isScrolling = true;
        touchMoved = true;
        
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          window.isScrolling = false;
        }, 300);
      }
      
      if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 20) {
        if (Math.abs(diffY) > mobileThreshold) {
          touchActive = false;
          e.preventDefault();
          e.stopPropagation();
          
          if (diffY < 0) {
            goNextThrottled();
          } else {
            goPrevThrottled();
          }
        }
      } else if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 20) {
        touchActive = false;
      }
    }, { passive: false });
    
    this.calendarElement.addEventListener('touchend', (e) => {
      touchActive = false;
      
      if (touchMoved) {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          window.isScrolling = false;
          touchMoved = false;
        }, 150);
      } else {
        touchMoved = false;
        window.isScrolling = false;
      }
    }, { passive: true });

    // Expose touchMoved for date click handling
    window.touchMoved = touchMoved;
  }
}
