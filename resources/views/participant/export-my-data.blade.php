@extends('layouts.participant.app')
@section('navbar_title', __('participant.visualise_symptoms_export_data'))
@section('navbar_subtitle', __('participant.export_pbac_data_selected_dates'))


@section('content')
<div class=""
    x-data="{
        preset:'month', start:'', end:'', loading:false, error:'', busy:false,
        async refresh(){
            try { this.loading = true; this.error='';
                await window.RubyExport && window.RubyExport.loadChart(this.preset, this.start, this.end);
            } catch(e) { this.error='Failed to load chart'; } finally { this.loading=false; }
        },
        exportCSV(){
            if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(25); } catch(e) {} }
            window.dispatchEvent(new CustomEvent('export:start', { detail: { type: 'csv', preset: this.preset, start: this.start, end: this.end } }));
        },
        exportPDF(){
            if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(25); } catch(e) {} }
            window.dispatchEvent(new CustomEvent('export:start', { detail: { type: 'pdf', preset: this.preset, start: this.start, end: this.end } }));
        }
    }"
    x-init="() => { const d=new Date(); const s=new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0,10); const e=d.toISOString().slice(0,10); start=s; end=e; $nextTick(()=>refresh()); }"
    @export:busy.window="busy = true"
    @export:idle.window="busy = false"
>
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center rounded-md border border-primary px-3 py-2 text-sm font-semibold text-primary bg-primary/5">{{ __('participant.range') }}</span>
            <x-form.select name="preset" x-model="preset" @change="if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(15); } catch(e) {} }; refresh()" variant="participant" :enhanced="false">
                <option value="month">{{ __('participant.this_month') }}</option>
                <option value="quarter">{{ __('participant.this_quarter') }}</option>
                <option value="year">{{ __('participant.this_year') }}</option>
                <option value="custom">{{ __('participant.custom_range') }}</option>
            </x-form.select>
            <span class="ml-1 text-gray-500 text-sm" x-show="loading">{{ __('participant.loading_chart') }}</span>
        </div>
        <div class="flex items-center gap-6" x-show="preset==='custom'">
            <div class="flex items-center gap-2">
                <!-- Mobile: Native date input -->
                <input type="date" x-model="start" @change="if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(15); } catch(e) {} }; refresh()" 
                       class="md:hidden rounded-md border border-primary px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/30" />
                <!-- Desktop: Flatpickr -->
                <x-form.input type="date" name="start_date" x-model="start" @change="if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(15); } catch(e) {} }; refresh()" variant="participant" placeholder="dd/mm/yyyy" class="hidden md:block" />
            </div>
            <div class="flex items-center gap-2">
                <!-- Mobile: Native date input -->
                <input type="date" x-model="end" @change="if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(15); } catch(e) {} }; refresh()" 
                       class="md:hidden rounded-md border border-primary px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/30" />
                <!-- Desktop: Flatpickr -->
                <x-form.input type="date" name="end_date" x-model="end" @change="if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(15); } catch(e) {} }; refresh()" variant="participant" placeholder="dd/mm/yyyy" class="hidden md:block" />
            </div>
        </div>
    </div>

    <template x-if="error">
        <p class="text-sm text-red-600 mb-2" x-text="error"></p>
    </template>

    <div class="bg-white rounded-lg border border-gray-100 p-4">
        <div id="exportLegend" class="flex flex-wrap items-center gap-6 px-2 pb-2"></div>
        <div class="overflow-x-auto overflow-y-hidden h-[320px] md:h-[480px] w-full scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <div class="min-w-[800px] h-full relative">
                <canvas id="exportChart" class="w-full h-full"></canvas>
                <!-- Scroll indicator for mobile -->
                <div id="scrollIndicator" class="md:hidden absolute top-2 right-2 bg-black/20 text-white text-xs px-2 py-1 rounded-full pointer-events-none transition-opacity duration-500">
                    {{ __('participant.scroll_indicator') }}
                </div>
            </div>
        </div>
        <div class="mt-8 flex items-center flex-wrap md:justify-end gap-3">
            <button @click="exportPDF()" :disabled="busy" :class="busy ? 'opacity-60 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                {{ __('participant.export_as_pdf') }}
                <i class="fa-solid fa-file-pdf"></i>
            </button>
            <button @click="exportCSV()" :disabled="busy" :class="busy ? 'opacity-60 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                {{ __('participant.export_as_csv') }}
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

@push('scripts')
<script>
window.healthDomainTranslations = {
    'blood_loss': '{{ __('participant.blood_loss') }}',
    'pain': '{{ __('participant.pain') }}',
    'impact': '{{ __('participant.impact') }}',
    'general_health': '{{ __('participant.general_health') }}',
    'mood': '{{ __('participant.mood') }}',
    'stool_urine': '{{ __('participant.stool_urine') }}',
    'sleep': '{{ __('participant.sleep') }}',
    'diet': '{{ __('participant.diet') }}',
    'exercise': '{{ __('participant.exercise') }}',
    'sex': '{{ __('participant.sexual_health') }}',
    'notes': '{{ __('participant.notes') }}'
};
</script>
@endpush
