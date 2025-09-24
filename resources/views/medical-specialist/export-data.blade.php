@extends('layouts.medical-specialist.app')

@section('content')
<script>
    window.__medicalSpecialistChartData = @json($chartData);
</script>
<script>
// Wait for Chart.js and DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
  // Ensure Chart.js is loaded
  if (!window.Chart) {
    console.error('Chart.js not loaded!');
    return;
  }

(function(){
  const COLORS = {
    blood_loss: '#DC2626', // red
    pain: '#F59E0B', // amber
    impact: '#22C55E', // green
    general_health: '#10B981', // emerald
    mood: '#8B5CF6', // violet
    stool_urine: '#0EA5E9', // sky
    sleep: '#6366F1', // indigo
    diet: '#EAB308', // yellow
    exercise: '#FB923C', // orange
    sex: '#F472B6', // pink
    notes: '#64748B', // slate
    line: '#60a5fa' // light blue
  };

  let chart = null;
  let resizeObs = null;

  function buildDatasets(rows){
    const dates = rows.map(r => new Date(r.reported_date));
    const years = new Set(dates.map(d => d.getFullYear()));
    const useYear = years.size > 1 || (window.__rubyPreset === 'year');
    const labels = dates.map(d => d.toLocaleDateString(undefined, useYear ? { month:'short', day:'2-digit', year:'numeric' } : { month:'short', day:'2-digit' }));

    const bloodLossBars = rows.map(r => r.pillars?.blood_loss?.amount ?? 0);
    const painBars = rows.map(r => r.pillars?.pain?.value ?? 0);
    const impactBars = rows.map(r => r.pillars?.impact?.gradeYourDay ?? 0);
    const generalHealthBars = rows.map(r => r.pillars?.general_health?.energyLevel ?? 0);
    const moodBars = rows.map(r => (r.pillars?.mood?.positives?.length ?? 0) + (r.pillars?.mood?.negatives?.length ?? 0));
    const stoolUrineBars = rows.map(r => (r.pillars?.stool_urine?.urine?.blood ? 1 : 0) + (r.pillars?.stool_urine?.stool?.blood ? 1 : 0));
    const sleepBars = rows.map(r => r.pillars?.sleep?.calculatedHours ?? 0);
    const dietBars = rows.map(r => (r.pillars?.diet?.positives?.length ?? 0) + (r.pillars?.diet?.negatives?.length ?? 0));
    const exerciseBars = rows.map(r => r.pillars?.exercise?.any ? 1 : 0);
    const sexBars = rows.map(r => r.pillars?.sex?.today ? 1 : 0);
    const notesBars = rows.map(r => r.pillars?.notes?.hasNote ? 1 : 0);
    const lineSeries = bloodLossBars;

    const datasets = [
      { type: 'bar', label: 'Blood Loss', data: bloodLossBars, backgroundColor: COLORS.blood_loss },
      { type: 'bar', label: 'Pain', data: painBars, backgroundColor: COLORS.pain },
      { type: 'bar', label: 'Impact', data: impactBars, backgroundColor: COLORS.impact },
      { type: 'bar', label: 'General Health', data: generalHealthBars, backgroundColor: COLORS.general_health },
      { type: 'bar', label: 'Mood', data: moodBars, backgroundColor: COLORS.mood },
      { type: 'bar', label: 'Stool/Urine', data: stoolUrineBars, backgroundColor: COLORS.stool_urine },
      { type: 'bar', label: 'Sleep', data: sleepBars, backgroundColor: COLORS.sleep },
      { type: 'bar', label: 'Diet', data: dietBars, backgroundColor: COLORS.diet },
      { type: 'bar', label: 'Exercise', data: exerciseBars, backgroundColor: COLORS.exercise },
      { type: 'bar', label: 'Sex', data: sexBars, backgroundColor: COLORS.sex },
      { type: 'bar', label: 'Notes', data: notesBars, backgroundColor: COLORS.notes },
      { type: 'line', label: 'Trend', data: lineSeries, borderColor: COLORS.line, backgroundColor: COLORS.line, tension: 0.35, yAxisID: 'y' }
    ];
    return { labels, datasets };
  }

  function renderLegend(datasets){
    const legend = document.getElementById('exportLegend');
    if (!legend) return;
    legend.innerHTML = '';
    datasets.forEach((ds, idx) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'inline-flex items-center gap-2 select-none';
      btn.dataset.index = String(idx);
      const box = document.createElement('span');
      box.className = 'inline-block h-3 w-6 rounded-sm';
      const color = ds.borderColor || ds.backgroundColor;
      box.style.background = color;
      const label = document.createElement('span');
      label.className = 'text-sm text-gray-700';
      label.textContent = ds.label;
      btn.appendChild(box); btn.appendChild(label);
      btn.addEventListener('click', () => {
        if (!window.__rubyChart) return;
        const i = Number(btn.dataset.index);
        const visible = window.__rubyChart.isDatasetVisible(i);
        window.__rubyChart.setDatasetVisibility(i, !visible);
        window.__rubyChart.update();
        btn.classList.toggle('opacity-50', visible);
      });
      legend.appendChild(btn);
    });
  }

  function renderChart(rows){
    const canvas = document.getElementById('exportChart');
    
    if (!canvas || !window.Chart) {
      console.error('Missing canvas or Chart.js:', { canvas: !!canvas, chartJs: !!window.Chart });
      return;
    }
    
    const { labels, datasets } = buildDatasets(rows);
    const ctx = canvas.getContext('2d');

    if (chart) { chart.destroy(); chart = null; }
    chart = new Chart(ctx, {
      data: { labels, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 100,
        interaction: { mode: 'index', intersect: false },
        scales: { y: { beginAtZero: true }, x: { ticks: { maxRotation: 0, autoSkip: true } } },
        plugins: {
          legend: { display: false },
          title: { display: rows.length === 0, text: rows.length === 0 ? 'No data in the selected range' : '' }
        },
        datasets: { bar: { barPercentage: 0.9, categoryPercentage: 0.9, maxBarThickness: 28 } }
      }
    });

    window.__rubyChart = chart;
    renderLegend(datasets);

    try {
      if (resizeObs) resizeObs.disconnect();
      const container = canvas.parentElement;
      resizeObs = new ResizeObserver(() => { if (window.__rubyChart) window.__rubyChart.resize(); });
      resizeObs.observe(container);
    } catch (e) {}
  }

  async function fetchDashboard(preset, start, end){
    const url = new URL('/medical-specialist/chart-data', window.location.origin);
    if (preset) url.searchParams.set('preset', preset);
    if (preset === 'custom') {
      if (start) url.searchParams.set('start_date', start);
      if (end) url.searchParams.set('end_date', end);
    }
    
    const res = await fetch(url, { 
      headers: { 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    
    if (!res.ok) throw new Error('Failed to load data');
    const json = await res.json();
    return json.calendar || [];
  }

  async function loadChart(preset, start, end){
    window.__rubyPreset = preset;
    
    let rows;
    if (preset === 'month' && !start && !end && window.__medicalSpecialistChartData) {
      console.log('Using preloaded data:', window.__medicalSpecialistChartData);
      rows = window.__medicalSpecialistChartData;
    } else {
      console.log('Fetching data from API...');
      try {
        rows = await fetchDashboard(preset, start, end);
        console.log('API data received:', rows);
      } catch (error) {
        console.error('Failed to fetch data:', error);
        throw error;
      }
    }
    
    renderChart(rows);
  }

  window.RubyExport = { loadChart };
})();

});

