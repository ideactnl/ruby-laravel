/* Dashboard (Monthly Overview) page logic: Alpine filter menu + FullCalendar setup */

window.filterMenu = function filterMenu(){
  return {
    open:false,
    options: [
      { value: 'pbac', label: 'Blood Loss', color: '#DC2626' },
      { value: 'pain', label: 'Pain', color: '#F59E0B' },
      { value: 'general', label: 'General Health', color: '#22C55E' },
      { value: 'mood', label: 'Mood', color: '#10B981' },
      { value: 'stool', label: 'Stool/Urine', color: '#0EA5E9' },
      { value: 'diet', label: 'Diet', color: '#EAB308' },
      { value: 'exercise', label: 'Exercise', color: '#FB923C' },
      { value: 'sex', label: 'Sex', color: '#F472B6' },
      { value: 'sleep', label: 'Sleep (hrs)', color: '#64748B' },
    ],
    selected: [],
    init(){
      const saved = localStorage.getItem('calendar_selected_types');
      this.selected = saved ? JSON.parse(saved) : ['pbac','pain','sleep'];
      window.selectedCalendarTypes = new Set(this.selected);
    },
    apply(){
      window.selectedCalendarTypes = new Set(this.selected);
      localStorage.setItem('calendar_selected_types', JSON.stringify(this.selected));
      if (window.participantCalendar) window.participantCalendar.refetchEvents();
    },
    selectAll(){
      this.selected = this.options.map(o=>o.value);
      this.apply();
    },
    clearAll(){
      this.selected = [];
      this.apply();
    }
  }
};

// FullCalendar initialization
window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('participantCalendar');
  if (!el || !window.FullCalendar) return;
  const { Calendar } = window.FullCalendar;

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
        for (const r of rows) {
          const date = r.reported_date;
          const pushIf = (cond, type, value) => {
            if (cond && selected.has(type)) {
              evts.push({ start: date, allDay: true, display: 'list-item', extendedProps: { type, value } });
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
      } catch (e) {
        failure(e);
      }
    },
    eventContent: function(arg) {
      const type = arg.event.extendedProps.type;
      const value = arg.event.extendedProps.value;
      const icons = {
        pbac: { cls: 'fa-droplet text-red-600', label: 'Blood Loss' },
        pain: { cls: 'fa-burst text-amber-500', label: 'Pain' },
        sleep: { cls: 'fa-moon text-slate-500', label: 'Sleep (hrs)' },
        general: { cls: 'fa-heart-pulse text-green-500', label: 'General Health' },
        mood: { cls: 'fa-face-smile text-emerald-500', label: 'Mood/Influence' },
        stool: { cls: 'fa-toilet text-sky-500', label: 'Stool/Urine' },
        exercise: { cls: 'fa-person-running text-orange-400', label: 'Exercise' },
        diet: { cls: 'fa-utensils text-yellow-500', label: 'Diet' },
        sex: { cls: 'fa-venus-mars text-pink-400', label: 'Sex' },
        spotting: { cls: 'fa-droplet text-red-500', label: 'Spotting' },
        pain_medication: { cls: 'fa-capsules text-amber-500', label: 'Pain medication' },
      };
      const spec = icons[type] || { cls: 'fa-circle text-gray-400', label: type };
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

  const btnToday = document.getElementById('btn-today');
  const btnPrev = document.getElementById('btn-prev');
  const btnNext = document.getElementById('btn-next');
  btnToday && btnToday.addEventListener('click', () => calendar.today());
  btnPrev && btnPrev.addEventListener('click', () => calendar.prev());
  btnNext && btnNext.addEventListener('click', () => calendar.next());

  const applyDayTopCenter = () => {
    const tops = el.querySelectorAll('.fc-daygrid-day-top');
    tops.forEach(t => t.classList.add('justify-center'));
  };
  applyDayTopCenter();
  calendar.on('datesSet', applyDayTopCenter);
});
