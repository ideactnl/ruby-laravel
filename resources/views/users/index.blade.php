@extends('layouts.admin.app')

@section('navbar_title', 'Users')
@section('navbar_subtitle', 'Manage application users')

@section('content')
<div x-data="usersTable()" x-init="init()" class="user_s">
    <div class="w-full flex flex-col gap-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                    <i class="fa-solid fa-users mr-1.5"></i>
                    <span>Total: </span>&nbsp;<span x-text="total"></span>
                </span>
                <select x-model.number="perPage" @change="fetchData(1)" class="border border-gray-300 rounded-xl p-2 text-sm shadow-sm">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                 <div class="relative">
                    <x-form.input name="Search" type="text" x-model="q" @keydown.enter.prevent="fetchData(1)" @blur="fetchData(1)" type="text" placeholder="Search name or email"
                           class="!w-60 pl-10" />
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                </div>

                <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 bg-[#5E0F0F] text-white px-4 py-2 rounded-xl shadow hover:opacity-90">
                    <i class="fa-solid fa-user-plus"></i>
                    New User
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed text-sm">
                    <colgroup>
                        <col style="width:30%">
                        <col style="width:35%">
                        <col style="width:20%">
                        <col style="width:15%">
                    </colgroup>
                    <thead class="bg-[#3C0606] text-white border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left cursor-pointer select-none" @click="toggleSort('name')">Name <span class="ml-1 text-xs text-gray-400" x-show="sort==='name'" x-text="dir==='asc' ? '▲' : '▼'"></span></th>
                            <th class="px-6 py-3 text-left cursor-pointer select-none" @click="toggleSort('email')">Email <span class="ml-1 text-xs text-gray-400" x-show="sort==='email'" x-text="dir==='asc' ? '▲' : '▼'"></span></th>
                            <th class="px-6 py-3 text-left">Roles</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="u in rows" :key="u.id">
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-3 align-middle font-semibold truncate" x-text="u.name"></td>
                                <td class="px-6 py-3 align-middle text-gray-700 truncate" x-text="u.email"></td>
                                <td class="px-6 py-3 align-middle">
                                    <template x-for="r in u.roles" :key="r">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 mr-1" x-text="r"></span>
                                    </template>
                                </td>
                                <td class="px-6 py-3 align-middle text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a :href="`/users/${u.id}/edit`" class="inline-flex items-center justify-center h-8 w-8 rounded-md hover:bg-blue-50 text-blue-600 cursor-pointer" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button @click="requestDelete(u)" class="inline-flex items-center justify-center h-8 w-8 rounded-md hover:bg-red-50 text-red-600 cursor-pointer" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="loading">
                            <td colspan="4" class="px-6 py-6 text-center text-gray-500">Loading...</td>
                        </tr>
                        <tr x-show="!loading && rows.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-500">
                                    <i class="fa-regular fa-folder-open text-2xl"></i>
                                    <div class="text-sm">No users found.</div>
                                    <button @click="clearSearch()" class="mt-1 px-3 py-1.5 rounded-lg border text-xs">Clear search</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between px-4 py-3 border-t bg-gray-50" x-show="lastPage > 1">
                <div class="text-xs text-gray-600">
                    Page <span x-text="page"></span> of <span x-text="lastPage"></span>
                </div>
                <nav class="flex items-center gap-1" aria-label="Pagination">
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
function usersTable(){
    return {
        rows: [], page: 1, perPage: Number(new URLSearchParams(window.location.search).get('per_page'))||10, total: 0, lastPage: 1,
        q: new URLSearchParams(window.location.search).get('q')||'',
        sort: 'created_at', dir: 'desc', loading: false,
        debounceId: null,
        async init(){
            this.$watch('q', (val)=>{
                clearTimeout(this.debounceId);
                this.debounceId = setTimeout(()=>{ this.fetchData(1); }, 300);
            });
            await this.fetchData(this.page);
        },
        async fetchData(p){
            this.loading = true; this.page = p;
            const params = new URLSearchParams({ ajax: '1', page: this.page, per_page: this.perPage, q: this.q, sort: this.sort, dir: this.dir });
            const res = await fetch(`/users?${params.toString()}`, { headers: { 'Accept':'application/json' } });
            const json = await res.json();
            this.rows = json.data || []; this.total = json.meta.total; this.lastPage = json.meta.last_page; this.loading = false;
        },
        toggleSort(col){ this.dir = (this.sort===col && this.dir==='asc') ? 'desc' : 'asc'; this.sort = col; this.fetchData(1); },
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
        clearSearch(){ this.q=''; this.fetchData(1); },
        requestDelete(u){
            window.dispatchEvent(new CustomEvent('confirm-delete', {
                detail: {
                    title: 'Delete user',
                    message: `Are you sure you want to delete "${u.name}"? This action cannot be undone.`,
                    action: `/users/${u.id}`,
                    method: 'DELETE'
                }
            }));
        }
    }
}
</script>
@endpush
@endsection
