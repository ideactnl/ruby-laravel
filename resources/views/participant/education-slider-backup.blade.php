@extends('layouts.participant.app')

@section('navbar_title', __('participant.education'))
@section('navbar_subtitle', __('participant.educational_resources_learning_materials'))

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex gap-2">
                <select class="border border-gray-300 rounded-md px-3 py-1 text-sm text-white bg-primary">
                    <option>{{ __('participant.category') }}</option>
                </select>
                <select class="border border-gray-300 rounded-md px-3 py-3 text-sm text-white bg-primary">
                    <option>{{ __('participant.recommended') }}</option>
                </select>
            </div>
        </div>

        <div class="swiper educationSwiper cursor-grab">
            <div class="swiper-loading py-8 text-gray-500">{{ __('participant.loading') }}</div>
            <div class="swiper-wrapper pb-3" style="display: none;">

                <!-- Slide 1 - YouTube Video -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">{{ __('participant.learn_more_about_subject') }}</div>
                    </div>
                </div>


                <!-- Slide 2 - YouTube Video -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Another educational topic presented here.</div>
                    </div>
                </div>

                <!-- Slide 3 - Flip Card -->
                <div class="swiper-slide px-2">
                    <div class="group [perspective:1000px] select-none touch-manipulation" data-flip-card>
                        <div
                            class="card-new-dec relative h-70 w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">

                            <!-- Front -->
                            <div
                                class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                <div class="p-4 text-sm font-medium">
                                    <div class="text-[23px] font-bold mb-2">MYTH</div>
                                    <h4 class="text-[20px]">{{ __('participant.cant_exercise_during_period') }}</h4>
                                </div>
                                <div class="absolute top-3 right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70"></i>
                                </div>
                            </div>

                            <!-- Back -->
                            <div
                                class="absolute inset-0 rounded-lg bg-primary text-white text-center p-4 [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                <div class="text-sm font-medium">
                                    {{ __('participant.exercise_helps_period_symptoms') }}
                                </div>
                                <div class="absolute top-3 right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 - YouTube Video -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Another educational topic presented here.</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->

    </section>

    <!-- VIDEOS -->
    <section class="mt-12">
        <div class="swiper videoSwiper cursor-grab">
            <div class="swiper-loading py-8 text-gray-500">{{ __('participant.loading') }}</div>
            <div class="swiper-wrapper pb-3" style="display: none;">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">{{ __('participant.learn_more_about_subject') }}</div>
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
                        <div class="p-4 text-sm">{{ __('participant.learn_more_about_subject') }}</div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">{{ __('participant.learn_more_about_subject') }}</div>
                    </div>
                </div>
            </div>
            <!-- Navigation -->

    </section>

