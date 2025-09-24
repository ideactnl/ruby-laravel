@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - EXPORT DATA')
@section('navbar_subtitle', 'Export Your PBAC Data Based on the Selected Dates')

@section('content')
<div class="px-0 pr-4 sm:pr-6 lg:px-8 py-6"
    x-data="{
        preset:'month', start:'', end:'', loading:false, error:'', busy:false,
        async refresh(){
            try { this.loading = true; this.error='';
                await window.RubyExport && window.RubyExport.loadChart(this.preset, this.start, this.end);
            } catch(e) { this.error='Failed to load chart'; } finally { this.loading=false; }
        },
        exportCSV(){
            window.dispatchEvent(new CustomEvent('export:start', { detail: { type: 'csv', preset: this.preset, start: this.start, end: this.end } }));
        },
        exportPDF(){
            window.dispatchEvent(new CustomEvent('export:start', { detail: { type: 'pdf', preset: this.preset, start: this.start, end: this.end } }));
        }
    }"
    x-init="() => { const d=new Date(); const s=new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0,10); const e=d.toISOString().slice(0,10); start=s; end=e; $nextTick(()=>refresh()); }"
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
        <x-participant.export-progress type="pdf" chartCanvasId="exportChart" />
        <x-participant.export-progress type="csv" />
    </div>
</div>
@endsection
