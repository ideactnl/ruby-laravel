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
   * Provide haptic feedback for mobile interactions
   */
  triggerHapticFeedback(type = 'light') {
    if ('vibrate' in navigator) {
      const patterns = {
        light: 10,      
        medium: 25,     
        strong: 40,     
        success: [10, 50, 10]  
      };

      try {
        navigator.vibrate(patterns[type] || patterns.light);
      } catch (e) {
      }
    }
  }

  /**
   * Throttle function to prevent rapid navigation
   */
  throttle(fn, wait) {
    let inFlight = false;
    return function (...args) {
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
    const goPrevThrottled = this.throttle(() => this.calendar.prev(), 300);
    const goNextThrottled = this.throttle(() => this.calendar.next(), 300);

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
   * Setup wheel navigation (desktop - restore original behavior)
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
   * Setup mobile-optimized touch navigation (left/right swipes)
   */
  setupTouchNavigation(goPrevThrottled, goNextThrottled) {
    let touchStartY = null;
    let touchStartX = null;
    let touchActive = false;
    let touchMoved = false;
    let swipeDetected = false;
    const mobileThreshold = 50;
    const verticalThreshold = 30;

    window.isScrolling = false;
    window.touchMoved = false;
    let scrollTimeout;
    let touchTimeout;

    this.calendarElement.addEventListener('touchstart', (e) => {
      if (!e.touches || e.touches.length !== 1) return;

      touchActive = true;
      touchMoved = false;
      swipeDetected = false;
      touchStartY = e.touches[0].clientY;
      touchStartX = e.touches[0].clientX;

      clearTimeout(scrollTimeout);
      clearTimeout(touchTimeout);
    }, { passive: true });

    this.calendarElement.addEventListener('touchmove', (e) => {
      if (!touchActive || !e.touches || e.touches.length !== 1) return;

      const currentY = e.touches[0].clientY;
      const currentX = e.touches[0].clientX;
      const diffY = currentY - touchStartY;
      const diffX = currentX - touchStartX;

      if (Math.abs(diffY) > 3 || Math.abs(diffX) > 3) {
        touchMoved = true;
        window.touchMoved = true;
        window.isScrolling = true;
      }

      const isHorizontalSwipe = Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > mobileThreshold;
      const isVerticalMovement = Math.abs(diffY) > verticalThreshold;

      if (isHorizontalSwipe && !isVerticalMovement && !swipeDetected) {
        swipeDetected = true;
        touchActive = false;

        if (e.cancelable) {
          e.preventDefault();
        }
        e.stopPropagation();

        if (diffX < 0) {
          this.triggerHapticFeedback('light');
          goNextThrottled();
        } else {
          this.triggerHapticFeedback('light');
          goPrevThrottled();
        }

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          window.isScrolling = false;
          window.touchMoved = false;
        }, 250);

      } else if (isVerticalMovement && Math.abs(diffY) > 20) {
        touchActive = false;

        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          window.isScrolling = false;
          window.touchMoved = false;
        }, 150);
      }
    }, { passive: false });

    this.calendarElement.addEventListener('touchend', (e) => {
      touchActive = false;

      if (touchMoved && !swipeDetected) {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          window.isScrolling = false;
          window.touchMoved = false;
        }, 75);
      } else if (!touchMoved) {
        window.isScrolling = false;
        window.touchMoved = false;
      }

      swipeDetected = false;
    }, { passive: true });

    this.calendarElement.addEventListener('touchcancel', (e) => {
      touchActive = false;
      swipeDetected = false;

      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        window.isScrolling = false;
        window.touchMoved = false;
      }, 50);
    }, { passive: true });
  }
}
