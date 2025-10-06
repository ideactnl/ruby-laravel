@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - DAILY VIEW')
@section('navbar_subtitle', 'Daily Overview Showing Selected Domains For The Chosen Date')

@section('content')
<div class="max-w-7xl mx-auto pl-0 pr-0 py-6" x-data="dailyView()" x-init="init()">
    <div class="grid grid-cols-1 md:grid-cols-3 items-center mb-6 gap-4">
        <div class="justify-self-start">
            <h2 class="text-[22px] font-semibold text-gray-900" x-text="heading"></h2>
        </div>

        <div class="md:justify-self-center">
            <div class="inline-flex items-center gap-3">
                <button @click="prevDay()"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-primary w-[120px] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                    <i class="fa-solid fa-angles-left"></i>
                    Previous
                </button>
                <button @click="nextDay()"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-primary w-[120px] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                    Next
                    <i class="fa-solid fa-angles-right"></i>
                </button>
            </div>
        </div>                

        <div class="md:justify-self-end">
            <button @click="openDate()"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:bg-primary-800 cursor-pointer transition-colors">
                Select Date
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <input x-ref="datePick" type="date" x-model="date" @change="fetchData()" class="sr-only" />
        </div>
    </div>

    <template x-if="loading">
        <div class="text-gray-600">Loading...</div>
    </template>
    <template x-if="!loading && !data">
        <p class="text-sm text-gray-500 mb-3">No data for this date.</p>
    </template>

    <template x-if="!loading && data">
        <div class="mb-8">
            <div class="swiper" x-ref="symSwiper">
                <div class="swiper-wrapper">
                    <template x-for="item in items" :key="item.key">
                        <div class="swiper-slide !w-auto">
                            <div class="w-[280px] h-[200px] rounded-lg bg-white shadow-sm border border-gray-200 p-4 mx-auto relative hover:shadow-md transition-shadow">
                                <!-- Header with icon and title -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <img :src="item.iconSrc" :alt="item.label" class="w-8 h-8 object-contain" x-show="item.iconSrc">
                                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide" x-text="item.label"></h3>
                                    </div>
                                    <div :class="`w-3 h-3 rounded-full ${item.statusColor}`" :title="item.statusText"></div>
                                </div>
                                
                                <!-- Visual representation with icons -->
                                <div class="mb-4 flex-1 flex items-center justify-center">
                                    <!-- Severity/Level icons -->
                                    <div class="flex items-center gap-1" x-show="item.severityIcons && item.severityIcons.length > 0">
                                        <template x-for="(icon, index) in item.severityIcons" :key="index">
                                            <img :src="icon.src" :alt="icon.alt" :class="`w-6 h-6 object-contain ${icon.active ? 'opacity-100' : 'opacity-30'}`">
                                        </template>
                                    </div>
                                    
                                    <!-- Single status icon -->
                                    <div x-show="item.statusIcon && (!item.severityIcons || item.severityIcons.length === 0)">
                                        <img :src="item.statusIcon.src" :alt="item.statusIcon.alt" class="w-12 h-12 object-contain">
                                    </div>
                                </div>
                                
                                <!-- Context and additional info -->
                                <div class="mb-3">
                                    <div class="text-sm font-medium text-gray-700 text-center" x-text="item.context" x-show="item.context"></div>
                                    <div class="text-xs text-gray-600 text-center mt-1" x-text="item.additionalInfo" x-show="item.additionalInfo"></div>
                                </div>
                                
                                <!-- More button -->
                                <div class="absolute bottom-3 right-3">
                                    <button @click="openDomainModal(item)" 
                                            class="bg-primary text-white text-xs px-3 py-1 rounded-full hover:bg-primary-600 transition-colors">
                                        More
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <template x-if="!loading && videos.length">
        <div>
            <h2 class="text-[22px] font-medium uppercase text-black tracking-tight mb-5"></h2>
            <div class="swiper" x-ref="vidSwiper">
                <div class="swiper-wrapper">
                    <template x-for="(vid,vi) in videos" :key="'vid-'+vi">
                        <div class="swiper-slide !w-auto">
                            <a :href="vid.type==='youtube' ? `https://www.youtube.com/watch?v=${vid.id}` : (vid.src || '#')" target="_blank" class="block">
                                <div class="w-[500px] h-[240px] rounded-lg bg-rose-50 shadow-sm border border-gray-200 flex items-center justify-center mx-auto relative overflow-hidden">
                                    <template x-if="vid.type==='youtube'">
                                        <img :src="`https://img.youtube.com/vi/${vid.id}/hqdefault.jpg`" alt="Video thumbnail" class="w-full h-full object-cover rounded"/>
                                    </template>
                                    <template x-if="vid.type==='mp4'">
                                        <div class="w-full h-full bg-black/10 rounded"></div>
                                    </template>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/90 shadow">
                                            <i class="fa-solid fa-play text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <div class="mt-2 text-sm font-medium text-gray-800 w-[500px] mx-auto" x-cloak x-text="vid.title || 'Video'"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- Domain Detail Modal -->
    <div x-show="showModal" x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         @keydown.escape.window="closeModal()">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-white bg-opacity-20 backdrop-blur-sm transition-opacity" 
             @click="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto md:ml-32"
                 @click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <img :src="modalData?.iconSrc" :alt="modalData?.label" class="w-8 h-8 object-contain" x-show="modalData?.iconSrc">
                        <h2 class="text-xl font-semibold text-gray-900" x-text="modalData?.label"></h2>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
