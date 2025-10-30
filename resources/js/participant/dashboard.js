/**
 * Main Dashboard Module
 * Orchestrates all calendar functionality
 */

import {
  createFilterMenu,
  CalendarEvents,
  CalendarLayout,
  CalendarNavigation,
  CalendarUI
} from './calendar/index.js';

window.filterMenu = createFilterMenu;

window.goToCurrentMonth = () => {
  if (window.participantCalendar) {
    if (window.innerWidth <= 768 && 'vibrate' in navigator) {
      try {
        navigator.vibrate([10, 30, 10]);
      } catch (e) {
      }
    }
    window.participantCalendar.today();
  }
};


window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('participantCalendar');
  if (!el || !window.FullCalendar) return;
  const { Calendar } = window.FullCalendar;

  CalendarLayout.preloadCommonIcons();

  const eventHandler = new CalendarEvents();

  const calendar = new Calendar(el, {
    initialView: 'dayGridMonth',
    height: 'auto',
    headerToolbar: false,
    dayMaxEvents: false,
    eventDisplay: 'block',

    dayCellDidMount: CalendarLayout.handleDayCellMount,
    dateClick: CalendarUI.handleDateClick,
    events: (info, success, failure) => eventHandler.fetchEvents(info, success, failure),
    eventContent: CalendarLayout.createEventContent,
    eventDidMount: CalendarLayout.handleEventMount
  });

  window.participantCalendar = calendar;
  calendar.render();

  const calendarUI = new CalendarUI(calendar);

  const navigation = new CalendarNavigation(calendar, el);

  CalendarLayout.applyDayTopCenter(el);
  calendar.on('datesSet', () => CalendarLayout.applyDayTopCenter(el));
  CalendarLayout.updateZeroDayClassForMobile();

  eventHandler.prefetchPreviousMonths();
});
