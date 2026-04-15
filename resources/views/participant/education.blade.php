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
                const [educationResponse, selfManagementResponse] = await Promise.all([
                    fetch('/api/v1/participant/videos/education'),
                    fetch('/api/v1/participant/videos/self-management')
                ]);
                const educationData = await educationResponse.json();
                const selfManagementData = await selfManagementResponse.json();
                const videos = [...(educationData.videos || []), ...(selfManagementData.videos || [])];

                // Flipcard definitions - placed in order: one video, one card
                const flipCards = [
                    {
                        id: 'myth_exercise',
                        frontTitle: "MYTH<br><span class='text-[16px] font-normal'>{{ __('participant.cant_exercise_during_period') }}</span>",
                        backContent: `{{ __('participant.exercise_helps_period_symptoms') }}`
                    },
                    {
                        id: 'alarmsignalen',
                        frontTitle: "{{ __('participant.flipcard_alarmsignalen_title') }}",
                        backContent: `{{ __('participant.flipcard_alarmsignalen_content') }}`
                    },
                    {
                        id: 'menstruatiepijn_1',
                        frontTitle: "{{ __('participant.flipcard_menstruatiepijn_title') }}",
                        backContent: `{{ __('participant.flipcard_menstruatiepijn_content') }}`
                    },
                    {
                        id: 'bloedverlies_1',
                        frontTitle: "{{ __('participant.flipcard_bloedverlies_title') }}",
                        backContent: `{{ __('participant.flipcard_bloedverlies_content') }}`
                    },
                    {
                        id: 'bloedverlies_2',
                        frontTitle: "{{ __('participant.flipcard_bloedverlies2_title') }}",
                        backContent: `{{ __('participant.flipcard_bloedverlies2_content') }}`
                    },
                    {
                        id: 'menstruatiepijn_2',
                        frontTitle: "{{ __('participant.flipcard_menstruatiepijn2_title') }}",
                        backContent: `{{ __('participant.flipcard_menstruatiepijn2_content') }}`
                    },
                    {
                        id: 'tampon',
                        frontTitle: "{{ __('participant.flipcard_tampon_title') }}",
                        backContent: `{{ __('participant.flipcard_tampon_content') }}`
                    },
                    {
                        id: 'maandverband',
                        frontTitle: "{{ __('participant.flipcard_maandverband_title') }}",
                        backContent: `{{ __('participant.flipcard_maandverband_content') }}`
                    },
                    {
                        id: 'menstruatieondergoed',
                        frontTitle: "{{ __('participant.flipcard_menstruatieondergoed_title') }}",
                        backContent: `{{ __('participant.flipcard_menstruatieondergoed_content') }}`
                    },
                    {
                        id: 'menstruatiecup',
                        frontTitle: "{{ __('participant.flipcard_menstruatiecup_title') }}",
                        backContent: `{{ __('participant.flipcard_menstruatiecup_content') }}`
                    }
                ];

                // Track which flipcard index to insert next
                let flipCardIndex = 0;

                function createFlipCardHTML(flipCard) {
                    return `
                            <div class="group [perspective:1000px] select-none touch-manipulation h-full w-full" data-flip-card data-flip-id="${flipCard.id}">
                                <div class="relative h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                                    <!-- Front -->
                                    <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-t-lg [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none p-3 md:p-4 overflow-hidden">
                                        <div class="text-sm font-medium w-full">
                                            <div class="text-[14px] md:text-[18px] font-bold mb-1 md:mb-2 leading-tight px-1 break-words hyphens-auto" lang="nl">${flipCard.frontTitle}</div>
                                        </div>
                                        <div class="absolute top-2 right-2 md:top-3 md:right-3">
                                            <i class="fas fa-sync-alt text-white opacity-70 text-xs md:text-sm"></i>
                                        </div>
                                    </div>
                                    <!-- Back -->
                                    <div class="absolute inset-0 rounded-t-lg bg-primary text-white text-center p-3 md:p-4 [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none overflow-y-auto shadow-none">
                                        <div class="text-xs md:text-sm font-medium whitespace-pre-line leading-snug">
                                            ${flipCard.backContent}
                                        </div>
                                        <div class="absolute top-2 right-2 md:top-3 md:right-3">
                                            <i class="fas fa-sync-alt text-white opacity-70 text-xs md:text-sm"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                }

                function createFlipCardContainer(flipCardHTML) {
                    const container = document.createElement('div');
                    container.className = 'rounded-[10px] overflow-hidden bg-[#FDF8FE] flex flex-col';
                    container.innerHTML = `
                            <div class="aspect-[9/16] w-full">
                                ${flipCardHTML}
                            </div>
                            <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                                <h3 class="text-[14px] font-semibold text-black mb-[6px] opacity-0 select-none">.</h3>
                            </div>
                        `;
                    return container;
                }

                const educationGrid = document.getElementById('education-grid');
                const loadingElement = document.getElementById('education-loading');

                if (educationGrid) {
                    educationGrid.innerHTML = '';

                    videos.forEach((video, index) => {
                        // Add video card
                        const videoCard = document.createElement('div');
                        videoCard.className = 'rounded-[10px] overflow-hidden  bg-[#FDF8FE] flex flex-col';

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
                                    </div>
                                `;

                        educationGrid.appendChild(videoCard);

                        // Insert one flipcard after each video until all cards are used
                        if (flipCardIndex < flipCards.length) {
                            const flipCard = flipCards[flipCardIndex];
                            const flipCardContainer = createFlipCardContainer(createFlipCardHTML(flipCard));
                            educationGrid.appendChild(flipCardContainer);
                            flipCardIndex++;
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