@endsection
@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', async () => {
            // Fetch videos from API
            try {
                const response = await fetch('/api/v1/participant/videos/education');
                const data = await response.json();
                const videos = data.videos || [];

                const flipCard = `<div class="swiper-slide px-2">
                        <div class="group [perspective:1000px] select-none touch-manipulation" data-flip-card>
                            <div class="card-new-dec relative h-70 w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                                <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                    <div class="p-4 text-sm font-medium">
                                        <div class="text-[23px] font-bold mb-2">MYTH</div>
                                        <h4 class="text-[20px]">{{ __('participant.cant_exercise_during_period') }}</h4>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <i class="fas fa-sync-alt text-white opacity-70"></i>
                                    </div>
                                </div>
                                <div class="absolute inset-0 rounded-lg bg-primary text-white text-center p-4 [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                    <div class="text-sm font-medium">
                                        {{ __('participant.exercise_helps_period_symptoms') }}
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <i class="fas fa-sync-alt text-white opacity-70"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                const firstSliderVideos = videos.slice(0, 4);
                const secondSliderVideos = videos.slice(4);

                const educationWrapper = document.querySelector('.educationSwiper .swiper-wrapper');
                if (educationWrapper) {
                    educationWrapper.innerHTML = '';

                    firstSliderVideos.forEach((video, index) => {
                        const videoSlide = document.createElement('div');
                        videoSlide.className = 'swiper-slide';
                        videoSlide.innerHTML = `
                            <div class="rounded overflow-hidden shadow-md bg-white">
                                <div class="aspect-video">
                                    <iframe class="w-full h-full"
                                            src="${video.embed_url}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                            loading="lazy"></iframe>
                                </div>
                                <div class="p-4 text-sm">${video.title}</div>
                            </div>
                        `;
                        educationWrapper.appendChild(videoSlide);

                        // Insert the flip card after the second video (index 1)
                        if (index === 1 && flipCard) {
                            const flipCardDiv = document.createElement('div');
                            flipCardDiv.innerHTML = flipCard;
                            educationWrapper.appendChild(flipCardDiv.firstElementChild);
                        }
                    });
                }

                const videoWrapper = document.querySelector('.videoSwiper .swiper-wrapper');
                if (videoWrapper && secondSliderVideos.length > 0) {
                    videoWrapper.innerHTML = '';
                    secondSliderVideos.forEach(video => {
                        const slide = document.createElement('div');
                        slide.className = 'swiper-slide';
                        slide.innerHTML = `
                            <div class="rounded overflow-hidden shadow-md bg-white">
                                <div class="aspect-video">
                                    <iframe class="w-full h-full"
                                            src="${video.embed_url}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen
                                            loading="lazy"></iframe>
                                </div>
                                <div class="p-4 text-sm">${video.title}</div>
                            </div>
                        `;
                        videoWrapper.appendChild(slide);
                    });
                }
            } catch (error) {
                console.error('Error fetching videos:', error);
            }

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

            function triggerHapticFeedback(type = 'light') {
                if ('vibrate' in navigator) {
                    try {
                        const patterns = {
                            light: 10,
                            medium: 25,
                            strong: 40,
                            flip: [30, 20, 10]
                        };
                        navigator.vibrate(patterns[type] || patterns.light);
                    } catch (e) {}
                }
            }

            function initFlipCards() {
                const flipCards = document.querySelectorAll('[data-flip-card]');
                const isMobile = window.innerWidth <= 768;
                let isSwipingSlider = false;
                let swipeStartTime = 0;

                flipCards.forEach(card => {
                    const cardInner = card.querySelector('.card-new-dec');
                    let isFlipped = false;
                    let touchStartX = 0;
                    let touchStartY = 0;
                    let hasMoved = false;

                    const handleMobileFlip = (e) => {
                        if (isSwipingSlider || hasMoved) {
                            return;
                        }

                        e.preventDefault();
                        e.stopPropagation();

                        isFlipped = !isFlipped;
                        triggerHapticFeedback('flip');

                        if (isFlipped) {
                            cardInner.style.transform = 'rotateY(180deg)';
                            card.classList.add('flipped');
                        } else {
                            cardInner.style.transform = 'rotateY(0deg)';
                            card.classList.remove('flipped');
                        }
                    };

                    const handleDesktopHover = () => {
                        cardInner.style.transform = 'rotateY(180deg)';
                        card.classList.add('flipped');
                    };

                    const handleDesktopLeave = () => {
                        cardInner.style.transform = 'rotateY(0deg)';
                        card.classList.remove('flipped');
                    };

                    if (isMobile) {
                        card.addEventListener('touchstart', (e) => {
                            const touch = e.touches[0];
                            touchStartX = touch.clientX;
                            touchStartY = touch.clientY;
                            hasMoved = false;
                            swipeStartTime = Date.now();
                        }, { passive: true });

                        card.addEventListener('touchmove', (e) => {
                            if (!e.touches[0]) return;

                            const touch = e.touches[0];
                            const deltaX = Math.abs(touch.clientX - touchStartX);
                            const deltaY = Math.abs(touch.clientY - touchStartY);

                            if (deltaX > 10 || deltaY > 5) {
                                hasMoved = true;
                                isSwipingSlider = true;
                            }
                        }, { passive: true });

                        card.addEventListener('touchend', (e) => {
                            setTimeout(() => {
                                isSwipingSlider = false;
                                hasMoved = false;
                            }, 100);
                        }, { passive: true });

                        card.addEventListener('click', handleMobileFlip);
                    } else {
                        card.addEventListener('mouseenter', handleDesktopHover);
                        card.addEventListener('mouseleave', handleDesktopLeave);

                        card.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                        });
                    }
                });

                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        const newIsMobile = window.innerWidth <= 768;
                        if (newIsMobile !== isMobile) {
                            flipCards.forEach(card => {
                                const newCard = card.cloneNode(true);
                                card.parentNode.replaceChild(newCard, card);
                            });
                            initFlipCards();
                        }
                    }, 250);
                });
            }

            initFlipCards();
        });
    </script>
@endpush
