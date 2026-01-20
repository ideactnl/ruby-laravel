@props([])

<div x-data="menstruationWrapped({
    translations: {
        wrapped_header: @js(__('participant.wrapped_header')),
        wrapped_cycle_length: @js(__('participant.wrapped_cycle_length')),
        wrapped_no_cycle_length: @js(__('participant.wrapped_no_cycle_length')),
        wrapped_blood_loss_spotting: @js(__('participant.wrapped_blood_loss_spotting')),
        wrapped_pain_extreme: @js(__('participant.wrapped_pain_extreme')),
        wrapped_impact: @js(__('participant.wrapped_impact'))
    }
})" x-init="init()" class="mb-10 mt-5" x-cloak>

    <div x-show="loading" class="flex justify-center p-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <div x-show="!loading && data" class="space-y-6 animate-in fade-in duration-500">

        <template x-if="data && data.can_calculate">
            <div class="hidden md:block bg-primary/5 rounded-xl p-4 border border-primary/10">
                <h3 class="text-lg font-bold text-gray-900" x-html="getHeaderText()"></h3>
            </div>
        </template>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">

            <!-- 1. Cycle Length -->
            <div class="bg-[#FDF8FE] rounded-2xl px-[10px] py-[6px] shadow-sm border border-primary flex items-center gap-2 hover:shadow-md transition-shadow">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('images/calender.png') }}" class="size-6 object-contain opacity-80 contrast-75 brightness-110">
                </div>

                <div class="flex-1 min-w-0">
                    <template x-if="data && data.can_calculate">
                        <p class="text-sm text-gray-600 leading-tight" x-html="getCycleLengthText()"></p>
                    </template>
                    <template x-if="data && !data.can_calculate">
                        <p class="text-sm text-gray-600 leading-tight">
                            {{ __('participant.wrapped_no_cycle_length') }}
                        </p>
                    </template>
                </div>

                <div class="flex-shrink-0 relative">
                    <img src="{{ asset('images/question.png') }}"
                         class="w-5 h-5 cursor-help hover:opacity-100"
                         :class="activeTooltip === 'cycle' ? 'opacity-100' : 'opacity-60'"
                         @mouseenter="if (window.innerWidth >= 1024) activeTooltip = 'cycle'"
                         @mouseleave="if (window.innerWidth >= 1024) activeTooltip = null"
                         @click.stop="if (window.innerWidth < 1024) toggleTooltip('cycle')">

                    <div x-show="activeTooltip === 'cycle'"
                         x-transition.opacity
                         x-cloak
                         @click.away="activeTooltip = null"
                         class="absolute top-full mt-3 md:top-auto md:bottom-full md:mb-3 right-0 z-[99] w-72 md:w-90 p-3 bg-[#FDF8FE] text-black border-primary border text-xs rounded-lg shadow-xl">
                        <template x-if="data && data.can_calculate">
                            <span>{!! __('participant.cycle_length') !!}</span>
                        </template>
                        <template x-if="!data || !data.can_calculate">
                            <span>{!! __('participant.wrapped_no_cycle_length_info') !!}</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- 2. Blood Loss -->
            <template x-if="data && data.can_calculate">
                <div class="bg-[#FDF8FE] rounded-2xl px-[10px] py-[6px] shadow-sm border border-primary flex flex-col gap-2 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                            <img src="{{ asset('images/grid_blood_loss.png') }}" class="w-8 h-8 object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-600 leading-tight" x-html="getBloodLossText()"></p>
                        </div>
                        <div class="flex-shrink-0 relative">
                            <img src="{{ asset('images/question.png') }}"
                                 class="w-5 h-5 cursor-help hover:opacity-100"
                                 :class="activeTooltip === 'blood' ? 'opacity-100' : 'opacity-60'"
                                 @mouseenter="if (window.innerWidth >= 1024) activeTooltip = 'blood'"
                                 @mouseleave="if (window.innerWidth >= 1024) activeTooltip = null"
                                 @click.stop="if (window.innerWidth < 1024) toggleTooltip('blood')">

                            <div x-show="activeTooltip === 'blood'"
                                 x-transition.opacity
                                 x-cloak
                                 @click.away="activeTooltip = null"
                                 class="absolute top-full mt-3 md:top-auto md:bottom-full md:mb-3 right-0 w-72 p-3 bg-[#FDF8FE] text-black border-primary border text-xs rounded-lg shadow-xl z-[99]">
                                {!! __('participant.wrapped_blood_loss_info') !!}
                            </div>
                        </div>
                    </div>

                    <template x-if="data.show_pbac_high">
                        <div class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-primary text-[10px] font-bold border border-red-100">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            {{ __('participant.wrapped_pbac_high') }}
                        </div>
                    </template>
                </div>
            </template>

            <!-- 3. Pain -->
            <template x-if="data && data.can_calculate">
                <div class="bg-[#FDF8FE] rounded-2xl px-[10px] py-[6px] shadow-sm border border-primary flex items-center gap-2 hover:shadow-md transition-shadow">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('images/grid_pain.png') }}" class="w-8 h-8 object-contain">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-600 leading-tight" x-html="getPainText()"></p>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('images/question.png') }}"
                             class="w-5 h-5 cursor-help hover:opacity-100"
                             :class="activeTooltip === 'pain' ? 'opacity-100' : 'opacity-60'"
                             @mouseenter="if (window.innerWidth >= 1024) activeTooltip = 'pain'"
                             @mouseleave="if (window.innerWidth >= 1024) activeTooltip = null"
                             @click.stop="if (window.innerWidth < 1024) toggleTooltip('pain')">

                        <div x-show="activeTooltip === 'pain'"
                             x-transition.opacity
                             x-cloak
                             @click.away="activeTooltip = null"
                             class="absolute top-full mt-3 md:top-auto md:bottom-full md:mb-3 right-0 w-72 p-3 bg-[#FDF8FE] text-black border-primary border text-xs rounded-lg shadow-xl z-[99]">
                            {!! __('participant.wrapped_pain_info') !!}
                        </div>
                    </div>
                </div>
            </template>

            <!-- 4. Impact -->
            <template x-if="data && data.can_calculate">
                <div class="bg-[#FDF8FE] rounded-2xl px-[10px] py-[6px] shadow-sm border border-primary flex items-center gap-2 hover:shadow-md transition-shadow">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('images/grid_impact_new.png') }}" class="w-8 h-8 object-contain">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-600 leading-tight" x-html="getImpactText()"></p>
                    </div>
                    <div class="flex-shrink-0 relative">
                        <img src="{{ asset('images/question.png') }}"
                             class="w-5 h-5 cursor-help hover:opacity-100"
                             :class="activeTooltip === 'impact' ? 'opacity-100' : 'opacity-60'"
                             @mouseenter="if (window.innerWidth >= 1024) activeTooltip = 'impact'"
                             @mouseleave="if (window.innerWidth >= 1024) activeTooltip = null"
                             @click.stop="if (window.innerWidth < 1024) toggleTooltip('impact')">

                        <div x-show="activeTooltip === 'impact'"
                             x-transition.opacity
                             x-cloak
                             @click.away="activeTooltip = null"
                             class="absolute top-full mt-3 md:top-auto md:bottom-full md:mb-3 right-0 w-72 p-3 bg-[#FDF8FE] text-black border-primary border text-xs rounded-lg shadow-xl z-[99]">
                            {!! __('participant.wrapped_impact_info') !!}
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>
