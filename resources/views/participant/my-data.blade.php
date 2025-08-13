@extends('layouts.participant.app')

@section('content')
<div class="max-w-6xl mx-auto py-8" x-data="pbacChartData()" x-init="fetchChart()">
    <h1 class="text-2xl font-bold mb-6">PBAC Records</h1>

    <!-- Filter + Export Section -->
    <div class="my-8 bg-white p-6 rounded-lg shadow-sm" x-data>
        <h2 class="text-xl font-bold mb-4">Filter & Export</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="preset" class="block text-sm font-medium text-gray-700">Date Range</label>
                <select id="preset" x-model="preset" @change="fetchChart" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2">
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div x-show="preset === 'custom'" x-transition>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" id="start_date" x-model="customStart" @change="fetchChart" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2">
            </div>

            <div x-show="preset === 'custom'" x-transition>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" id="end_date" x-model="customEnd" @change="fetchChart" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-2">
            </div>
        </div>

        <!-- Export buttons -->
        <div class="mt-6 flex flex-wrap gap-4">
            <form method="GET" action="{{ route('participant.pbac.export') }}" @submit="setExportRange">
                <input type="hidden" name="preset" x-model="preset">
                <input type="hidden" name="start_date" :value="customStart">
                <input type="hidden" name="end_date" :value="customEnd">
                <input type="hidden" name="format" value="xlsx">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-lg shadow">
                    Export to Excel
                </button>
            </form>

            <!-- Chart PDF Export -->
            <button @click="exportChartAsPdf" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow">
                Export Chart to PDF
            </button>
        </div>
    </div>

    <!-- Chart -->
    <div class="mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">PBAC Chart</h2>
        <canvas id="pbacChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function pbacChartData() {
    return {
        chartData: [],
        chart: null,
        loadingChart: true,
        preset: 'month',
        customStart: '',
        customEnd: '',

        fetchChart() {
            this.loadingChart = true;

            let url = new URL(`{{ route('participant.pbac.chart') }}`);
            url.searchParams.append('preset', this.preset);

            if (this.preset === 'custom') {
                if (this.customStart) url.searchParams.append('start_date', this.customStart);
                if (this.customEnd) url.searchParams.append('end_date', this.customEnd);
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    this.chartData = data.data ?? [];
                    this.updateChart();
                    this.loadingChart = false;
                })
                .catch(() => this.loadingChart = false);
        },

        updateChart() {
            const labels = this.chartData.map(r => r.reported_date);
            const dotted = [4, 4];
            const style = (label, data, borderColor, backgroundColor) => ({
                label, data, borderColor, backgroundColor, tension: 0.4, borderDash: dotted, pointRadius: 4
            });

            if (this.chart) this.chart.destroy();

            const ctx = document.getElementById('pbacChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        style('PBAC Score', this.chartData.map(r => r.pbac_score_per_day), '#EF4444', 'rgba(239, 68, 68, 0.1)'),
                        style('Pain Score', this.chartData.map(r => r.pain_score_per_day), '#F59E0B', 'rgba(245, 158, 11, 0.1)'),
                        style('Quality of Life', this.chartData.map(r => r.quality_of_life), '#3B82F6', 'rgba(59, 130, 246, 0.1)'),
                        style('Energy Level', this.chartData.map(r => r.energy_level), '#10B981', 'rgba(16, 185, 129, 0.1)'),
                        style('Influence Factor', this.chartData.map(r => r.influence_factor), '#8B5CF6', 'rgba(139, 92, 246, 0.1)'),
                        style('Pain Medication', this.chartData.map(r => r.pain_medication), '#EC4899', 'rgba(236, 72, 153, 0.1)'),
                        style('Defecation Complaints', this.chartData.map(r => r.complaints_with_defecation), '#6366F1', 'rgba(99, 102, 241, 0.1)'),
                        style('Urinating Complaints', this.chartData.map(r => r.complaints_with_urinating), '#14B8A6', 'rgba(20, 184, 166, 0.1)'),
                        style('Sleep Quality', this.chartData.map(r => r.quality_of_sleep), '#7C3AED', 'rgba(124, 58, 237, 0.1)'),
                        style('Exercise', this.chartData.map(r => r.exercise), '#4B5563', 'rgba(75, 85, 99, 0.1)')
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'left',
                            align: 'start',
                            labels: { usePointStyle: true }
                        },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        },

        setExportRange(event) {
            // Ensures proper start/end values are included in export form
            if (this.preset === 'custom') {
                if (!this.customStart || !this.customEnd) {
                    event.preventDefault();
                    alert('Please select a valid custom range');
                }
            }
        },

        exportChartAsPdf() {
            const chartCanvas = document.getElementById('pbacChart');
            const base64Image = chartCanvas.toDataURL('image/png');

            fetch(`{{ route('participant.pbac.chart.export.pdf') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    chart_image: base64Image,
                    preset: this.preset,
                    start_date: this.customStart,
                    end_date: this.customEnd,
                })
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'pbac_chart_export.pdf';
                link.click();
            })
            .catch(error => {
                alert('Failed to export PDF');
                console.error(error);
            });
        }
    }
}
</script>
@endsection
