@extends('layouts.medical-specialist.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-4">Medical Specialist Dashboard</h2>

        <div class="mb-6 space-y-1">
            <p class="text-gray-700"><strong>Participant:</strong> {{ $participant->full_name }}</p>
            @if ($expiryDate)
                <p class="text-gray-600"><strong>PIN Expiry:</strong> {{ $expiryDate }}</p>
            @endif
        </div>

        <form method="GET" action="{{ route('medical-specialist.dashboard') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="preset" class="block text-sm font-medium text-gray-700 mb-1">Date Preset</label>
                <select name="preset" id="preset" onchange="toggleCustomDateInputs()"
                    class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="week" {{ $preset === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $preset === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ $preset === 'year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ $preset === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div class="custom-date hidden">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="custom-date hidden">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">Apply</button>
            </div>
        </form>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2">PBAC Score Chart ({{ $startDate }} to {{ $endDate }})</h3>
            <canvas id="pbacChart" height="100"></canvas>
        </div>

        <div class="mb-4 flex flex-wrap gap-3">
            <form method="GET" action="{{ route('medical-specialist.export') }}" target="_blank" class="flex gap-2">
                <input type="hidden" name="preset" value="{{ $preset }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button type="submit" name="format" value="xlsx"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Export Excel</button>
                <button type="submit" name="format" value="csv"
                    class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800 transition">Export CSV</button>
                <button type="submit" name="format" value="json"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">Export JSON</button>
            </form>

            <form method="POST" action="{{ route('medical-specialist.export.pdf') }}" target="_blank">
                @csrf
                <input type="hidden" name="preset" value="{{ $preset }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Download
                    PDF</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartData = @json($chartData);

            if (!chartData.length) {
                console.warn("No PBAC data available for chart.");
                return;
            }
            console.log(chartData);


            const ctx = document.getElementById('pbacChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: 'PBAC Score',
                        data: chartData.map(item => item.total_score),
                        borderWidth: 2,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Score'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    }
                }
            });

            // Show/hide custom date inputs
            function toggleCustomDateInputs() {
                const preset = document.getElementById('preset').value;
                document.querySelectorAll('.custom-date').forEach(el => {
                    el.classList.toggle('hidden', preset !== 'custom');
                });
            }

            toggleCustomDateInputs();
            document.getElementById('preset').addEventListener('change', toggleCustomDateInputs);
        });
    </script>
@endpush
