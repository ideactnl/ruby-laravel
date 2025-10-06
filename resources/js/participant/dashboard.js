import { getDynamicIconAndTooltip } from './calendar-icons.js';
import { buildEventsFromRows } from './calendar-data.js';

window.filterMenu = function filterMenu(){
  return {
    open:false,
    options: [
      { value: 'blood_loss', label: 'Blood Loss', color: '#DC2626', iconSrc: '/images/grid_blood_loss.png' },
      { value: 'pain', label: 'Pain', color: '#F59E0B', iconSrc: '/images/grid_pain.png' },
      { value: 'impact', label: 'Impact', color: '#22C55E', iconSrc: '/images/grid_impact.png' },
      { value: 'general_health', label: 'General Health', color: '#10B981', iconSrc: '/images/grid_general_health.png' },
      { value: 'mood', label: 'Mood', color: '#8B5CF6', iconSrc: '/images/grid_mood.png' },
      { value: 'stool_urine', label: 'Stool/Urine', color: '#0EA5E9', iconSrc: '/images/grid_urine_stool.png' },
      { value: 'sleep', label: 'Sleep', color: '#6366F1', iconSrc: '/images/grid_sleep.png' },
      { value: 'diet', label: 'Diet', color: '#EAB308', iconSrc: '/images/grid_diet.png' },
      { value: 'exercise', label: 'Exercise', color: '#FB923C', iconSrc: '/images/grid_sport.png' },
      { value: 'sex', label: 'Sexual Health', color: '#F472B6', iconSrc: '/images/grid_sex.png' },
      { value: 'notes', label: 'Notes', color: '#64748B', iconSrc: '/images/grid_notes.png' },
    ],
    selected: [],
    init(){
      const saved = localStorage.getItem('calendar_selected_types');
      this.selected = saved ? JSON.parse(saved) : ['blood_loss','pain','impact'];
      if (this.selected.length > 3) this.selected = this.selected.slice(0,3);
      window.selectedCalendarTypes = new Set(this.selected);
    },
    apply(){
      window.selectedCalendarTypes = new Set(this.selected);
      localStorage.setItem('calendar_selected_types', JSON.stringify(this.selected));
      if (window.participantCalendar) window.participantCalendar.refetchEvents();
    },
    toggle(val){
      const idx = this.selected.indexOf(val);
      if (idx >= 0) {
        this.selected.splice(idx,1);
      } else {
        if (this.selected.length >= 3) return;
        this.selected.push(val);
      }
      this.apply();
    },
    selectAll(){
      this.selected = this.options.map(o=>o.value).slice(0,3);
      this.apply();
    },
    clearAll(){
      this.selected = [];
      this.apply();
    }
  }
};


