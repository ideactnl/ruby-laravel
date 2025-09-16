@extends('layouts.admin.app')

@section('content')
    <div class="max-w-7xl mx-auto py-10 px-4" x-data="{ log: null }">
        <h1 class="text-2xl font-bold mb-6">PBAC Export Logs</h1>

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

            <select name="status" class="w-full md:w-48 border border-gray-300 rounded-xl p-2 shadow-sm">
                <option value="">All Status</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl shadow">
                Filter
            </button>
        </form>

        <div class="bg-white shadow rounded-2xl p-6 overflow-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="text-left text-sm text-gray-600">
                        <th class="py-2">User</th>
                        <th class="py-2">Format</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Description</th>
                        <th class="py-2">File</th>
                        <th class="py-2">Logged At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($logs as $activity)
                        <tr>
                            <td class="py-2">{{ $activity->causer?->name ?? 'N/A' }}</td>
                            <td class="py-2 uppercase">{{ $activity->properties['format'] ?? '?' }}</td>
                            <td class="py-2">
                                @php($status = $activity->properties['event'] ?? 'queued')
                                <span
                                    class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] sm:text-xs font-medium
                                {{ $status === 'completed' ? 'bg-green-50 text-green-700 border-green-200' : ($status === 'failed' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="py-2 truncate max-w-xs" title="{{ $activity->description }}">
                                {{ Str::limit($activity->description, 50) }}</td>
                            <td class="py-2">
                                @if (!empty($activity->properties['file_path']))
                                    <code class="text-xs">{{ basename($activity->properties['file_path']) }}</code>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-2">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">No logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    @endsection
