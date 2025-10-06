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

// Expose filter menu for Alpine.js
window.filterMenu = createFilterMenu;


window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('participantCalendar');
  if (!el || !window.FullCalendar) return;
  const { Calendar } = window.FullCalendar;

  // Initialize event handler
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

  // Initialize UI components
  const calendarUI = new CalendarUI(calendar);
  
  // Initialize navigation
  const navigation = new CalendarNavigation(calendar, el);

  // Apply day styling
  CalendarLayout.applyDayTopCenter(el);
  calendar.on('datesSet', () => CalendarLayout.applyDayTopCenter(el));

  // Prefetch previous months data
  eventHandler.prefetchPreviousMonths();
});
