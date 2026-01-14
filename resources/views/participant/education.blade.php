@extends('layouts.participant.app')

@section('navbar_title', __('participant.education'))
@section('navbar_subtitle', __('participant.educational_resources_learning_materials'))

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex gap-2">
                <select class="border border-gray-300 rounded-md  px-3 py-1 text-sm text-white bg-primary">
                    <option>{{ __('participant.category') }}</option>
                </select>
                <select class="border border-gray-300 rounded-md  px-3 py-3 text-sm text-white bg-primary">
                    <option>{{ __('participant.recommended') }}</option>
                </select>
            </div>
        </div>

        <div id="education-loading" class="py-8 text-gray-500 text-center">{{ __('participant.loading') }}</div>

        <div id="education-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4"
            style="display: none;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch('/api/v1/participant/videos/education');
                const data = await response.json();
                const videos = data.videos || [];

                const flipCard = `
                        <div class="group [perspective:1000px] select-none touch-manipulation h-full w-full" data-flip-card>
                            <div class="relative h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                                <!-- Front -->
                                <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                    <div class="p-4 text-sm font-medium">
                                        <div class="text-[23px] font-bold mb-2">MYTH</div>
                                        <h4 class="text-[16px]">{{ __('participant.cant_exercise_during_period') }}</h4>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <i class="fas fa-sync-alt text-white opacity-70"></i>
                                    </div>
                                </div>
                                <!-- Back -->
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
                    `;

                const educationGrid = document.getElementById('education-grid');
                const loadingElement = document.getElementById('education-loading');

                if (educationGrid) {
                    educationGrid.innerHTML = '';

                    videos.forEach((video, index) => {
                        const videoCard = document.createElement('div');
                        videoCard.className = 'rounded-[10px] overflow-hidden  bg-white flex flex-col';

                        const maxLength = 25;
                        let subtitleContent = '';
                        if (video.subtitle) {
                            const isLong = video.subtitle.length > maxLength;
                            const truncated = isLong ? video.subtitle.substring(0, maxLength) + '...' : video.subtitle;
                            const subtitleId = `subtitle-${video.id}`;

                            subtitleContent = `
                                    <div class="text-sm text-gray-600 edu-video-caption">
                                        <span>
                                            <a href="${video.watch_url}" 
                                                target="_blank" 
                                                id="${subtitleId}"
                                                class="text-sm text-primary hover:underline block">
                                                ${truncated}
                                            </a>
                                        </span>
                                        ${isLong ? `
                                            <button onclick="toggleReadMore('${subtitleId}', '${video.subtitle.replace(/'/g, "\\'")}', '${truncated.replace(/'/g, "\\'")}', this)"
                                                    class="text-primary ml-1 text-xs font-medium">
                                                More
                                            </button>
                                        ` : ''}
                                    </div>
                                `;
                        }

                        videoCard.innerHTML = `
                            <div class="aspect-[9/16] edu-video-media">
                                <iframe class="w-full h-full"
                                        src="${video.embed_url}"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy"></iframe>
                            </div>

                             <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                                <h3 class="text-[14px] font-semibold text-black mb-[6px]">${video.title}</h3>
                                ${subtitleContent || '<div class="text-sm text-gray-600 edu-video-caption"></div>'}
                            </div>
                        `;

                        educationGrid.appendChild(videoCard);

                        if (index === 1) {
                            const flipCardContainer = document.createElement('div');
                            flipCardContainer.className = 'rounded-10 overflow-hidden  bg-white flex flex-col';
                            flipCardContainer.innerHTML = `
                                <div class="flip-wrapper h-full flex flex-col">
                                    <div class="aspect-[9/16] w-full">
                                        ${flipCard}
                                    </div>
                                    <div class="p-4 flex-1 flex items-center item  rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary">
                                    </div>
                                </div>
                            `;

                            educationGrid.appendChild(flipCardContainer);
                        }
                    });

                    loadingElement.style.display = 'none';
                    educationGrid.style.display = 'grid';

                    initFlipCards();
                }
            } catch (error) {
                console.error('Error fetching videos:', error);
                const loadingElement = document.getElementById('education-loading');
                if (loadingElement) {
                    loadingElement.textContent = 'Error loading content';
                }
            }
        });

        function toggleReadMore(subtitleId, fullText, truncatedText, button) {
            const span = document.getElementById(subtitleId);
            const isExpanded = button.textContent === 'Less';

            if (isExpanded) {
                span.textContent = truncatedText;
                button.textContent = 'More';
            } else {
                span.textContent = fullText;
                button.textContent = 'Less';
            }
        }

        function matchCardHeights() {
            const cards = document.querySelectorAll('#education-grid > div');
            let maxHeight = 0;

            // Find tallest card
            cards.forEach(card => {
                const height = card.offsetHeight;
                if (height > maxHeight) maxHeight = height;
            });

            // Apply max height to all cards
            cards.forEach(card => {
                card.style.height = maxHeight + 'px';
            });
        }

        // Removed grid-wide equalization so More/Less only affects the clicked caption


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
                } catch (e) { }
            }
        }

        function initFlipCards() {
            const flipCards = document.querySelectorAll('[data-flip-card]');
            const isMobile = window.innerWidth <= 768;

            flipCards.forEach(card => {
                const cardInner = card.querySelector('div[class*="relative h-full w-full transition-transform"]');
                let isFlipped = false;
                let touchStartX = 0;
                let touchStartY = 0;
                let hasMoved = false;

                const handleMobileFlip = (e) => {
                    if (hasMoved) return;

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
                    }, { passive: true });

                    card.addEventListener('touchmove', (e) => {
                        if (!e.touches[0]) return;

                        const touch = e.touches[0];
                        const deltaX = Math.abs(touch.clientX - touchStartX);
                        const deltaY = Math.abs(touch.clientY - touchStartY);

                        if (deltaX > 10 || deltaY > 5) {
                            hasMoved = true;
                        }
                    }, { passive: true });

                    card.addEventListener('touchend', (e) => {
                        setTimeout(() => {
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
        }
        matchCardHeights();
    </script>
@endpush
