/**
 * Calendar Events Module
 * Handles event fetching, caching, and rendering
 */

import { buildEventsFromRows } from './calendar-data.js';

export class CalendarEvents {
  constructor() {
    this.rangeCache = new Map();
    this.currentFetchController = null;
  }

  /**
   * Fetch events for the given date range
   */
  async fetchEvents(info, success, failure) {
    try {
      const cacheKey = `${info.startStr}|${info.endStr}`;
      const selected = window.selectedCalendarTypes || new Set();

      if (this.rangeCache.has(cacheKey)) {
        const cachedRows = this.rangeCache.get(cacheKey);
        success(buildEventsFromRows(cachedRows, selected));
        return;
      }

      if (this.currentFetchController) this.currentFetchController.abort();
      const controller = new AbortController();
      this.currentFetchController = controller;

      const url = new URL('/api/v1/participant/dashboard', window.location.origin);
      url.searchParams.set('preset', 'custom');
      url.searchParams.set('start_date', info.startStr);
      url.searchParams.set('end_date', info.endStr);
      
      const res = await fetch(url, { signal: controller.signal });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      
      const json = await res.json();
      const rows = json?.calendar ?? [];
      this.rangeCache.set(cacheKey, rows);
      success(buildEventsFromRows(rows, selected));
    } catch (e) {
      if (e?.name === 'AbortError') {
        return;
      }
      failure(e);
    }
  }

  /**
   * Prefetch data for previous months to improve performance
   */
  async prefetchPreviousMonths() {
    try {
      const base = new Date();
      for (let i = 1; i <= 2; i++) {
        const d = new Date(base.getFullYear(), base.getMonth() - i, 1);
        const start = new Date(d.getFullYear(), d.getMonth(), 1);
        const end = new Date(d.getFullYear(), d.getMonth() + 1, 0);
        const fmt = (x) => x.toISOString().slice(0, 10);
        
        const url = new URL('/api/v1/participant/dashboard', window.location.origin);
        url.searchParams.set('preset', 'custom');
        url.searchParams.set('start_date', fmt(start));
        url.searchParams.set('end_date', fmt(end));
        
        fetch(url).catch(() => {});
      }
    } catch (_) {}
  }
}
