@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - MONTHLY OVERVIEW')
@section('navbar_subtitle', 'Monthly Overview Showing The Relationship Between Selected Domains')

@section('content')
    <div x-data="filterMenu()" x-init="init()">
        <div class="mb-10 flex items-center justify-between">
            <!-- Mobile: Date with Navigation -->
            <div class="md:hidden flex items-center gap-2 flex-1 min-w-0">
                <!-- Previous Month Button -->
                <button id="btn-prev-month"
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors flex-shrink-0"
                    title="Previous month" aria-label="Previous month">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Date Display -->
                <div id="mobile-date-container"
                    class="flex items-center gap-1 sm:gap-2 cursor-pointer hover:opacity-80 transition-opacity p-1 -m-1 sm:p-2 sm:-m-2 rounded-lg hover:bg-gray-50 flex-shrink-0"
                    title="Tap to go to current month" onclick="goToCurrentMonth()">
                    <div id="mobile-date"
                        class="dashboard-date text-xl sm:text-2xl md:text-3xl font-bold text-primary leading-none">
                        {{ now()->format('d') }}
                    </div>
                    <div class="dashboard-my flex flex-col items-center leading-tight">
                        <div id="mobile-month"
                            class="text-sm sm:text-base md:text-lg font-semibold text-gray-900 uppercase leading-none">
                            {{ now()->format('M') }}
                        </div>
                        <div class="w-full h-px bg-gray-500 my-0.5"></div>
                        <div id="mobile-year"
                            class="text-base sm:text-lg md:text-xl font-medium text-gray-900 leading-none">
                            {{ now()->format('Y') }}
                        </div>
                    </div>
                </div>

                <!-- Next Month Button -->
                <button id="btn-next-month"
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors flex-shrink-0"
                    title="Next month" aria-label="Next month">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Desktop: Month label -->
            <h2 id="cal-month-label" x-cloak
                class="hidden md:block text-xl sm:text-2xl font-extrabold tracking-tight text-gray-900">&nbsp;
            </h2>

            <!-- Desktop: Navigation Buttons -->
            <div class="hidden md:flex items-center gap-2">
                <button id="btn-prev-month-desktop"
                    class="w-40 px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary-800 transition-colors whitespace-nowrap"
                    title="Previous month" aria-label="Previous month">
                    <i class="fa-solid fa-angles-left"></i> Previous Month
                </button>
                <button id="btn-next-month-desktop"
                    class="w-40 px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary-800 transition-colors whitespace-nowrap"
                    title="Next month" aria-label="Next month">
                    Next Month <i class="fa-solid fa-angles-right"></i>
                </button>
            </div>

            <!-- Domain Filter Dropdown -->
            <div class="relative flex-shrink-0" @click.outside="open=false" x-cloak>
                <button
                    @click="open=!open; if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(10); } catch(e) {} }"
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
                        <button class="text-xs font-medium text-gray-700 hover:text-gray-900"
                            @click="selectAll()">Reset</button>
                        <button class="text-xs font-medium text-red-600 hover:text-red-700"
                            @click="clearAll()">Clear</button>
                    </div>

                    <div class="px-2 pt-2 text-[11px] text-gray-600">
                        You can select up to <span class="font-semibold">3</span> domains.
                    </div>

                    <template x-for="opt in options" :key="opt.value">
                        <label
                            class="flex cursor-pointer items-center gap-2 rounded px-2 py-2 text-sm transition-all duration-200"
                            :class="{
                                'hover:bg-gray-50': !(!selected.includes(opt.value) && selected.length >= 3),
                                'opacity-50 cursor-not-allowed': !selected.includes(opt.value) && selected.length >= 3,
                                'shake': shakeTarget === opt.value
                            }"
                            @click="
                                if (!selected.includes(opt.value) && selected.length >= 3) {
                                    $event.preventDefault();
                                    shakeTarget = opt.value;
                                    triggerHaptic('error');
                                    setTimeout(() => shakeTarget = null, 400);
                                }
                            ">
                            <!-- Original Checkbox -->
                            <div class="w-5 h-5 flex items-center justify-center rounded border border-gray-300 cursor-pointer transition-all duration-300 ease-in-out transform hover:scale-105"
                                :class="{
                                    'bg-primary border-primary shadow-md': selected.includes(opt.value),
                                    'bg-white hover:border-primary/50': !selected.includes(opt.value)
                                }">
                                <svg x-show="selected.includes(opt.value)"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-75"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-75" xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <!-- Grid Icon -->
                            <img :src="opt.iconSrc"
                                class="w-5 h-5 object-contain transition-all duration-300 ease-in-out transform"
                                :class="{
                                    'opacity-100 scale-110': selected.includes(opt.value),
                                    'opacity-40 hover:opacity-60': !selected.includes(opt.value) && !(!selected
                                        .includes(opt.value) && selected.length >= 3),
                                    'opacity-20': !selected.includes(opt.value) && selected.length >= 3
                                }"
                                :alt="opt.label">

                            <!-- Label -->
                            <span x-text="opt.label" class="transition-all duration-300 ease-in-out"
                                :class="{
                                    'text-gray-400': !selected.includes(opt.value) && selected.length >= 3,
                                    'font-semibold text-gray-900': selected.includes(opt.value),
                                    'text-gray-700': !selected.includes(opt.value) && !(!selected.includes(opt.value) &&
                                        selected.length >= 3)
                                }">
                            </span>

                            <input type="checkbox" class="hidden" :value="opt.value"
                                :checked="selected.includes(opt.value)" @change="toggle(opt.value)"
                                :disabled="!selected.includes(opt.value) && selected.length >= 3" />
                        </label>
                    </template>

                </div>
            </div>
        </div>

        <div class="relative">
            <div id="participantCalendar" class="p-0"></div>
        </div>

        <!-- Back to current month button -->
        <div class="mt-4 flex justify-end">
            <button id="btn-back-current" title="Back to current month" aria-label="Back to current month" x-cloak
                class="hidden inline-flex items-center gap-2 rounded-full bg-primary text-white shadow-lg hover:bg-primary-800 px-4 py-3 cursor-pointer transition-colors">
                <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                <span class="text-sm font-medium">Back to current month</span>
            </button>
        </div>
    </div>
@endsection
