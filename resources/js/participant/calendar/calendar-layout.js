/**
 * Calendar Layout Module
 * Handles mobile/desktop layout classes and event positioning
 */

import { getDynamicIconAndTooltip } from './calendar-icons.js';
import { CalendarUI } from './calendar-ui.js';

const preloadedIcons = new Set();
const preloadIcon = (src) => {
  if (!preloadedIcons.has(src)) {
    const img = new Image();
    img.src = src;
    preloadedIcons.add(src);
  }
};

export class CalendarLayout {
  /**
   * Preload common PBAC icons for better performance
   */
  static preloadCommonIcons() {
    const commonIcons = [

      // Grid icons
      '/images/grid_blood_loss.png',
      '/images/grid_pain.png',
      '/images/grid_impact.png',
      '/images/grid_impact_new.png',
      '/images/grid_general_health.png',
      '/images/grid_mood.png',
      '/images/grid_urine_stool.png',
      '/images/grid_sleep.png',
      '/images/grid_diet.png',
      '/images/grid_sport.png',
      '/images/grid_sex.png',
      '/images/grid_notes.png',

      // Blood loss icons
      '/images/spotting.png',
      '/images/blood_loss_1.png',
      '/images/blood_loss_2.png',
      '/images/blood_loss_3.png',
      '/images/blood_loss_4.png',
      '/images/blood_loss_5.png',

      // Pain icons (smile faces)
      '/images/smile_1.png',
      '/images/smile_2.png',
      '/images/smile_3.png',
      '/images/smile_4.png',
      '/images/smile_5.png',
      '/images/smile_6.png',

      // Common mood icons
      '/images/mood_1.png',
      '/images/mood_2.png',
      '/images/mood_7.png',

      // General health icons
      '/images/sleep.png',
      '/images/general_health_1.png',
      '/images/general_health_2.png',
      '/images/general_health_3.png',
      '/images/general_health_4.png'
    ];

    commonIcons.forEach(preloadIcon);
  }

  /**
   * Generate event content with proper icons and tooltips (mobile-only clickable icons)
   */
  static createEventContent(arg) {
    const type = arg.event.extendedProps.type;
    const value = arg.event.extendedProps.value;
    const dateStr = arg.event.startStr;

    const { iconSrc, tooltip } = getDynamicIconAndTooltip(type, value);

    const wrap = document.createElement('span');
    wrap.className = 'inline-flex items-center gap-1 px-0.5';

    if (iconSrc) {
      preloadIcon(iconSrc);

      const img = document.createElement('img');
      img.src = iconSrc;
      img.className = 'w-6 h-6 object-contain pbac-calendar-icon pbac-tooltip';
      img.setAttribute('data-tooltip', tooltip);
      img.setAttribute('data-date', dateStr);
      img.title = tooltip;
      img.alt = tooltip.split('\n')[0];
      img.loading = 'eager';

      img.addEventListener('click', (e) => {
        const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
          if ('vibrate' in navigator) {
            try {
              navigator.vibrate(40);
            } catch (ex) { }
          }
          CalendarUI.handleIconClick(e, dateStr);
        }
      });

      img.addEventListener('touchend', (e) => {
        const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
          e.preventDefault();
          if ('vibrate' in navigator) {
            try {
              navigator.vibrate(40);
            } catch (ex) { }
          }
          CalendarUI.handleIconClick(e, dateStr);
        }
      });

