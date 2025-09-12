@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - EXPORT DATA')
@section('navbar_subtitle', 'Export Your PBAC Data Based on the Selected Dates')

@section('content')
<div class="px-0 pr-4 sm:pr-6 lg:px-8 py-6" x-data="{ preset:'month', start:'', end:'', loading:false, error:'', async refresh(){ try{ this.loading=true; this.error=''; await window.RubyExport && window.RubyExport.loadChart(this.preset, this.start, this.end); }catch(e){ this.error='Failed to load chart'; } finally{ this.loading=false; } }, exportCSV(){ window.RubyExport && window.RubyExport.exportCSV(this.preset, this.start, this.end); }, exportPDF(){ window.RubyExport && window.RubyExport.exportPDF(this.preset, this.start, this.end); } }" x-init="() => { const d=new Date(); const s=new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0,10); const e=d.toISOString().slice(0,10); start=s; end=e; $nextTick(()=>refresh()); }">
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-md border border-[#5E0F0F] px-3 py-2 text-sm font-semibold text-[#5E0F0F] bg-[#5E0F0F]/5">Range</span>
            <div class="relative">
                <select x-model="preset" @change="refresh()" class="appearance-none rounded-md border border-[#5E0F0F] text-[#5E0F0F] bg-white px-3 py-2 pr-9 text-sm font-medium shadow-sm hover:bg-[#5E0F0F]/5 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30">
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-[#5E0F0F]">
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </span>
            </div>
            <span class="ml-1 text-gray-500 text-sm" x-show="loading">Loading chart...</span>
        </div>
        <div class="flex items-center gap-6" x-show="preset==='custom'">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Start</label>
                <input type="date" x-model="start" @change="refresh()" class="rounded-md border border-[#5E0F0F] px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30" />
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">End</label>
                <input type="date" x-model="end" @change="refresh()" class="rounded-md border border-[#5E0F0F] px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30" />
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
            <button @click="exportPDF()" class="inline-flex items-center gap-2 rounded-md bg-[#5E0F0F] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                Export as PDF
                <i class="fa-solid fa-file-pdf"></i>
            </button>
            <button @click="exportCSV()" class="inline-flex items-center gap-2 rounded-md bg-[#5E0F0F] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                Export as CSV
                <i class="fa-solid fa-file-csv"></i>
            </button>
        </div>
    </div>
</div>
@endsection
