@extends('layouts.admin.app')

@section('navbar_title', 'PBAC Export Logs')
@section('navbar_subtitle', 'Audit trail of researcher exports')

@section('content')
<div x-data="logsTable()" x-init="init()" class="log_menu">
    <div class="w-full flex flex-col gap-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                    <i class="fa-solid fa-clipboard-list mr-1.5"></i>
                    <span>Total: </span>&nbsp;<span x-text="total"></span>
                </span>
                <select x-model.number="perPage" @change="fetchData(1)"
                 class="border border-gray-300 text-sm shadow-sm px-1 py-2.5 rounded-lg focus:border-[#555] focus:ring-1 focus:outline-0 focus:ring-[#5E0F0F]/20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative w-full md:w-auto">
                    <x-form.input name="Search" type="text" required x-model="search"
                     @keydown.enter.prevent="fetchData(1)" @blur="fetchData(1)" type="text" placeholder="Search by user, format, or description"
                           class="w-full md:!w-80 pl-10" />
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                </div>
                <x-form.select name="preset" id="preset" x-model="preset" required x-model="format" @change="fetchData(1)" class="md:!w-44">
                    <option value="">All Formats</option>
                    <option value="csv">CSV</option>
                    <option value="xlsx">Excel</option>
                    <option value="json">JSON</option>
                </x-form.select>
                <x-form.select name="preset" id="preset" x-model="preset" required x-model="status" @change="fetchData(1)" class="md:!w-44">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </x-form.select>
            </div>
        </div>

        <div class="bg-white shadow rounded-xl p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed text-sm">
                    <colgroup>
                        <col style="width:20%">
                        <col style="width:10%">
                        <col style="width:12%">
                        <col style="width:33%">
                        <col style="width:15%">
                        <col style="width:10%">
                    </colgroup>
                    <thead class="bg-[#3C0606] text-white border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">User</th>
                            <th class="px-6 py-3 text-left">Format</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">File</th>
                            <th class="px-6 py-3 text-left">Logged At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="row in rows" :key="row.id">
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-3 align-middle" x-text="row.user || 'N/A'"></td>
                                <td class="px-6 py-3 align-middle uppercase" x-text="row.format || '?' "></td>
                                <td class="px-6 py-3 align-middle">
                                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] sm:text-xs font-medium"
                                          :class="row.status==='completed' ? 'bg-green-50 text-green-700 border-green-200' : (row.status==='failed' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200')">
                                        <span x-text="(row.status||'').charAt(0).toUpperCase()+ (row.status||'').slice(1)"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-3 align-middle truncate" :title="row.description" x-text="row.description"></td>
                                <td class="px-6 py-3 align-middle">
                                    <template x-if="row.file">
                                        <code class="text-xs" x-text="row.file.split('/').pop()"></code>
                                    </template>
                                    <template x-if="!row.file">
                                        <span class="text-gray-400 text-xs">—</span>
                                    </template>
                                </td>
                                <td class="px-6 py-3 align-middle whitespace-nowrap" x-text="new Date(row.created_at).toLocaleString()"></td>
                            </tr>
                        </template>
                        <tr x-show="loading">
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500">Loading...</td>
                        </tr>
                        <tr x-show="!loading && rows.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-500">
                                    <i class="fa-regular fa-folder-open text-2xl"></i>
                                    <div class="text-sm">No results match your filters.</div>
                                    <button @click="clearFilters()" class="mt-1 px-3 py-1.5 rounded-lg border text-xs">Clear filters</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between p-4 pt-8 bg-gray-50">
                <div class="text-xs text-gray-600">
                    Page <span x-text="page"></span> of <span x-text="lastPage"></span>
                </div>
                <nav class="flex items-center gap-1 pagintion" aria-label="Pagination">
                    <button @click="go(1)" :disabled="page===1" class="px-2.5 py-1.5 rounded-lg border text-sm disabled:opacity-50 cursor-pointer">«</button>
                    <button @click="prev()" :disabled="page<=1" class="px-2.5 py-1.5 rounded-lg border text-sm disabled:opacity-50 cursor-pointer">‹</button>
                    <template x-for="p in pages()" :key="p.key">
                        <span x-show="p.type==='gap'" class="px-2 text-gray-400">…</span>
                        <button x-show="p.type==='page'" @click="go(p.num)" :class="p.num===page ? 'bg-[#5E0F0F] text-white' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-3 py-1.5 rounded-lg border text-sm cursor-pointer" x-text="p.num"></button>
                    </template>
                    <button @click="next()" :disabled="page>=lastPage" class="px-2.5 py-1.5 rounded-lg border text-sm disabled:opacity-50 cursor-pointer">›</button>
                    <button @click="go(lastPage)" :disabled="page===lastPage" class="px-2.5 py-1.5 rounded-lg border text-sm disabled:opacity-50 cursor-pointer">»</button>
                </nav>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function logsTable(){
    return {
        rows: [], page: 1, perPage: Number(new URLSearchParams(window.location.search).get('per_page'))||10, total: 0, lastPage: 1, loading:false,
        search: new URLSearchParams(window.location.search).get('search')||'',
        format: new URLSearchParams(window.location.search).get('format')||'',
        status: new URLSearchParams(window.location.search).get('status')||'',
        debounceId: null,
        async init(){
            this.$watch('search', (val)=>{
                clearTimeout(this.debounceId);
                this.debounceId = setTimeout(()=>{ this.fetchData(1); }, 300);
            });
            await this.fetchData(1);
        },
        async fetchData(p){
            this.loading = true; this.page = p;
            const params = new URLSearchParams({ ajax: '1', page: this.page, per_page: this.perPage, search: this.search, format: this.format, status: this.status });
            const res = await fetch(`/logs?${params.toString()}`, { headers: { 'Accept':'application/json' } });
            const json = await res.json();
            this.rows = json.data || []; this.total = json.meta.total; this.lastPage = json.meta.last_page; this.loading = false;
        },
        next(){ if(this.page < this.lastPage) this.fetchData(this.page+1); },
        prev(){ if(this.page > 1) this.fetchData(this.page-1); },
        go(n){ if(n>=1 && n<=this.lastPage) this.fetchData(n); },
        pages(){
            const total = this.lastPage; const cur = this.page; const out = [];
            const addPage = (n)=> out.push({ type:'page', num:n, key:`p-${n}` });
            const addGap = ()=> out.push({ type:'gap', key:`g-${out.length}` });
            if (total <= 7){ for(let i=1;i<=total;i++) addPage(i); return out; }
            addPage(1);
            if (cur > 3) addGap();
            for (let i=Math.max(2, cur-1); i<=Math.min(total-1, cur+1); i++) addPage(i);
            if (cur < total-2) addGap();
            addPage(total);
            return out;
        },
        clearFilters(){ this.search='', this.format='', this.status=''; this.fetchData(1); }
    }
}
</script>
@endpush
@endsection
