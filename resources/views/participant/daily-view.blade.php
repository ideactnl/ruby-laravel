@extends('layouts.participant.app')
@section('navbar_title', 'VISUALISE SYMPTOMS - DAILY VIEW')
@section('navbar_subtitle', 'Daily Overview Showing Selected Domains For The Chosen Date')

@section('content')
<div class="max-w-7xl mx-auto pl-0 pr-0 py-6" x-data="dailyView()" x-init="init()">
    <div class="grid grid-cols-3 items-center mb-6">
        <div class="justify-self-start">
            <h2 class="text-[22px] font-semibold text-gray-900" x-text="heading"></h2>
        </div>

        <div class="justify-self-center">
            <div class="inline-flex items-center gap-3">
                <button @click="prevDay()"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-[#5E0F0F] w-[120px] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                    <i class="fa-solid fa-angles-left"></i>
                    Previous
                </button>
                <button @click="nextDay()"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-[#5E0F0F] w-[120px] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 cursor-pointer">
                    Next
                    <i class="fa-solid fa-angles-right"></i>
                </button>
            </div>
        </div>                

        <div class="justify-self-end">
            <button @click="openDate()"
                    class="inline-flex items-center gap-2 rounded-md bg-[#5E0F0F] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
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
                            <div class="w-[280px] h-[240px] rounded-lg bg-rose-50 shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center mx-auto">
                                <div class="text-center text-[26px] font-medium tracking-wide text-gray-900 uppercase mb-6" x-text="item.label"></div>
                                <div :class="`inline-flex items-center justify-center rounded px-3 py-1 text-sm font-semibold w-[54.58px] h-[49.58px] text-[20px] text-white ${item.badge}`" x-text="item.display"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <template x-if="!loading && videos.length">
        <div>
            <h2 class="text-[22px] font-medium uppercase text-black tracking-tight mb-5">menstrual health</h2>
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
                                            <i class="fa-solid fa-play text-[#5E0F0F]"></i>
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
</div>
@endsection
