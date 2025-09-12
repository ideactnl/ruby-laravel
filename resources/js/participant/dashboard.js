// Dashboard (Monthly Overview): Alpine filter menu + FullCalendar

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

  function renderLegendFromAgg(agg){
    const legendEl = document.getElementById('calendarLegend');
    if (!legendEl) return;
    legendEl.innerHTML = '';
    const items = Object.entries(agg)
      .map(([type, {sum, count}]) => ({ type, avg: count ? (sum / count) : 0 }))
      .sort((a,b) => b.avg - a.avg)
      .slice(0,3);
    items.forEach(item => {
      const spec = ICONS[item.type] || { cls: 'fa-circle text-gray-400', label: item.type };
      const pill = document.createElement('span');
      pill.className = 'inline-flex items-center gap-2 rounded-full border border-[#5E0F0F]/30 bg-[#5E0F0F]/5 px-3 py-1 text-xs text-[#5E0F0F]';
      const i = document.createElement('i');
      i.className = `fa-solid ${spec.cls}`;
      const name = document.createElement('span');
      name.textContent = spec.label;
      const val = document.createElement('span');
      val.className = 'text-[11px] text-gray-500';
      val.textContent = `avg ${item.avg.toFixed(item.type==='sleep' ? 1 : 0)}`;
      pill.append(i, name, val);
      legendEl.appendChild(pill);
    });
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
        const url = new URL('/api/v1/participant/dashboard', window.location.origin);
        url.searchParams.set('preset', 'custom');
        url.searchParams.set('start_date', info.startStr);
        url.searchParams.set('end_date', info.endStr);
        const res = await fetch(url);
        const json = await res.json();
        const rows = json?.calendar ?? [];
        const evts = [];
        const selected = window.selectedCalendarTypes || new Set();
        const agg = {};
        for (const r of rows) {
          const date = r.reported_date;
          const pushIf = (cond, type, value) => {
            if (cond && selected.has(type)) {
              evts.push({ start: date, allDay: true, display: 'list-item', extendedProps: { type, value } });
              if (!agg[type]) agg[type] = { sum: 0, count: 0 };
              agg[type].sum += (value ?? 0);
              agg[type].count += 1;
            }
          };
          pushIf(r.pbac_score_per_day > 0, 'pbac', r.pbac_score_per_day);
          pushIf(r.pain_score_per_day > 0, 'pain', r.pain_score_per_day);
          pushIf((r.sleep_hours ?? 0) > 0, 'sleep', r.sleep_hours);
          pushIf((r.quality_of_life ?? 0) > 0, 'general', r.quality_of_life);
          pushIf((r.influence_factor ?? 0) > 0, 'mood', r.influence_factor);
          pushIf((r.complaints_with_defecation ?? 0) > 0 || (r.complaints_with_urinating ?? 0) > 0, 'stool', 1);
          pushIf((r.exercise ?? 0) > 0, 'exercise', r.exercise);
          pushIf((r.diet ?? 0) > 0, 'diet', r.diet);
          pushIf((r.sex ?? 0) > 0, 'sex', r.sex);
        }
        success(evts);
        renderLegendFromAgg(agg);
      } catch (e) {
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
  const yearSelect = document.getElementById('cal-year-select');
  if (monthEl){
    const fmt = new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' });
    const setMonth = () => { monthEl.textContent = fmt.format(calendar.getDate()); };
    setMonth();
    calendar.on('datesSet', setMonth);
  }

  if (yearSelect){
    const nowYear = new Date().getFullYear();
    const years = [];
    for (let y = nowYear - 20; y <= nowYear + 10; y++) years.push(y);
    yearSelect.innerHTML = years.map(y => `<option value="${y}">${y}</option>`).join('');
    const syncYear = () => {
      const y = calendar.getDate().getFullYear();
      if (String(yearSelect.value) !== String(y)) yearSelect.value = String(y);
    };
    syncYear();
    calendar.on('datesSet', syncYear);
    yearSelect.addEventListener('change', () => {
      const targetYear = parseInt(yearSelect.value, 10);
      const current = calendar.getDate();
      const newDate = new Date(current);
      newDate.setFullYear(targetYear);
      calendar.gotoDate(newDate);
    });
  }

  const btnToday = document.getElementById('btn-today');
  const btnPrev = document.getElementById('btn-prev');
  const btnNext = document.getElementById('btn-next');
  btnToday && btnToday.addEventListener('click', () => calendar.today());
  btnPrev && btnPrev.addEventListener('click', () => calendar.prev());
  btnNext && btnNext.addEventListener('click', () => calendar.next());

  const applyDayTopCenter = () => {
    el.querySelectorAll('.fc-daygrid-day-top').forEach(t => t.classList.add('justify-center'));
  };
  applyDayTopCenter();
  calendar.on('datesSet', applyDayTopCenter);
});
