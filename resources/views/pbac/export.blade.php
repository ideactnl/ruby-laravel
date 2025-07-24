@extends('layouts.admin.app')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8" 
     x-data="{ preset: '{{ old('preset', '') }}' }">
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Export PBAC Data</h2>

        <form method="GET" action="{{ route('pbac.export') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">                
                <div>
                    <label for="preset" class="block text-sm font-medium text-gray-700">Preset Date Range</label>
                    <select id="preset" name="preset" x-model="preset" class="mt-1 block w-full border border-gray-300 rounded-xl shadow-sm p-2" required>
                        <option value="">-- Select --</option>
                        <option value="week" {{ old('preset') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ old('preset') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ old('preset') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                        <option value="year" {{ old('preset') == 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ old('preset') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                    @error('preset')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700">Export Format</label>
                    <select id="format" name="format" required
                            class="mt-1 block w-full border border-gray-300 rounded-xl shadow-sm p-2">
                        <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                        <option value="xlsx" {{ old('format') == 'xlsx' ? 'selected' : '' }}>Excel</option>
                        <option value="json" {{ old('format') == 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                    @error('format')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="preset === 'custom'" x-transition>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-xl shadow-sm p-2"
                           placeholder="YYYY-MM-DD">
                    @error('start_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="preset === 'custom'" x-transition>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-xl shadow-sm p-2"
                           placeholder="YYYY-MM-DD">
                    @error('end_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-xl shadow">
                    Export Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
