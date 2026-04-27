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
                    class="inline-flex items-center justify-center gap-1 rounded-md bg-primary flex-1 px-4 py-4 text-sm font-semibold text-white shadow hover:bg-red-800 cursor-pointer">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                    {{ __('participant.prev') }}
                </button>

                <!-- Next Button -->
                <button @click="nextDay()"
                    class="inline-flex items-center justify-center gap-1 rounded-md bg-primary flex-1 px-4 py-4 text-sm font-semibold text-white shadow hover:bg-red-800 cursor-pointer">
                    {{ __('participant.next') }}
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>

                <!-- Select Date Button -->
                <div class="relative flex-1">
                    <button @click="openDate()"
                        class="inline-flex items-center justify-center gap-1 rounded-md bg-primary w-full px-6 py-4 text-sm font-semibold text-white shadow cursor-pointer hover:bg-red-800 transition-colors">
                        {{ __('participant.select') }}
                        <i class="fa-solid fa-calendar text-xs"></i>
                    </button>
                    <input x-ref="datePick" type="date" x-model="date" @change="fetchData()" class="sr-only" />
                </div>
            </div>

            <!-- Date Display -->
            <div class="mb-6">
                <h2 class="text-lg font-normal mt-7 text-gray-900 text-center" x-text="heading"></h2>
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
                                    class="w-full sm:w-[320px] md:w-[350px] h-[200px] rounded-lg bg-[#FDF8FE] shadow-sm border border-primary p-3 sm:p-4 mx-auto relative hover:shadow-md transition-shadow">
                                    <!-- Header with icon and title -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <img :src="item.iconSrc" :alt="item.label" class="w-6 h-6 object-contain"
                                                x-show="item.iconSrc">
                                            <h3 class="text-[18px] font-normal capitalize
               md:text-sm md:font-semibold md:uppercase
               text-gray-900 tracking-wide
               truncate md:overflow-visible md:whitespace-normal" x-text="item.label">
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
                                                    :class="`${icon.active ? 'p-0.5 sm:p-1 bg-blue-100 border-2 border-primary rounded-full' : 'p-0.5 sm:p-1'} flex-shrink-0`">
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
                            <div class="swiper-slide">
                                <div
                                    class="dv-video-card w-full rounded-[10px] bg-[#FDF8FE] border border-gray-200 ml-0 md:mx-auto overflow-hidden flex flex-col">
                                    <div class="dv-video-media aspect-[9/16] relative bg-black group"
                                        x-data="{ playing: false }">
                                        <!-- YouTube -->
                                        <template x-if="vid.type==='youtube'">
                                            <div class="w-full h-full">
                                                <!-- Facade -->
                                                <div x-show="!playing" @click="playing = true"
                                                    class="absolute inset-0 cursor-pointer flex items-center justify-center z-10">
                                                    <!-- Thumbnail Background -->
                                                    <img :src="`https://i.ytimg.com/vi/${vid.id}/hqdefault.jpg`"
                                                        class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-300"
                                                        alt="Video Thumbnail">
                                                    
                                                    <!-- Top Overlay: Avatar, Title, Channel -->
                                                    <div class="absolute top-0 left-0 right-0 p-3 md:p-4 bg-gradient-to-b from-black/80 via-black/40 to-transparent flex items-center gap-2 md:gap-3 select-none">
                                                        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F15A24] text-white rounded-full flex items-center justify-center text-xl font-bold shadow-md flex-shrink-0">
                                                            M
                                                        </div>
                                                        <div class="flex flex-col justify-center min-w-0 flex-1">
                                                            <div class="text-white font-bold text-[14px] md:text-[16px] leading-tight truncate drop-shadow-md" x-text="vid.title"></div>
                                                            <div class="text-white/90 text-[11px] md:text-[12px] leading-tight mt-0.5 drop-shadow-md">MGA Studie</div>
                                                        </div>
                                                    </div>

                                                    <!-- Center Overlay: Large YouTube Shorts Logo -->
                                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
                                                        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center drop-shadow-[0_4px_12px_rgba(0,0,0,0.5)] transform group-hover:scale-110 transition-transform duration-300">
                                                            <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="m18.931 9.99-1.441-.601 1.717-.913a4.48 4.48 0 0 0 1.874-6.078 4.506 4.506 0 0 0-6.09-1.874L4.792 5.929a4.504 4.504 0 0 0-2.402 4.193 4.521 4.521 0 0 0 2.666 3.904c.036.012 1.442.6 1.442.6l-1.706.901a4.51 4.51 0 0 0-2.369 3.967A4.528 4.528 0 0 0 6.93 24c.725 0 1.437-.174 2.08-.508l10.21-5.406a4.494 4.494 0 0 0 2.39-4.192 4.525 4.525 0 0 0-2.678-3.904Z" fill="#FF0000"/>
                                                                <path d="M9.597 15.19V8.824l6.007 3.184Z" fill="#FFFFFF"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Active Player -->
                                                <template x-if="playing">
                                                    <iframe class="w-full h-full"
                                                        :src="`https://www.youtube-nocookie.com/embed/${vid.id}?rel=0&modestbranding=1&autoplay=1`"
                                                        frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen></iframe>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- MP4 -->
                                        <template x-if="vid.type==='mp4'">
                                            <div class="w-full h-full relative">
                                                <!-- Facade Overlay -->
                                                <div x-show="!playing" @click="playing = true; $refs.videoPlayer.play()"
                                                    class="absolute inset-0 cursor-pointer flex items-center justify-center z-10 bg-black/10 group-hover:bg-black/20 transition-colors">
                                                    <div
                                                        class="w-12 h-12 bg-black/50 rounded-full flex items-center justify-center backdrop-blur-sm group-hover:scale-110 transition-transform">
                                                        <i class="fa-solid fa-play text-white text-lg ml-1"></i>
                                                    </div>
                                                </div>
                                                <!-- Video Element -->
                                                <video x-ref="videoPlayer" class="w-full h-full object-cover" :src="vid.src"
                                                    controls playsinline @pause="playing = false"
                                                    @play="playing = true"></video>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="dv-video-caption p-3 text-sm text-gray-600 w-full rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]"
                                        x-cloak>
                                        <h3 class="text-[14px] font-semibold text-black mb-[6px]" x-text="vid.title"></h3>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

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