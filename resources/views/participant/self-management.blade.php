@extends('layouts.participant.app')

@section('navbar_title', 'SELF MANAGEMENT')
@section('navbar_subtitle', 'Tools and resources for managing your health')

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold">EDUCATION VIDEOS</h2>
            <div class="flex gap-2">
                <select class="border border-gray-300 rounded px-3 py-1 text-sm text-white bg-[#7B1C1C]">
                    <option>Category</option>
                </select>
                <select class="border border-gray-300 rounded px-3 py-1 text-sm text-white bg-[#7B1C1C]">
                    <option>Recommended</option>
                </select>
            </div>
        </div>

        <div class="swiper educationSwiper cursor-grab">
            <div class="swiper-loading py-8 text-gray-500">Loading...</div>
            <div class="swiper-wrapper pb-3" style="display: none;">

                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Another educational topic presented here.</div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>
            </div>
            <!-- Navigation -->

    </section>

    <!-- VIDEOS -->
    <section class="mt-12">
        <h2 class="text-2xl font-bold mb-4">VIDEOS</h2>
        <div class="swiper videoSwiper cursor-grab">
            <div class="swiper-loading py-8 text-gray-500">Loading...</div>
            <div class="swiper-wrapper pb-3" style="display: none;">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Another educational topic presented here.</div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>
            </div>
            <!-- Navigation -->

    </section>

@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const educationSwiper = new Swiper('.educationSwiper', {
                slidesPerView: 1.3,
                spaceBetween: 16,
                breakpoints: {
                    1024: {
                        slidesPerView: 2.9,
                    },
                },
                on: {
                    init: function() {
                        const container = document.querySelector('.educationSwiper');
                        const loading = container.querySelector('.swiper-loading');
                        const wrapper = container.querySelector('.swiper-wrapper');
                        if (loading) loading.style.display = 'none';
                        if (wrapper) wrapper.style.display = 'flex';
                    }
                }
            });

            const videoSwiper = new Swiper('.videoSwiper', {
                slidesPerView: 1.3,
                spaceBetween: 16,
                breakpoints: {
                    1024: {
                        slidesPerView: 2.9,
                    },
                },
                on: {
                    init: function() {
                        const container = document.querySelector('.videoSwiper');
                        const loading = container.querySelector('.swiper-loading');
                        const wrapper = container.querySelector('.swiper-wrapper');
                        if (loading) loading.style.display = 'none';
                        if (wrapper) wrapper.style.display = 'flex';
                    }
                }
            });
        });
    </script>
@endpush
