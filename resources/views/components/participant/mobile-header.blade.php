{{-- <div class="md:hidden sticky top-0 z-50 bg-[#FFF7F8] px-4 py-3">
    <!-- Top Row -->
    <div class="flex items-center justify-between">
        <img src="{{ asset('images/logo.png') }}" class="h-8" />

        <button class="bg-primary text-white px-4 py-2 rounded-lg flex items-center gap-2">
            Select Down
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Date Navigation -->
    <div class="flex items-center justify-center gap-6 mt-4">
        <button>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <span class="font-semibold text-sm">
            {{ now()->format('d-m-Y') }}
        </span>

        <button>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</div> --}}



<div class="md:hidden sticky top-0 z-50 bg-[#FFF7F8] px-4 py-3" x-data="filterMenu()" x-init="init()">
    <div class="flex items-center justify-between">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="Logo"
            class="h-8 object-contain"
        />

@if (request()->routeIs('participant.dashboard'))

            <div class="relative flex-shrink-0" @click.outside="open=false" x-cloak>
                <button
                    @click="open=!open; if(window.innerWidth <= 768 && 'vibrate' in navigator) { try { navigator.vibrate(10); } catch(e) {} }"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-4 md:px-4 md:py-2 text-sm font-semibold text-white shadow hover:bg-primary-800 cursor-pointer transition-colors">
                    <span>{{ __('participant.domains') }} (<span x-cloak x-text="selected.length"></span>/3)</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M6 9l6 6 6-6" />
                    </svg>
                </button>

                <div x-cloak x-show="open" x-transition
                    class="absolute right-0 z-50 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-2 shadow-lg"
                    style="display:none">

                    <div class="flex items-center justify-between px-2 pb-2 border-b border-gray-100">
                        <button class="text-xs font-medium text-gray-700 hover:text-gray-900"
                            @click="selectAll()">{{ __('participant.reset') }}</button>
                        <button class="text-xs font-medium text-red-600 hover:text-red-700"
                            @click="clearAll()">{{ __('participant.clear') }}</button>
                    </div>

                    <div class="px-2 pt-2 text-[11px] text-gray-600">
                        {{ __('participant.you_can_select_up_to_3_domains') }}
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

@endif
    </div>
</div>

