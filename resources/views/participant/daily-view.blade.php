@extends('layouts.participant.app')
@section('navbar_title', __('participant.visualise_symptoms_daily_view'))
@section('navbar_subtitle', __('participant.daily_overview_showing_selected_domains'))

@section('content')
    <div class="max-w-7xl mx-auto pl-0 pr-0 py-0" x-data="dailyView()" x-init="init()">
        <!-- Mobile Layout -->
        <div class="md:hidden mb-8">
            <!-- Navigation and Select Date Buttons -->
            <div class="flex items-center gap-1 mb-4">
                <!-- Previous Button -->
                <button @click="prevDay()"
                    class="inline-flex items-center justify-center gap-1 rounded-md bg-red-900 flex-1 px-2 py-2 text-xs font-semibold text-white shadow hover:bg-red-800 cursor-pointer">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                    {{ __('participant.prev') }}
                </button>

                <!-- Next Button -->
                <button @click="nextDay()"
                    class="inline-flex items-center justify-center gap-1 rounded-md bg-red-900 flex-1 px-2 py-2 text-xs font-semibold text-white shadow hover:bg-red-800 cursor-pointer">
                    {{ __('participant.next') }}
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>

                <!-- Select Date Button -->
                <div class="relative flex-1">
                    <button @click="openDate()"
                        class="inline-flex items-center justify-center gap-1 rounded-md bg-red-900 w-full px-2 py-2 text-xs font-semibold text-white shadow cursor-pointer hover:bg-red-800 transition-colors">
                        {{ __('participant.select') }}
                        <i class="fa-solid fa-calendar text-xs"></i>
                    </button>
                    <input x-ref="datePick" type="date" x-model="date" @change="fetchData()" class="sr-only" />
                </div>
            </div>

            <!-- Date Display -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 text-center" x-text="heading"></h2>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden md:grid grid-cols-3 items-center mb-6 gap-4 main-hed">
            <div class="justify-self-start">
                <div class="flex items-center gap-3">
                    <button @click="prevDay()"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-primary w-[120px] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                        <i class="fa-solid fa-angles-left"></i>
                        {{ __('participant.previous') }}
                    </button>
                    <h2 class="text-[22px] font-semibold text-gray-900 min-w-[220px] text-center" x-text="heading"></h2>
                    <button @click="nextDay()"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-primary w-[120px] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                        {{ __('participant.next') }}
                        <i class="fa-solid fa-angles-right"></i>
                    </button>
                </div>
            </div>

            <div></div>

            <div class="md:justify-self-end">
                <button @click="openDate()"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow cursor-pointer hover:bg-primary-800 transition-colors">
                    {{ __('participant.select_date') }}
                    <i class="fa-solid fa-calendar text-sm"></i>
                </button>
                <input x-ref="datePick" type="date" x-model="date" @change="fetchData()" class="sr-only" />
            </div>
        </div>

        <template x-if="loading">
            <div class="text-gray-600">{{ __('participant.loading') }}</div>
        </template>
        <!-- Domain Cards Section -->
        <template x-if="!loading && items.length > 0">
            <div class="mb-8">
                <div class="swiper" x-ref="symSwiper">
                    <div class="swiper-wrapper">
                        <template x-for="item in items" :key="item.key">
                            <div class="swiper-slide md:!w-auto">
                                <div
                                    class="w-full sm:w-[320px] md:w-[350px] h-[200px] rounded-lg bg-white shadow-sm border border-gray-200 p-3 sm:p-4 mx-auto relative hover:shadow-md transition-shadow">
                                    <!-- Header with icon and title -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <img :src="item.iconSrc" :alt="item.label" class="w-6 h-6 object-contain"
                                                x-show="item.iconSrc">
                                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide truncate md:overflow-visible md:whitespace-normal"
                                                x-text="item.label"></h3>
                                        </div>
                                        <!-- Removed color marker -->
                                    </div>

                                    <!-- Visual representation with icons -->
                                    <div class="mb-4 flex-1 flex items-center justify-center h-20">
                                        <!-- Severity/Level icons -->
                                        <div class="flex items-center justify-center flex-wrap gap-0.5 sm:gap-1"
                                            x-show="item.severityIcons && item.severityIcons.length > 0">
                                            <template x-for="(icon, index) in item.severityIcons" :key="index">
                                                <div
                                                    :class="`${icon.active ? 'p-0.5 sm:p-1 bg-blue-100 border-2 border-blue-500 rounded-full' : 'p-0.5 sm:p-1'} flex-shrink-0`">
                                                    <img :src="icon.src" :alt="icon.alt"
                                                        :class="`object-contain ${icon.active ? 'opacity-100' : 'opacity-30'} w-6 h-6 sm:w-7 sm:h-7`">
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Single status icon -->
                                        <div
                                            x-show="item.statusIcon && (!item.severityIcons || item.severityIcons.length === 0)">
                                            <img :src="item.statusIcon.src" :alt="item.statusIcon.alt"
                                                class="w-6 h-6 sm:w-7 sm:h-7 object-contain">
                                        </div>
                                    </div>

                                    <!-- Context and additional info -->
                                    <div class="mb-3">
                                        <div class="text-sm font-medium text-gray-700 text-center truncate md:overflow-visible md:whitespace-normal"
                                            x-html="item.context" x-show="item.context"></div>
                                        <div class="text-xs text-gray-600 text-center mt-1 truncate md:overflow-visible md:whitespace-normal"
                                            x-text="item.additionalInfo" x-show="item.additionalInfo"></div>
                                    </div>

                                    <!-- More button -->
                                    <div class="absolute bottom-3 right-3">
                                        <button @click="openDomainModal(item)"
                                            class="bg-primary text-white text-xs px-3 py-1 rounded-full hover:bg-primary-600 transition-colors">
                                            {{ __('participant.more') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- No Data Message -->
        <template x-if="!loading && items.length === 0">
            <div class="mb-8">
                <p class="text-sm text-gray-500 text-center py-8">{{ __('participant.no_symptom_data_recorded') }}</p>
            </div>
        </template>

        <!-- Videos Section - Always Show -->
        <template x-if="!loading">
            <div>
                <h2 class="text-[22px] font-medium uppercase text-black tracking-tight mb-5"></h2>
                <div class="swiper pl-0 md:pl-0" x-ref="vidSwiper">
                    <div class="swiper-wrapper">
                        <template x-for="(vid,vi) in videos" :key="'vid-' + vi">
                            <div class="swiper-slide md:!w-auto">
                                <a :href="vid.type === 'youtube' ? `https://www.youtube.com/watch?v=${vid.id}` : (vid.src || '#')"
                                    target="_blank" class="block">
                                    <div
                                        class="w-full md:w-[500px] h-[200px] md:h-[240px] rounded-lg bg-rose-50 shadow-sm border border-gray-200 flex items-center justify-center ml-0 md:mx-auto relative overflow-hidden">
                                        <template x-if="vid.type==='youtube'">
                                            <img :src="`https://img.youtube.com/vi/${vid.id}/hqdefault.jpg`"
                                                alt="Video thumbnail" class="w-full h-full object-cover rounded" />
                                        </template>
                                        <template x-if="vid.type==='mp4'">
                                            <div class="w-full h-full bg-black/10 rounded"></div>
                                        </template>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span
                                                class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow">
                                                <i class="fa-solid fa-play text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                <div class="mt-2 text-sm font-medium text-gray-800 w-full md:w-[500px] mx-auto" x-cloak
                                    x-text="vid.title || '{{ __('participant.video') }}'"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Domain Detail Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="closeModal()">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-white bg-opacity-20 backdrop-blur-sm transition-opacity" @click="closeModal()">
            </div>

            <!-- Modal Content -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto md:ml-32"
                    @click.stop>
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <img :src="modalData?.iconSrc" :alt="modalData?.label" class="w-8 h-8 object-contain"
                                x-show="modalData?.iconSrc">
                            <h2 class="text-xl font-semibold text-gray-900" x-text="modalData?.label"></h2>
                        </div>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6" x-html="modalContent">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('participant.partials.daily-view-translations')