window.filterMenu = function filterMenu(){
  return {
    open:false,
    options: [
      { value: 'pbac', label: 'Blood Loss', color: '#DC2626', iconCls: 'fa-droplet text-red-600' },
      { value: 'pain', label: 'Pain', color: '#F59E0B', iconCls: 'fa-burst text-amber-500' },
      { value: 'general', label: 'General Health', color: '#22C55E', iconCls: 'fa-heart-pulse text-green-500' },
      { value: 'mood', label: 'Mood', color: '#10B981', iconCls: 'fa-face-smile text-emerald-500' },
      { value: 'stool', label: 'Stool/Urine', color: '#0EA5E9', iconCls: 'fa-toilet text-sky-500' },
      { value: 'diet', label: 'Diet', color: '#EAB308', iconCls: 'fa-utensils text-yellow-500' },
      { value: 'exercise', label: 'Exercise', color: '#FB923C', iconCls: 'fa-person-running text-orange-400' },
      { value: 'sex', label: 'Sex', color: '#F472B6', iconCls: 'fa-venus-mars text-pink-400' },
      { value: 'sleep', label: 'Sleep (hrs)', color: '#64748B', iconCls: 'fa-moon text-slate-500' },
    ],
    selected: [],
    init(){
      const saved = localStorage.getItem('calendar_selected_types');
      this.selected = saved ? JSON.parse(saved) : ['pbac','pain','sleep'];
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

const ICONS = {
  pbac: { cls: 'fa-droplet text-red-600', label: 'Blood Loss' },
  pain: { cls: 'fa-burst text-amber-500', label: 'Pain' },
  sleep: { cls: 'fa-moon text-slate-500', label: 'Sleep (hrs)' },
  general: { cls: 'fa-heart-pulse text-green-500', label: 'General Health' },
  mood: { cls: 'fa-face-smile text-emerald-500', label: 'Mood/Influence' },
  stool: { cls: 'fa-toilet text-sky-500', label: 'Stool/Urine' },
  exercise: { cls: 'fa-person-running text-orange-400', label: 'Exercise' },
  diet: { cls: 'fa-utensils text-yellow-500', label: 'Diet' },
  sex: { cls: 'fa-venus-mars text-pink-400', label: 'Sex' },
};

window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('participantCalendar');
  if (!el || !window.FullCalendar) return;
  const { Calendar } = window.FullCalendar;

  const rangeCache = new Map();
  let currentFetchController = null;

  function buildEventsFromRows(rows, selected){
    const evts = [];
    const pushIf = (date, cond, type, value) => {
      if (cond && selected.has(type)) {
        evts.push({ start: date, allDay: true, display: 'list-item', extendedProps: { type, value } });
      }
    };
    for (const r of rows) {
      const date = r.reported_date;
      pushIf(date, r.pbac_score_per_day > 0, 'pbac', r.pbac_score_per_day);
      pushIf(date, r.pain_score_per_day > 0, 'pain', r.pain_score_per_day);
      pushIf(date, (r.sleep_hours ?? 0) > 0, 'sleep', r.sleep_hours);
      pushIf(date, (r.quality_of_life ?? 0) > 0, 'general', r.quality_of_life);
      pushIf(date, (r.influence_factor ?? 0) > 0, 'mood', r.influence_factor);
      pushIf(date, (r.complaints_with_defecation ?? 0) > 0 || (r.complaints_with_urinating ?? 0) > 0, 'stool', 1);
      pushIf(date, (r.exercise ?? 0) > 0, 'exercise', r.exercise);
      pushIf(date, (r.diet ?? 0) > 0, 'diet', r.diet);
      pushIf(date, (r.sex ?? 0) > 0, 'sex', r.sex);
    }
    return evts;
  }

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
      const spec = ICONS[type] || { cls: 'fa-circle text-gray-400', label: type };
      const wrap = document.createElement('span');
      wrap.className = 'inline-flex items-center gap-1 px-0.5';
      const i = document.createElement('i');
      i.className = `fa-solid ${spec.cls} text-2xl`;
      i.title = `${spec.label}: ${type==='sleep' ? (value+'h') : value}`;
      wrap.appendChild(i);
      return { domNodes: [wrap] };
    },
    eventDidMount: function(arg){
      const container = arg.el.closest('.fc-daygrid-day-events');
      if (container){
        container.classList.remove('grid','grid-cols-3');
        container.classList.add('block','columns-3','w-4/5','mx-auto','px-1','pb-1');
      }
      const harness = arg.el.closest('.fc-daygrid-event-harness');
      if (harness){
        harness.classList.add('relative','mb-2.5','mt-2');
      }
      arg.el.classList.add('inline-flex','items-center','gap-1','bg-transparent','border-0','px-0.5','w-auto');
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
  let touchActive = false;
  el.addEventListener('touchstart', (e) => {
    if (!e.touches || e.touches.length !== 1) return;
    touchActive = true;
    touchStartY = e.touches[0].clientY;
  }, { passive: true });
  el.addEventListener('touchmove', (e) => {
    if (!touchActive || !e.touches || e.touches.length !== 1) return;
    const diff = e.touches[0].clientY - touchStartY;
    if (Math.abs(diff) > dragThreshold) {
      touchActive = false;
      e.preventDefault();
      if (diff < 0) {
        goNextThrottled();
      } else {
        goPrevThrottled();
      }
    }
  }, { passive: false });
  el.addEventListener('touchend', () => { touchActive = false; }, { passive: true });

  const applyDayTopCenter = () => {
    el.querySelectorAll('.fc-daygrid-day-top').forEach(t => t.classList.add('justify-center'));
  };
  applyDayTopCenter();
  calendar.on('datesSet', applyDayTopCenter);

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
