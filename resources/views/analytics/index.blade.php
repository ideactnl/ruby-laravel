@extends('layouts.admin.app')

@section('navbar_title', 'ADMIN CONSOLE - ANALYTICS')
@section('navbar_subtitle', 'Participant interaction metrics')

@section('content')
    <div x-data="analyticsTable()" x-init="init()" class="analytics_view">
        <div class="w-full flex flex-col gap-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <x-form.select name="per_page" x-model.number="perPage" @change="fetchData(1)" :enhanced="false"
                        variant="participant">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </x-form.select>
                    <span
                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                        <i class="fa-solid fa-chart-simple mr-1.5"></i>
                        <span>Participants: </span>&nbsp;<span x-text="total"></span>
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <x-form.input name="q" x-model="q" @keydown.enter.prevent="fetchData(1)"
                            @blur="fetchData(1)" type="text" placeholder="Search registration number"
                            class="w-80 pl-9" />
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-fixed text-sm">
                        <colgroup>
                            <col style="width:25%">
                            <col style="width:15%">
                            <col style="width:20%">
                            <col style="width:20%">
                            <col style="width:20%">
                        </colgroup>
                        <thead class="bg-primary text-white border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left cursor-pointer select-none"
                                    @click="toggleSort('registration_number')">
                                    Registration ID
                                    <span class="ml-1 text-xs text-gray-400" x-show="sort==='registration_number'"
                                        x-text="dir==='asc' ? '▲' : '▼'"></span>
                                </th>
                                <th class="px-6 py-4 text-left cursor-pointer select-none"
                                    @click="toggleSort('dashboard_visits_count')">
                                    Visits
                                    <span class="ml-1 text-xs text-gray-400" x-show="sort==='dashboard_visits_count'"
                                        x-text="dir==='asc' ? '▲' : '▼'"></span>
                                </th>
                                <th class="px-6 py-4 text-left">Interactions</th>
                                <th class="px-6 py-4 text-left cursor-pointer select-none"
                                    @click="toggleSort('avg_duration_seconds')">
                                    Avg. Session
                                    <span class="ml-1 text-xs text-gray-400" x-show="sort==='avg_duration_seconds'"
                                        x-text="dir==='asc' ? '▲' : '▼'"></span>
                                </th>
                                <th class="px-6 py-4 text-left cursor-pointer select-none"
                                    @click="toggleSort('total_duration_seconds')">
                                    Total Time
                                    <span class="ml-1 text-xs text-gray-400" x-show="sort==='total_duration_seconds'"
                                        x-text="dir==='asc' ? '▲' : '▼'"></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <template x-for="(p, index) in rows" :key="p.id">
                                <tr class="hover:bg-gray-50/70 transition-colors group">
                                    <td class="px-6 py-4 align-middle font-bold text-gray-900"
                                        x-text="p.registration_number"></td>
                                    <td class="px-6 py-4 align-middle">
                                        <span
                                            class="inline-flex items-center px-4 py-1 rounded-full text-xs font-bold bg-primary/5 text-primary border border-primary/10 transition-all group-hover:scale-105"
                                            x-text="p.dashboard_visits_count"></span>
                                    </td>
                                    <td class="px-6 py-4 align-middle relative group/int">
                                        <div class="flex items-center justify-center gap-1 cursor-help border-b border-dotted border-gray-300 pb-0.5 w-fit mx-auto">
                                            <i class="fa-solid fa-chart-line text-[10px] text-blue-500 opacity-60"></i>
                                            <span class="font-bold text-gray-700" x-text="p.interaction_count"></span>
                                            <i class="fa-solid fa-circle-info text-[9px] text-gray-300 ml-0.5"></i>
                                        </div>

                                        <!-- Visits Details Tooltip (Unified Design) -->
                                        <div class="hidden group-hover/int:block absolute z-50 right-full top-0 mr-3 shadow-2xl rounded-xl p-4 bg-white border border-gray-100 min-w-[200px]"
                                            x-show="p.interaction_list && p.interaction_list.length">
                                            <div
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 pb-2">
                                                Section Visits</div>
                                            <div class="space-y-2.5">
                                                <template x-for="visit in p.interaction_list" :key="visit.name">
                                                    <div class="flex items-center justify-between gap-4">
                                                        <span
                                                            class="text-[11px] text-gray-500 font-medium truncate max-w-[120px]"
                                                            x-text="visit.name"></span>
                                                        <span
                                                            class="text-[11px] font-bold text-primary bg-[#FDF8FE] px-2 py-0.5 rounded-md"
                                                            x-text="visit.count"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <div
                                                class="absolute top-4 left-full border-8 border-transparent border-l-white">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle font-medium text-gray-700 text-center"
                                        x-text="p.avg_duration || '0s'"></td>
                                    <td class="px-6 py-4 align-middle relative group/time">
                                        <div class="flex items-center justify-center gap-1 cursor-help border-b border-dotted border-primary/20 pb-0.5 w-fit mx-auto">
                                            <span class="font-bold text-primary text-base"
                                                x-text="p.total_duration || '0s'"></span>
                                            <i class="fa-solid fa-circle-info text-[9px] text-primary/20 ml-0.5"></i>
                                        </div>

                                        <!-- Time Breakdown Tooltip (Unified Design - Matches Interactions) -->
                                        <div class="hidden group-hover/time:block absolute z-50 right-full top-0 mr-3 shadow-2xl rounded-xl p-4 bg-white border border-gray-100 min-w-[240px]"
                                            x-show="p.section_breakdown && p.section_breakdown.length">
                                            <div
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 border-b border-gray-100 pb-2">
                                                Time on Page Breakdown</div>
                                            <div class="space-y-2.5">
                                                <template x-for="sec in p.section_breakdown" :key="sec.name">
                                                    <div class="flex items-center justify-between gap-4">
                                                        <span
                                                            class="text-[11px] text-gray-500 font-medium truncate max-w-[140px]"
                                                            x-text="sec.name"></span>
                                                        <span
                                                            class="text-[11px] font-bold text-primary bg-[#FDF8FE] px-2 py-0.5 rounded-md whitespace-nowrap"
                                                            x-text="sec.duration"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="absolute top-4 left-full border-8 border-transparent border-l-white"></div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="loading">
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Fetching analytics data...
                                </td>
                            </tr>
                            <tr x-show="!loading && rows.length === 0">
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <i class="fa-regular fa-folder-open text-3xl"></i>
                                        <div class="text-sm font-medium mt-2">No results found for your search.</div>
                                        <button @click="clearSearch()"
                                            class="mt-3 px-4 py-2 rounded-lg border text-xs bg-white hover:bg-gray-50 transition-colors shadow-sm">Reset
                                            filters</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between p-4 border-t bg-gray-50/50" x-show="lastPage > 1">
                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        Page <span x-text="page" class="text-primary"></span> of <span x-text="lastPage"></span>
                    </div>
                    <nav class="flex items-center gap-1" aria-label="Pagination">
                        <button @click="go(1)" :disabled="page === 1"
                            class="px-2.5 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm disabled:opacity-30 disabled:cursor-not-allowed shadow-sm transition-all">«</button>
                        <button @click="prev()" :disabled="page <= 1"
                            class="px-2.5 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm disabled:opacity-30 disabled:cursor-not-allowed shadow-sm transition-all">‹</button>
                        <template x-for="p in pages()" :key="p.key">
                            <span x-show="p.type==='gap'" class="px-2 text-gray-400">…</span>
                            <button x-show="p.type==='page'" @click="go(p.num)"
                                :class="p.num === page ? 'bg-primary text-white border-primary shadow-md' :
                                    'bg-white text-gray-700 hover:bg-gray-100 border-gray-200'"
                                class="px-3 py-1.5 rounded-lg border text-sm cursor-pointer font-bold transition-all"
                                x-text="p.num"></button>
                        </template>
                        <button @click="next()" :disabled="page >= lastPage"
                            class="px-2.5 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm disabled:opacity-30 disabled:cursor-not-allowed shadow-sm transition-all">›</button>
                        <button @click="go(lastPage)" :disabled="page === lastPage"
                            class="px-2.5 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm disabled:opacity-30 disabled:cursor-not-allowed shadow-sm transition-all">»</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function analyticsTable() {
                return {
                    rows: [],
                    page: 1,
                    perPage: 10,
                    total: 0,
                    lastPage: 1,
                    loading: false,
                    q: '',
                    sort: 'dashboard_visits_count',
                    dir: 'desc',
                    debounceId: null,
                    async init() {
                        this.$watch('q', (val) => {
                            clearTimeout(this.debounceId);
                            this.debounceId = setTimeout(() => {
                                this.fetchData(1);
                            }, 300);
                        });
                        await this.fetchData(1);

                        // Auto-refresh every 30 seconds to show live duration updates
                        setInterval(() => {
                            this.fetchData(this.page);
                        }, 30000);
                    },
                    async fetchData(p) {
                        this.loading = true;
                        this.page = p;
                        try {
                            const params = new URLSearchParams({
                                ajax: '1',
                                page: this.page,
                                per_page: this.perPage,
                                q: this.q,
                                sort: this.sort,
                                dir: this.dir
                            });
                            const res = await fetch(`/analytics?${params.toString()}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const json = await res.json();
                            this.rows = json.data || [];
                            this.total = json.meta.total;
                            this.lastPage = json.meta.last_page;
                        } catch (e) {
                            console.error('Analytics fetch error', e);
                        } finally {
                            this.loading = false;
                        }
                    },
                    toggleSort(col) {
                        this.dir = (this.sort === col && this.dir === 'asc') ? 'desc' : 'asc';
                        this.sort = col;
                        this.fetchData(1);
                    },
                    next() {
                        if (this.page < this.lastPage) this.fetchData(this.page + 1);
                    },
                    prev() {
                        if (this.page > 1) this.fetchData(this.page - 1);
                    },
                    go(n) {
                        if (n >= 1 && n <= this.lastPage) this.fetchData(n);
                    },
                    pages() {
                        const total = this.lastPage;
                        const cur = this.page;
                        const out = [];
                        const addPage = (n) => out.push({
                            type: 'page',
                            num: n,
                            key: `p-${n}`
                        });
                        const addGap = () => out.push({
                            type: 'gap',
                            key: `g-${out.length}`
                        });
                        if (total <= 7) {
                            for (let i = 1; i <= total; i++) addPage(i);
                            return out;
                        }
                        addPage(1);
                        if (cur > 3) addGap();
                        for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) addPage(i);
                        if (cur < total - 2) addGap();
                        addPage(total);
                        return out;
                    },
                    clearSearch() {
                        this.q = '';
                        this.fetchData(1);
                    }
                }
            }
        </script>
    @endpush
@endsection
