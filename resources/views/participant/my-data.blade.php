@extends('layouts.participant.app')

@section('content')
<div class="max-w-6xl mx-auto py-8" x-data="pbacTable()" x-init="fetchAll()">
    <h1 class="text-2xl font-bold mb-6">PBAC Records</h1>

    <!-- Filters -->
    <div class="mb-8 bg-gray-50 p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end">
        <div class="flex flex-col w-full sm:w-auto">
            <label class="text-sm font-medium text-gray-700 mb-1">From</label>
            <input
                type="date"
                x-model="filters.from"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <div class="flex flex-col w-full sm:w-auto">
            <label class="text-sm font-medium text-gray-700 mb-1">To</label>
            <input
                type="date"
                x-model="filters.to"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <div class="w-full sm:w-auto">
            <button
                @click="fetchAll()"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow transition duration-200 w-full sm:w-auto"
            >
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Table -->
    <template x-if="loadingTable">
        <p class="text-gray-500">Loading table...</p>
    </template>
    <template x-if="!loadingTable && tableData.length">
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left">
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">PBAC</th>
                        <th class="px-4 py-2">Pain</th>
                        <th class="px-4 py-2">QoL</th>
                        <th class="px-4 py-2">Energy</th>
                        <th class="px-4 py-2">Spotting</th>
                        <th class="px-4 py-2">Influence</th>
                        <th class="px-4 py-2">Medication</th>
                        <th class="px-4 py-2">Defecation</th>
                        <th class="px-4 py-2">Urinating</th>
                        <th class="px-4 py-2">Sleep</th>
                        <th class="px-4 py-2">Exercise</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="record in tableData" :key="record.id">
                        <tr>
                            <td class="px-4 py-2" x-text="record.reported_date"></td>
                            <td class="px-4 py-2" x-text="record.pbac_score_per_day"></td>
                            <td class="px-4 py-2" x-text="record.pain_score_per_day"></td>
                            <td class="px-4 py-2" x-text="record.quality_of_life"></td>
                            <td class="px-4 py-2" x-text="record.energy_level"></td>
                            <td class="px-4 py-2" x-text="record.spotting_yes_no ? 'Yes' : 'No'"></td>
                            <td class="px-4 py-2" x-text="record.influence_factor"></td>
                            <td class="px-4 py-2" x-text="record.pain_medication"></td>
                            <td class="px-4 py-2" x-text="record.complaints_with_defecation"></td>
                            <td class="px-4 py-2" x-text="record.complaints_with_urinating"></td>
                            <td class="px-4 py-2" x-text="record.quality_of_sleep"></td>
                            <td class="px-4 py-2" x-text="record.exercise"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <!-- Chart -->
    <div class="mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">PBAC Chart</h2>
        <canvas id="pbacChart" height="100"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function pbacTable() {
    return {
        filters: { from: '', to: '' },
        tableData: [],
        chartData: [],
        loadingTable: true,
        loadingChart: true,
        chart: null,

        fetchAll() {
            this.fetchTable();
            this.fetchChart();
        },

        fetchTable() {
            this.loadingTable = true;
            const params = new URLSearchParams(this.filters).toString();
            fetch(`{{ route('participant.pbac.table') }}?${params}`)
                .then(res => res.json())
                .then(data => {
                    this.tableData = data.data ?? [];
                    this.loadingTable = false;
                })
                .catch(() => this.loadingTable = false);
        },

        fetchChart() {
            this.loadingChart = true;
            const params = new URLSearchParams(this.filters).toString();
            fetch(`{{ route('participant.pbac.chart') }}?${params}`)
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
            const dotted = [4, 4]; // dashed line style

            const datasetStyle = (label, data, borderColor, backgroundColor) => ({
                label,
                data,
                borderColor,
                backgroundColor,
                tension: 0.4,
                borderDash: dotted,
                pointRadius: 4
            });

            const pbac = this.chartData.map(r => r.pbac_score_per_day);
            const pain = this.chartData.map(r => r.pain_score_per_day);
            const qol = this.chartData.map(r => r.quality_of_life);
            const energy = this.chartData.map(r => r.energy_level);
            const influence = this.chartData.map(r => r.influence_factor);
            const medication = this.chartData.map(r => r.pain_medication);
            const defecation = this.chartData.map(r => r.complaints_with_defecation);
            const urinating = this.chartData.map(r => r.complaints_with_urinating);
            const sleep = this.chartData.map(r => r.quality_of_sleep);
            const exercise = this.chartData.map(r => r.exercise);

            if (this.chart) this.chart.destroy();

            const ctx = document.getElementById('pbacChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        datasetStyle('PBAC Score', pbac, '#EF4444', 'rgba(239, 68, 68, 0.1)'),
                        datasetStyle('Pain Score', pain, '#F59E0B', 'rgba(245, 158, 11, 0.1)'),
                        datasetStyle('Quality of Life', qol, '#3B82F6', 'rgba(59, 130, 246, 0.1)'),
                        datasetStyle('Energy Level', energy, '#10B981', 'rgba(16, 185, 129, 0.1)'),
                        datasetStyle('Influence Factor', influence, '#8B5CF6', 'rgba(139, 92, 246, 0.1)'),
                        datasetStyle('Pain Medication', medication, '#EC4899', 'rgba(236, 72, 153, 0.1)'),
                        datasetStyle('Defecation Complaints', defecation, '#6366F1', 'rgba(99, 102, 241, 0.1)'),
                        datasetStyle('Urinating Complaints', urinating, '#14B8A6', 'rgba(20, 184, 166, 0.1)'),
                        datasetStyle('Sleep Quality', sleep, '#7C3AED', 'rgba(124, 58, 237, 0.1)'),
                        datasetStyle('Exercise', exercise, '#4B5563', 'rgba(75, 85, 99, 0.1)'),
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
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>
@endsection