      wrap.appendChild(img);
    } else {
      const div = document.createElement('div');
      div.className = 'w-6 h-6 bg-gray-200 flex items-center justify-center text-xs text-gray-600 pbac-tooltip';
      div.setAttribute('data-tooltip', tooltip);
      div.setAttribute('data-date', dateStr);
      div.title = tooltip;
      div.textContent = type.charAt(0).toUpperCase();

      div.addEventListener('click', (e) => {
        const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
          if ('vibrate' in navigator) {
            try {
              navigator.vibrate(40);
            } catch (ex) { }
          }
          CalendarUI.handleIconClick(e, dateStr);
        }
      });

      div.addEventListener('touchend', (e) => {
        const isMobile = window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
          e.preventDefault();
          if ('vibrate' in navigator) {
            try {
              navigator.vibrate(40);
            } catch (ex) { }
          }
          CalendarUI.handleIconClick(e, dateStr);
        }
      });

      wrap.appendChild(div);
    }

    return { domNodes: [wrap] };
  }

  /**
   * Apply layout classes when events are mounted
   */
  static handleEventMount(arg) {
    const container = arg.el.closest('.fc-daygrid-day-events');
    const dayFrame = arg.el.closest('.fc-daygrid-day-frame');

    if (container && dayFrame) {
      const eventElement = arg.el.closest('.fc-daygrid-event-harness') || arg.el;

      if (eventElement.parentNode) {
        eventElement.parentNode.removeChild(eventElement);
      }

      dayFrame.appendChild(eventElement);

      const allEvents = dayFrame.querySelectorAll('.fc-daygrid-event');
      const eventCount = allEvents.length;

      dayFrame.classList.forEach(className => {
        if (className.startsWith('pbac-day-count-')) {
          dayFrame.classList.remove(className);
        }
      });

      dayFrame.classList.add(`pbac-day-count-${eventCount}`);
      dayFrame.classList.add('pbac-free-flow-events');

      const emptyContainer = dayFrame.querySelector('.fc-daygrid-day-events');
      if (emptyContainer) {
        emptyContainer.style.display = 'none';
        emptyContainer.style.visibility = 'hidden';
        emptyContainer.style.height = '0';
        emptyContainer.style.overflow = 'hidden';
      }
    }

    const harness = arg.el.closest('.fc-daygrid-event-harness');
    if (harness && !harness.classList.contains('relative')) {
      const isMobile = window.innerWidth <= 768;
      if (isMobile) {
        harness.classList.add('relative', 'mb-1', 'mt-1');
      } else {
        harness.classList.add('relative', 'mb-2.5', 'mt-2');
      }
    }

    if (!arg.el.classList.contains('inline-flex')) {
      arg.el.classList.add('inline-flex', 'items-center', 'gap-1', 'bg-transparent', 'border-0', 'px-0.5', 'w-auto');
    }

    const icon = arg.el.querySelector('.pbac-calendar-icon');
    if (icon && icon.style.display !== 'block') {
      icon.style.display = 'block';
      icon.style.visibility = 'visible';
      icon.style.opacity = '1';
    }

    CalendarLayout.addNewClassForMobile();
    window.addEventListener('resize', CalendarLayout.addNewClassForMobile);
  }

  /**
   * Apply mobile-specific layout classes
   */
  static applyMobileLayout(container, dayFrame, eventCount) {
    container.classList.add('pbac-mobile-events');
    if (dayFrame) {
      dayFrame.classList.add('pbac-mobile-day');
    }

    if (eventCount <= 3) {
      container.classList.add('pbac-mobile-single-row');

      if (eventCount === 1) {
        container.classList.add('pbac-mobile-one-icon');
      } else if (eventCount === 2) {
        container.classList.add('pbac-mobile-two-icons');
      } else if (eventCount === 3) {
        container.classList.add('pbac-mobile-three-icons');
      }
    } else {
      container.classList.add('pbac-mobile-multi-row');
    }
  }

  /**
   * Apply desktop-specific layout classes
   */
  static applyDesktopLayout(container, eventCount) {
    container.classList.add('pbac-desktop-events');

    if (eventCount <= 3) {
      container.classList.add('pbac-desktop-single-row');
    } else if (eventCount <= 6) {
      container.classList.add('pbac-desktop-double-row');
    } else {
      container.classList.add('pbac-desktop-multi-row');
    }
  }

  /**
   * Apply day cell styling
   */
  static handleDayCellMount(info) {
    const frame = info.el.querySelector('.fc-daygrid-day-frame') || info.el;
    const day = info.date.getDay(); 
    frame.classList.add('cursor-pointer', 'hover:bg-gray-50');

    const events = frame.querySelectorAll('.fc-daygrid-event');
    if (events.length === 0) {
      frame.classList.add('pbac-day-count-0');
    }

  if (day === 0 || day === 6) {
    const dateNumber = info.el.querySelector('.fc-daygrid-day-number');

    if (dateNumber) {
      dateNumber.classList.add(
        'text-red-500!',
      );
    }
  }

  }

  /**
   * Apply day top center styling
   */
  static applyDayTopCenter(calendarElement) {
    requestAnimationFrame(() => {
      calendarElement.querySelectorAll('.fc-daygrid-day-top:not(.justify-center)').forEach(t => {
        t.classList.add('justify-center');
      });
    });
  }

  /**
   * Update zero event day class for mobile layout
   */
  static addNewClassForMobile() {
    const isMobile = window.innerWidth <= 768;
    if (!isMobile) return;

    const filterHasThree = window.selectedCalendarTypes && window.selectedCalendarTypes.size === 3;

    document.querySelectorAll('.fc-daygrid-day-frame').forEach(frame => {
      if (filterHasThree) {
        frame.classList.add('three-present');
      } else {
        frame.classList.remove('three-present');
      }
    });
  }



}