window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('participantCalendar');
  if (!el || !window.FullCalendar) return;
  const { Calendar } = window.FullCalendar;

  const rangeCache = new Map();
  let currentFetchController = null;

  const calendar = new Calendar(el, {
    initialView: 'dayGridMonth',
    height: 'auto',
    headerToolbar: false,
    dayMaxEvents: false,
    eventDisplay: 'block',

    dayCellDidMount(info){
      const frame = info.el.querySelector('.fc-daygrid-day-frame') || info.el;
      frame.classList.add('cursor-pointer','hover:bg-gray-50');
    },

    dateClick(info){
      // Prevent accidental clicks during mobile scrolling
      if (window.isScrolling || touchMoved) {
        return false;
      }
      window.location.href = `/participant/daily-view?date=${info.dateStr}`;
    },

    events: async (info, success, failure) => {
      try {
        const cacheKey = `${info.startStr}|${info.endStr}`;
        const selected = window.selectedCalendarTypes || new Set();

        if (rangeCache.has(cacheKey)) {
          const cachedRows = rangeCache.get(cacheKey);
          success(buildEventsFromRows(cachedRows, selected));
          return;
        }

        if (currentFetchController) currentFetchController.abort();
        const controller = new AbortController();
        currentFetchController = controller;

        const url = new URL('/api/v1/participant/dashboard', window.location.origin);
        url.searchParams.set('preset', 'custom');
        url.searchParams.set('start_date', info.startStr);
        url.searchParams.set('end_date', info.endStr);
        const res = await fetch(url, { signal: controller.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        const rows = json?.calendar ?? [];
        rangeCache.set(cacheKey, rows);
        success(buildEventsFromRows(rows, selected));
      } catch (e) {
        if (e?.name === 'AbortError') {
          return;
        }
        failure(e);
      }
    },
    eventContent: function(arg) {
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
    },
    eventDidMount: function(arg){
      const container = arg.el.closest('.fc-daygrid-day-events');
      if (container){
        container.classList.remove('grid','grid-cols-3');
        // Check if mobile
        const isMobile = window.innerWidth <= 768;
        if (isMobile) {
          container.classList.add('block','columns-2','w-full','mx-auto','px-0.5','pb-0.5');
        } else {
          container.classList.add('block','columns-3','w-4/5','mx-auto','px-1','pb-1');
        }
      }
      const harness = arg.el.closest('.fc-daygrid-event-harness');
      if (harness){
        const isMobile = window.innerWidth <= 768;
        if (isMobile) {
          harness.classList.add('relative','mb-1','mt-1');
        } else {
          harness.classList.add('relative','mb-2.5','mt-2');
        }
      }
      arg.el.classList.add('inline-flex','items-center','gap-1','bg-transparent','border-0','px-0.5','w-auto');
      
      // Ensure icons are visible on mobile
      const icon = arg.el.querySelector('.pbac-calendar-icon');
      if (icon) {
        icon.style.display = 'block';
        icon.style.visibility = 'visible';
        icon.style.opacity = '1';
      }
    }
  });

  window.participantCalendar = calendar;
  calendar.render();

  const monthEl = document.getElementById('cal-month-label');
  if (monthEl){
    const fmt = new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' });
    const setMonth = () => { monthEl.textContent = fmt.format(calendar.getDate()); };
    setMonth();
    calendar.on('datesSet', setMonth);
  }

  const btnBackCurrent = document.getElementById('btn-back-current');
  const updateBackButton = () => {
    const now = new Date();
    const cur = calendar.getDate();
    const isCurrentMonth = cur && (cur.getFullYear() === now.getFullYear()) && (cur.getMonth() === now.getMonth());
    if (isCurrentMonth) {
      if (btnBackCurrent){
        btnBackCurrent.classList.add('hidden');
        btnBackCurrent.style.display = 'none';
        btnBackCurrent.setAttribute('aria-hidden', 'true');
      }
    } else {
      if (btnBackCurrent){
        btnBackCurrent.classList.remove('hidden');
        btnBackCurrent.style.display = 'inline-flex';
        btnBackCurrent.removeAttribute('aria-hidden');
      }
    }
  };
  calendar.on('datesSet', updateBackButton);
  updateBackButton();
  btnBackCurrent && btnBackCurrent.addEventListener('click', () => calendar.today());

  function throttle(fn, wait){
    let inFlight = false;
    return function(...args){
      if (inFlight) return;
      inFlight = true;
      try { fn.apply(this, args); } finally {
        setTimeout(() => { inFlight = false; }, wait);
      }
    }
  }

  const goPrevThrottled = throttle(() => calendar.prev(), 500);
  const goNextThrottled = throttle(() => calendar.next(), 500);

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

  let wheelAccum = 0;
  const wheelThreshold = 30;
  el.addEventListener('wheel', (e) => {
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

  let dragStartY = null;
  let dragging = false;
  const dragThreshold = 40;
  el.addEventListener('mousedown', (e) => {
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
  window.addEventListener('mouseup', () => { dragging = false; });

  let touchStartY = null;
  let touchStartX = null;
  let touchActive = false;
  let touchMoved = false;
  const mobileThreshold = 60; // Increased threshold for mobile
  
  // Global scroll state tracking
  window.isScrolling = false;
  let scrollTimeout;
  
  el.addEventListener('touchstart', (e) => {
    if (!e.touches || e.touches.length !== 1) return;
    touchActive = true;
    touchMoved = false;
    touchStartY = e.touches[0].clientY;
    touchStartX = e.touches[0].clientX;
  }, { passive: true });
  
  el.addEventListener('touchmove', (e) => {
    if (!touchActive || !e.touches || e.touches.length !== 1) return;
    
    const currentY = e.touches[0].clientY;
    const currentX = e.touches[0].clientX;
    const diffY = currentY - touchStartY;
    const diffX = currentX - touchStartX;
    
    // Mark as scrolling if any movement detected
    if (Math.abs(diffY) > 5 || Math.abs(diffX) > 5) {
      window.isScrolling = true;
      touchMoved = true;
      
      // Clear existing timeout and set new one
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        window.isScrolling = false;
      }, 300); // Reset scroll state after 300ms of no movement
    }
    
    // Check if this is primarily a vertical swipe (not horizontal)
    if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 20) {
      // Only prevent default and navigate if we've moved enough
      if (Math.abs(diffY) > mobileThreshold) {
        touchActive = false;
        e.preventDefault();
        e.stopPropagation();
        
        if (diffY < 0) {
          // Swipe up = next month
          goNextThrottled();
        } else {
          // Swipe down = previous month
          goPrevThrottled();
        }
      }
    } else if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 20) {
      // Horizontal swipe detected - don't interfere with normal scrolling
      touchActive = false;
    }
  }, { passive: false });
  
  el.addEventListener('touchend', (e) => {
    // Reset touch state
    touchActive = false;
    
    // If we moved during touch, prevent clicks for a short time
    if (touchMoved) {
      // Keep scroll state active for a bit longer to prevent immediate clicks
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        window.isScrolling = false;
        touchMoved = false;
      }, 150); // Shorter delay for touchend
    } else {
      // No movement, allow immediate clicks
      touchMoved = false;
      window.isScrolling = false;
    }
  }, { passive: true });

  const applyDayTopCenter = () => {
    el.querySelectorAll('.fc-daygrid-day-top').forEach(t => t.classList.add('justify-center'));
  };
  applyDayTopCenter();
  calendar.on('datesSet', applyDayTopCenter);

  // Handle mobile layout changes on resize/orientation change
  const handleResize = () => {
    // Re-render calendar to apply mobile-specific layouts
    setTimeout(() => {
      if (window.participantCalendar) {
        window.participantCalendar.updateSize();
      }
    }, 100);
  };
  
  window.addEventListener('resize', handleResize);
  window.addEventListener('orientationchange', handleResize);

  (async () => {
    try {
      const base = new Date();
      for (let i = 1; i <= 2; i++) {
        const d = new Date(base.getFullYear(), base.getMonth() - i, 1);
        const start = new Date(d.getFullYear(), d.getMonth(), 1);
        const end = new Date(d.getFullYear(), d.getMonth() + 1, 0);
        const fmt = (x) => x.toISOString().slice(0,10);
        const url = new URL('/api/v1/participant/dashboard', window.location.origin);
        url.searchParams.set('preset', 'custom');
        url.searchParams.set('start_date', fmt(start));
        url.searchParams.set('end_date', fmt(end));
        fetch(url).catch(()=>{});
      }
    } catch (_) {}
  })();
});
