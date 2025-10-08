@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - MONTHLY OVERVIEW')
@section('navbar_subtitle', 'Monthly Overview Showing The Relationship Between Selected Domains')

@section('content')
    <div class="" x-data="filterMenu()" x-init="init()">
        <div class="mb-10 flex items-center justify-between">
            <!-- Mobile: Dynamic date layout -->
            <div class="md:hidden">
                <div class="flex items-center gap-2">
                    <div id="mobile-date" class="dashboard-date text-xl font-600 text-primary leading-none">
                        {{ now()->format('d') }}</div>
                    <div class="dashboard-my flex flex-col items-center leading-tight">
                        <div id="mobile-month" class="text-lg font-semibold text-gray-900 uppercase leading-none">
                            {{ now()->format('M') }}</div>
                        <div class="w-full h-px bg-gray-500 my-0.5"></div>
                        <div id="mobile-year" class="text-xl font-medium text-gray-900 leading-none">
                            {{ now()->format('Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Desktop: Month label -->
            <h2 id="cal-month-label" x-cloak
                class="hidden md:block text-xl sm:text-2xl font-extrabold tracking-tight text-gray-900">&nbsp;</h2>
            <div class="relative" @click.outside="open=false" x-cloak>
                <button @click="open=!open"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-4 md:px-4 md:py-2 text-sm font-semibold text-white shadow hover:bg-primary-800 cursor-pointer transition-colors">
                    <span>Domains (<span x-cloak x-text="selected.length"></span>/3)</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M6 9l6 6 6-6" />
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition
                    class="absolute right-0 z-50 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-2 shadow-lg"
                    style="display:none">
                    <div class="flex items-center justify-between px-2 pb-2 border-b border-gray-100">
                        <button class="text-xs font-medium text-gray-700 hover:text-gray-900" @click="selectAll()">Select
                            All</button>
                        <button class="text-xs font-medium text-red-600 hover:text-red-700" @click="clearAll()">Clear
                            All</button>
                    </div>
                    <div class="px-2 pt-2 text-[11px] text-gray-600">
                        You can select up to <span class="font-semibold">3</span> domains.
                    </div>

                    <template x-for="opt in options" :key="opt.value">
                        <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-2 text-sm hover:bg-gray-50">
                            <input type="checkbox" class="rounded border-gray-300" :value="opt.value"
                                :checked="selected.includes(opt.value)" @change="toggle(opt.value)"
                                :disabled="!selected.includes(opt.value) && selected.length >= 3" />
                            <template x-if="opt.iconCls">
                                <i class="fa-solid text-base" :class="opt.iconCls"></i>
                            </template>
                            <template x-if="!opt.iconCls">
                                <span class="inline-flex h-2.5 w-2.5 rounded" :style="`background:${opt.color}`"></span>
                            </template>
                            <span x-text="opt.label"></span>
                            <span class="ml-auto text-[10px] text-gray-400"
                                x-show="!selected.includes(opt.value) && selected.length>=3">Max 3</span>
                        </label>
                    </template>
                </div>
            </div>
        </div>
        <div class="relative">
            <div id="participantCalendar" class="p-0"></div>
        </div>

        <!-- Back to current month button - bottom right below calendar -->
        <div class="mt-4 flex justify-end">
            <button id="btn-back-current" title="Back to current month" aria-label="Back to current month" x-cloak
                class="hidden inline-flex items-center gap-2 rounded-full bg-primary text-white shadow-lg hover:bg-primary-800 px-4 py-3 cursor-pointer transition-colors">
                <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                <span class="text-sm font-medium">Back to current month</span>
            </button>
        </div>
    </div>
@endsection
