(function(){
  const COLORS = {
    blood_loss: '#DC2626', // red
    pain: '#F59E0B', // amber
    impact: '#22C55E', // green
    general_health: '#10B981', // emerald
    mood: '#8B5CF6', // violet
    stool_urine: '#0EA5E9', // sky
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
      { type: 'bar', label: 'Diet', data: dietBars, backgroundColor: COLORS.diet },
      { type: 'bar', label: 'Exercise', data: exerciseBars, backgroundColor: COLORS.exercise },
      { type: 'bar', label: 'Sex', data: sexBars, backgroundColor: COLORS.sex },
      { type: 'bar', label: 'Notes', data: notesBars, backgroundColor: COLORS.notes },
      { type: 'line', label: 'Trend', data: lineSeries, borderColor: COLORS.line, backgroundColor: COLORS.line, tension: 0.35, yAxisID: 'y' }
    ];
    return { labels, datasets };
  }

  async function fetchDashboard(preset, start, end){
    const url = new URL('/api/v1/participant/dashboard', window.location.origin);
    if (preset) url.searchParams.set('preset', preset);
    if (preset === 'custom') {
      if (start) url.searchParams.set('start_date', start);
      if (end) url.searchParams.set('end_date', end);
    }
    const res = await fetch(url, { credentials: 'include' });
    if (!res.ok) throw new Error('Failed to load data');
    const json = await res.json();
    return json.calendar || [];
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
    if (!canvas || !window.Chart) return;
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

  async function loadChart(preset, start, end){
    window.__rubyPreset = preset;
    const rows = await fetchDashboard(preset, start, end);
    renderChart(rows);
  }

  function exportCSV(preset, start, end){
    const url = new URL('/api/v1/participant/pbac/export', window.location.origin);
    if (preset) url.searchParams.set('preset', preset);
    if (start) url.searchParams.set('start_date', start);
    if (end) url.searchParams.set('end_date', end);
    window.location.href = url.toString();
  }

  async function exportPDF(preset, start, end){
    const canvas = document.getElementById('exportChart');
    if (!canvas) return;
    const dataUrl = canvas.toDataURL('image/png');
    try { await fetch('/sanctum/csrf-cookie', { credentials: 'include' }); } catch (e) {}
    const token = decodeURIComponent((document.cookie.split('; ').find(c=>c.startsWith('XSRF-TOKEN='))||'').split('=')[1]||'');
    const res = await fetch('/api/v1/participant/pbac/chart/export/pdf', {
      method: 'POST', headers: { 'Accept':'application/pdf,application/json', 'Content-Type': 'application/json', 'X-XSRF-TOKEN': token }, credentials: 'include',
      body: JSON.stringify({ chart_image: dataUrl, preset: preset || null, start_date: start || null, end_date: end || null })
    });
    if (!res.ok) { try { console.error(await res.json()); } catch (e) {} return; }
    const blob = await res.blob(); const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = 'pbac_chart.pdf'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
  }

  window.RubyExport = { loadChart, exportCSV, exportPDF };
})();