</script>
<div class="px-0 pr-4 sm:pr-6 lg:px-8 py-6"
    x-data="{
        preset:'month', start:'', end:'', loading:false, error:'', busy:false,
        async refresh(){
            console.log('refresh called:', { preset: this.preset, start: this.start, end: this.end });
            try { 
                this.loading = true; 
                this.error='';
                console.log('RubyExport available:', !!window.RubyExport);
                if (window.RubyExport && window.RubyExport.loadChart) {
                    await window.RubyExport.loadChart(this.preset, this.start, this.end);
                } else {
                    console.error('RubyExport.loadChart not available');
                    this.error = 'Chart system not initialized';
                }
            } catch(e) { 
                console.error('Chart refresh error:', e);
                this.error='Failed to load chart: ' + e.message; 
            } finally { 
                this.loading=false; 
            }
        },
        exportCSV(){
            window.dispatchEvent(new CustomEvent('export:start', { detail: { type: 'csv', preset: this.preset, start: this.start, end: this.end } }));
        },
        exportPDF(){
            window.dispatchEvent(new CustomEvent('export:start', { detail: { type: 'pdf', preset: this.preset, start: this.start, end: this.end } }));
        }
    }"
    x-init="() => { 
        const d=new Date(); 
        const s=new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0,10); 
        const e=d.toISOString().slice(0,10); 
        start=s; 
        end=e; 
        console.log('Medical Specialist Chart Init:', { preset, start, end, chartData: window.__medicalSpecialistChartData });
        $nextTick(()=>refresh()); 
    }"
    @export:busy.window="busy = true"
    @export:idle.window="busy = false"
>
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-md border border-primary px-3 py-2 text-sm font-semibold text-primary bg-primary/5">Range</span>
            <x-form.select name="preset" x-model="preset" @change="refresh()" variant="participant" :enhanced="false">
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
                <option value="year">This Year</option>
                <option value="custom">Custom Range</option>
            </x-form.select>
            <span class="ml-1 text-gray-500 text-sm" x-show="loading">Loading chart...</span>
        </div>
        <div class="flex items-center gap-6" x-show="preset==='custom'">
            <div class="flex items-center gap-2">
                <x-form.input type="date" name="start_date" x-model="start" @change="refresh()" variant="participant" placeholder="Start Date" />
            </div>
            <div class="flex items-center gap-2">
                <x-form.input type="date" name="end_date" x-model="end" @change="refresh()" variant="participant" placeholder="End Date" />
            </div>
        </div>
    </div>

    <template x-if="error">
        <p class="text-sm text-red-600 mb-2" x-text="error"></p>
    </template>

    <div class="bg-white rounded-lg border border-gray-100 p-4">
        <div id="exportLegend" class="flex flex-wrap items-center gap-6 px-2 pb-2"></div>
        <div class="overflow-hidden h-[320px] sm:h-[380px] md:h-[420px] lg:h-[480px]">
            <canvas id="exportChart"></canvas>
        </div>
        <div class="mt-4 flex items-center justify-end gap-3">
            <button @click="exportPDF()" :disabled="busy" :class="busy ? 'opacity-60 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                Export as PDF
                <i class="fa-solid fa-file-pdf"></i>
            </button>
            <button @click="exportCSV()" :disabled="busy" :class="busy ? 'opacity-60 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                Export as CSV
                <i class="fa-solid fa-file-csv"></i>
            </button>
        </div>
    </div>
    <div class="mt-2">
        <x-medical-specialist.export-progress type="pdf" chartCanvasId="exportChart" />
        <x-medical-specialist.export-progress type="csv" />
    </div>
</div>
@endsection
