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

        <!-- Header (Desktop Only) -->
        <template x-if="data && data.can_calculate">
            <div class="hidden md:block bg-primary/5 rounded-xl p-4 border border-primary/10">
                <h3 class="text-lg font-bold text-gray-900" x-html="getHeaderText()"></h3>
            </div>
        </template>

        <!-- Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- 1. Cycle Length -->
            <div
                class="bg-[#FDF8FE] rounded-2xl p-4 shadow-sm border border-primary flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('images/calender.png') }}" class="size-8 object-contain opacity-80 contrast-75 brightness-110">
                </div>

                <div class="flex-1 min-w-0" x-data="{ expanded: false }" @click="expanded = !expanded">
                    <template x-if="data && data.can_calculate">
                        <p class="text-sm leading-tight transition-all duration-300" 
                           :class="expanded ? 'text-gray-600' : 'line-clamp-1 cursor-pointer text-primary'">
                           <span class="text-gray-600" x-html="getCycleLengthText()"></span>
                        </p>
                    </template>
                    <template x-if="data && !data.can_calculate">
                        <p class="text-sm leading-tight transition-all duration-300"
                           :class="expanded ? 'text-gray-600' : 'line-clamp-1 cursor-pointer text-primary'">
                            <span class="text-gray-600">{{ __('participant.wrapped_no_cycle_length') }}</span>
                        </p>
                    </template>
                </div>

                <div class="flex-shrink-0 relative group">
                    <img src="{{ asset('images/question.png') }}"
                        class="w-5 h-5 cursor-help opacity-60 hover:opacity-100">

                    <div
                        class="absolute bottom-full right-0 mb-3 w-72 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none z-50">
                        <template x-if="data && data.can_calculate">
                            <span>{{ __('participant.cycle_length') }}</span>
                        </template>
                        <template x-if="!data || !data.can_calculate">
                            <span>{{ __('participant.wrapped_no_cycle_length_info') }}</span>
                        </template>
                        <div class="absolute top-full right-2 -mt-1 border-8 border-transparent border-t-gray-900">
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. Blood Loss -->
            <template x-if="data && data.can_calculate">
                <div
                    class="bg-[#FDF8FE] rounded-2xl p-4 shadow-sm border border-primary flex flex-col gap-2 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                            <img src="{{ asset('images/grid_blood_loss.png') }}" class="w-8 h-8 object-contain">
                        </div>
                        <div class="flex-1 min-w-0" x-data="{ expanded: false }" @click="expanded = !expanded">
                            <p class="text-sm leading-tight transition-all duration-300" 
                               :class="expanded ? 'text-gray-600' : 'line-clamp-1 cursor-pointer text-primary'">
                               <span class="text-gray-600" x-html="getBloodLossText()"></span>
                            </p>
                        </div>
                        <div class="flex-shrink-0 relative group">
                            <img src="{{ asset('images/question.png') }}"
                                class="w-5 h-5 cursor-help opacity-60 hover:opacity-100">
                            <div
                                class="absolute bottom-full right-0 mb-3 w-72 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none z-50">
                                {{ __('participant.wrapped_blood_loss_info') }}
                                <div
                                    class="absolute top-full right-2 -mt-1 border-8 border-transparent border-t-gray-900">
                                </div>
                            </div>
                        </div>
                    </div>
                    <template x-if="data.show_pbac_high">
                        <div
                            class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-red-700 text-[10px] font-bold border border-red-100">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            {{ __('participant.wrapped_pbac_high') }}
                        </div>
                    </template>
                </div>
            </template>

            <!-- 3. Pain -->
            <template x-if="data && data.can_calculate">
                <div
                    class="bg-[#FDF8FE] rounded-2xl p-4 shadow-sm border border-primary flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('images/grid_pain.png') }}" class="w-8 h-8 object-contain">
                    </div>
                    <div class="flex-1 min-w-0" x-data="{ expanded: false }" @click="expanded = !expanded">
                        <p class="text-sm leading-tight transition-all duration-300" 
                           :class="expanded ? 'text-gray-600' : 'line-clamp-1 cursor-pointer text-primary'">
                           <span class="text-gray-600" x-html="getPainText()"></span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 relative group">
                        <img src="{{ asset('images/question.png') }}"
                            class="w-5 h-5 cursor-help opacity-60 hover:opacity-100">
                        <div
                            class="absolute bottom-full right-0 mb-3 w-72 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none z-50">
                            {{ __('participant.wrapped_pain_info') }}
                            <div class="absolute top-full right-2 -mt-1 border-8 border-transparent border-t-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- 4. Impact -->
            <template x-if="data && data.can_calculate">
                <div
                    class="bg-[#FDF8FE] rounded-2xl p-4 shadow-sm border border-primary flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center">
                        <img src="{{ asset('images/grid_impact_new.png') }}" class="w-8 h-8 object-contain">
                    </div>
                    <div class="flex-1 min-w-0" x-data="{ expanded: false }" @click="expanded = !expanded">
                        <p class="text-sm leading-tight transition-all duration-300" 
                           :class="expanded ? 'text-gray-600' : 'line-clamp-1 cursor-pointer text-primary'">
                           <span class="text-gray-600" x-html="getImpactText()"></span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 relative group">
                        <img src="{{ asset('images/question.png') }}"
                            class="w-5 h-5 cursor-help opacity-60 hover:opacity-100">
                        <div
                            class="absolute bottom-full right-0 mb-3 w-72 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none z-50">
                            {{ __('participant.wrapped_impact_info') }}
                            <div class="absolute top-full right-2 -mt-1 border-8 border-transparent border-t-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>
