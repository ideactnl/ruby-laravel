@extends('layouts.admin.app')

@section('navbar_title', 'Export')
@section('navbar_subtitle', 'Queue PBAC dataset exports')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Card -->
        <div class="max-w-3xl w-full mr-auto bg-white shadow rounded-2xl" x-data="{ preset: '{{ old('preset', '') }}' }">
            <form method="POST" action="#" class="p-6 space-y-4" @submit.prevent="
                $dispatch('export:start', {
                    type: document.getElementById('format')?.value || 'csv',
                    preset: preset || document.getElementById('preset')?.value,
                    start: document.getElementById('start_date')?.value || null,
                    end: document.getElementById('end_date')?.value || null,
                })
            ">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.group name="preset">
                        <x-form.label name="preset" required>Preset Date Range</x-form.label>
                        <x-form.select name="preset" id="preset" x-model="preset" required>
                            <option value="">-- Select --</option>
                            <option value="week" {{ old('preset') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ old('preset') == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="quarter" {{ old('preset') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                            <option value="year" {{ old('preset') == 'year' ? 'selected' : '' }}>This Year</option>
                            <option value="custom" {{ old('preset') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </x-form.select>
                        @error('preset')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </x-form.group>

                    <x-form.group name="format">
                        <x-form.label name="format" required>Export Format</x-form.label>
                        <x-form.select id="format" name="format" required>
                            <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                            <option value="xlsx" {{ old('format') == 'xlsx' ? 'selected' : '' }}>Excel</option>
                            <option value="json" {{ old('format') == 'json' ? 'selected' : '' }}>JSON</option>
                        </x-form.select>
                        @error('format')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </x-form.group>

                    <div x-show="preset === 'custom'" x-transition>
                        <x-form.label name="start_date" required>Start Date</x-form.label>
                        <x-form.input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" placeholder="YYYY-MM-DD" />
                        @error('start_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="preset === 'custom'" x-transition>
                        <x-form.label name="end_date" required>End Date</x-form.label>
                        <x-form.input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" placeholder="YYYY-MM-DD" />
                        @error('end_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#5E0F0F] text-white rounded-xl shadow-sm hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30 cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up mr-2"></i>
                        Queue Export
                    </button>
                    <button type="button" @click="preset=''; document.getElementById('start_date').value=''; document.getElementById('end_date').value='';" class="px-4 py-2.5 rounded-xl border text-sm cursor-pointer">Clear</button>
                </div>
            </form>
        </div>

        <!-- Progress/Status Card -->
        <div class="bg-white shadow rounded-2xl p-6">
            <h3 class="text-base font-semibold mb-3">Export Status</h3>
            @include('components.admin.export-progress', [ 'type' => 'csv' ])
        </div>
    </div>
    <!-- Recent downloads (full-width) -->
    <div class="mt-6 bg-white shadow rounded-2xl p-6" x-data="recentDownloads()" x-init="init()">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold">Recent downloads</h3>
        </div>
        <div class="rounded-2xl border overflow-hidden">
            <template x-for="item in items" :key="item.id">
                <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                            <i class="fa-solid fa-file-arrow-down text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium truncate" x-text="item.filename || ('Export ' + item.id)"></div>
                            <div class="text-[11px] text-gray-500" x-text="new Date(item.completed_at).toLocaleString()"></div>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-full border whitespace-nowrap" :class="(item.format||'').toLowerCase()==='csv' ? 'bg-blue-50 text-blue-700 border-blue-200' : ((item.format||'').toLowerCase()==='xlsx' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-700 border-gray-200')" x-text="(item.format||'').toUpperCase() || '—'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] px-2 py-0.5 rounded-full border" :class="item.expired ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200'" x-text="item.expired ? 'Expired' : 'Active'"></span>
                        <a :href="item.download_url" :class="(item.expired || !item.file_exists) ? 'pointer-events-none opacity-50' : ''" class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg border shadow-sm">
                            <i class="fa-solid fa-download"></i>
                            Download
                        </a>
                    </div>
                </div>
            </template>
            <div class="px-4 py-10 text-center text-gray-500" x-show="!loading && items.length===0">No downloads yet.</div>
            <div class="px-4 py-10 text-center text-gray-500" x-show="loading">Loading…</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function recentDownloads(){
    return {
        items: [], loading: true,
        async init(){
            await this.reload();
            // Refresh when an export completes
            window.addEventListener('exports:completed', async ()=>{ await this.reload(); });
        },
        async reload(){
            this.loading = true;
            try{
                const res = await fetch('/pbac/exports/recent', { headers: { 'Accept':'application/json' } });
                const json = await res.json();
                this.items = Array.isArray(json.items) ? json.items : [];
            }catch(e){ /* swallow */ }
            this.loading = false;
        }
    }
}
</script>
@endpush
