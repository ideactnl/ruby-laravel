/**
 * Calendar Layout Module
 * Handles mobile/desktop layout classes and event positioning
 */

import { getDynamicIconAndTooltip } from './calendar-icons.js';

export class CalendarLayout {
  /**
   * Generate event content with proper icons and tooltips
   */
  static createEventContent(arg) {
    const type = arg.event.extendedProps.type;
    const value = arg.event.extendedProps.value;
    
    const { iconSrc, tooltip } = getDynamicIconAndTooltip(type, value);
    
    const wrap = document.createElement('span');
    wrap.className = 'inline-flex items-center gap-1 px-0.5';
    
    if (iconSrc) {
      const img = document.createElement('img');
      img.src = iconSrc;
      img.className = 'w-6 h-6 object-contain pbac-calendar-icon pbac-tooltip';
      img.setAttribute('data-tooltip', tooltip);
      img.title = tooltip;
      img.alt = tooltip.split('\n')[0];
      img.loading = 'lazy';
      wrap.appendChild(img);
    } else {
      const div = document.createElement('div');
      div.className = 'w-6 h-6 bg-gray-200 flex items-center justify-center text-xs text-gray-600 pbac-tooltip';
      div.setAttribute('data-tooltip', tooltip);
      div.title = tooltip;
      div.textContent = type.charAt(0).toUpperCase();
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
    
    if (container) {
      container.classList.add('pbac-events-container');
      container.classList.remove('grid', 'grid-cols-3');
      
      const dayEvents = container.querySelectorAll('.fc-daygrid-event');
      const eventCount = dayEvents.length;
      
      container.classList.add(`pbac-events-count-${eventCount}`);
      
      if (dayFrame) {
        dayFrame.classList.add(`pbac-day-count-${eventCount}`);
      }
      
      const isMobile = window.innerWidth <= 768;
      if (isMobile) {
        CalendarLayout.applyMobileLayout(container, dayFrame, eventCount);
      } else {
        CalendarLayout.applyDesktopLayout(container, eventCount);
      }
      
      const selectedCount = window.selectedCalendarTypes ? window.selectedCalendarTypes.size : 0;
      container.classList.add(`pbac-selected-domains-${selectedCount}`);
    }

    // Apply event harness styling
    const harness = arg.el.closest('.fc-daygrid-event-harness');
    if (harness) {
      const isMobile = window.innerWidth <= 768;
      if (isMobile) {
        harness.classList.add('relative', 'mb-1', 'mt-1');
      } else {
        harness.classList.add('relative', 'mb-2.5', 'mt-2');
      }
    }

    // Apply event element styling
    arg.el.classList.add('inline-flex', 'items-center', 'gap-1', 'bg-transparent', 'border-0', 'px-0.5', 'w-auto');
    
    // Ensure icon visibility
    const icon = arg.el.querySelector('.pbac-calendar-icon');
    if (icon) {
      icon.style.display = 'block';
      icon.style.visibility = 'visible';
      icon.style.opacity = '1';
    }
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
    frame.classList.add('cursor-pointer', 'hover:bg-gray-50');
  }

  /**
   * Apply day top center styling
   */
  static applyDayTopCenter(calendarElement) {
    calendarElement.querySelectorAll('.fc-daygrid-day-top').forEach(t => 
      t.classList.add('justify-center')
    );
  }
}
