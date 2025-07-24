@extends('layouts.admin.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4" x-data="{ log: null }">
    <h1 class="text-2xl font-bold mb-6">PBAC Export Logs</h1>

    {{-- Search + Filter --}}
    <form method="GET" class="mb-4 flex flex-col md:flex-row md:items-center gap-4">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by user, format, or description"
            class="w-full md:w-1/2 border border-gray-300 rounded-xl p-2 shadow-sm">

        <select name="format" class="w-full md:w-48 border border-gray-300 rounded-xl p-2 shadow-sm">
            <option value="">All Formats</option>
            <option value="csv" {{ request('format') == 'csv' ? 'selected' : '' }}>CSV</option>
            <option value="xlsx" {{ request('format') == 'xlsx' ? 'selected' : '' }}>Excel</option>
            <option value="json" {{ request('format') == 'json' ? 'selected' : '' }}>JSON</option>
        </select>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl shadow">
            Filter
        </button>
    </form>

    {{-- Logs Table --}}
    <div class="bg-white shadow rounded-2xl p-6 overflow-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-sm text-gray-600">
                    <th class="py-2">User</th>
                    <th class="py-2">Date Range</th>
                    <th class="py-2">Format</th>
                    <th class="py-2">Description</th>
                    <th class="py-2">Logged At</th>
                    <th class="py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse ($logs as $activity)
                    <tr>
                        <td class="py-2">{{ $activity->causer?->name ?? 'N/A' }}</td>
                        <td class="py-2">
                            {{ $activity->properties['start_date'] ?? '?' }} — 
                            {{ $activity->properties['end_date'] ?? '?' }}
                        </td>
                        <td class="py-2 uppercase">{{ $activity->properties['format'] ?? '?' }}</td>
                        <td class="py-2 truncate max-w-xs" title="{{ $activity->description }}">{{ Str::limit($activity->description, 50) }}</td>
                        <td class="py-2">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        <td class="py-2">
                            <button class="text-blue-600 hover:underline"
                                @click="log = {{ $activity->toJson() }}">
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-500">No logs found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
        </div>


    {{-- Alpine Modal --}}
    <div x-show="log"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        
        <div @click.outside="log = null"
            class="bg-white p-6 rounded-xl w-full max-w-xl shadow-xl relative">
            <button class="absolute top-2 right-3 text-gray-400 hover:text-black text-xl font-bold" @click="log = null">
                &times;
            </button>
            <h2 class="text-lg font-semibold mb-4">Log Details</h2>
            <div class="text-sm space-y-2">
                <p><strong>User:</strong> <span x-text="log?.causer?.name ?? 'N/A'"></span></p>
                <p><strong>Description:</strong> <span x-text="log?.description ?? 'N/A'"></span></p>
                <p><strong>Format:</strong> <span x-text="log?.properties?.format ?? 'N/A'"></span></p>
                <p><strong>Preset:</strong> <span x-text="log?.properties?.preset ?? 'N/A'"></span></p>
                <p><strong>Date Range:</strong>
                    <span x-text="log?.properties?.start_date ?? '?'"></span> -
                    <span x-text="log?.properties?.end_date ?? '?'"></span>
                </p>
                <p><strong>IP Address:</strong> <span x-text="log?.properties?.ip ?? 'N/A'"></span></p>
                <p><strong>User Agent:</strong> <span x-text="log?.properties?.user_agent ?? 'N/A'"></span></p>
                <p><strong>Logged At:</strong> <span x-text="new Date(log?.created_at).toLocaleString()"></span></p>
            </div>
        </div>
    </div>
</div>
@endsection
