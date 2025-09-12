@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - MONTHLY OVERVIEW')
@section('navbar_subtitle', 'Monthly Overview Showing The Relationship Between Selected Domains')

@section('content')
<div class="px-0 pr-4 sm:pr-6 lg:pr-8 py-6" x-data="filterMenu()" x-init="init()">
    <div class="mb-3 grid grid-cols-1 sm:grid-cols-3 items-center gap-3">
        <div class="justify-self-start">
            <h2 id="cal-month-label" class="text-xl sm:text-2xl font-extrabold tracking-tight text-gray-900">&nbsp;</h2>
        </div>
        <div class="justify-self-center">
            <div class="inline-flex items-center gap-0">
                <button id="btn-today" class="mr-[10px] bg-[#5E0F0F] text-white px-[15px] py-[9px] text-sm font-semibold shadow hover:opacity-90 rounded cursor-pointer">Today</button>
                <button id="btn-prev" class="bg-[#5E0F0F] text-white px-[15px] py-[9px] text-sm font-semibold shadow hover:opacity-90 rounded-l-[6px] rounded-r-[0px] cursor-pointer" aria-label="Previous month">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button id="btn-next" class="bg-[#5E0F0F] text-white px-[15px] py-[9px] text-sm font-semibold shadow hover:opacity-90 rounded-r-[6px] rounded-l-[0px] cursor-pointer" aria-label="Next month">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div class="justify-self-end">
            <div class="relative" @click.outside="open=false">
                <button @click="open=!open" class="inline-flex items-center gap-2 rounded-md bg-[#5E0F0F] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                    Select Domains
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div x-show="open" x-transition class="absolute right-0 z-50 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-2 shadow-lg" style="display:none">
                    <div class="flex items-center justify-between px-2 pb-2 border-b border-gray-100">
                        <button class="text-xs font-medium text-gray-700 hover:text-gray-900" @click="selectAll()">Select All</button>
                        <button class="text-xs font-medium text-red-600 hover:text-red-700" @click="clearAll()">Clear All</button>
                    </div>
                    <template x-for="opt in options" :key="opt.value">
                        <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-2 text-sm hover:bg-gray-50">
                            <input type="checkbox" class="rounded border-gray-300" :value="opt.value" x-model="selected" @change="apply()"/>
                            <span class="inline-flex h-2.5 w-2.5 rounded" :style="`background:${opt.color}`"></span>
                            <span x-text="opt.label"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>
    </div>
    <div id="participantCalendar" class="p-0"></div>
</div>
@endsection
