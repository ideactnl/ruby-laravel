// Shared Content Renderer for Education and Self-Management pages
// Handles all content types: video, audio, text, flipcard

window.ContentRenderer = {
    // Main content card creation function
    createContentCard(item) {
        const card = document.createElement('div');
        card.className = `rounded-[10px] overflow-hidden bg-[#FDF8FE] flex flex-col content-card-${item.type || ''}`;

        switch (item.type) {
            case 'video':
                card.innerHTML = this.createVideoCardContent(item);
                break;
            case 'audio':
                card.innerHTML = this.createAudioCardContent(item);
                break;
            case 'text':
                card.innerHTML = this.createTextCardContent(item);
                break;
            case 'flipcard':
                card.innerHTML = this.createFlipCardContent(item);
                break;
            default:
                card.innerHTML = this.createDefaultCardContent(item);
        }

        return card;
    },

    // Video content creation
    createVideoCardContent(video) {
        return `
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
                <div class="text-sm text-gray-600 edu-video-caption"></div>
            </div>
        `;
    },

    // Audio content creation
    createAudioCardContent(audio) {
        return `
            <div class="aspect-[9/16] edu-audio-media flex items-center justify-center bg-gradient-to-br from-purple-400 to-pink-400">
                <div class="text-center text-white p-4">
                    <i class="fas fa-headphones text-4xl mb-2"></i>
                    <h3 class="text-[14px] font-semibold">${audio.title}</h3>
                </div>
            </div>
            <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                <h3 class="text-[14px] font-semibold text-black mb-[6px]">${audio.title}</h3>
                <audio controls class="w-full mb-2">
                    <source src="${audio.audio_url}" type="audio/mpeg">
                    <source src="${audio.audio_url}" type="audio/wav">
                    Browser does not support audio
                </audio>
                <div class="text-sm text-gray-600">${audio.body ? audio.body.replace(/<[^>]*>/g, '').substring(0, 50) + '...' : ''}</div>
            </div>
        `;
    },

    // Text content creation
    createTextCardContent(text) {
        return `
            <div class="aspect-[9/16] edu-text-media flex items-center justify-center bg-gradient-to-br from-blue-400 to-cyan-400 p-4">
                <div class="text-center text-white">
                    <i class="fas fa-file-alt text-4xl mb-2"></i>
                    <h3 class="text-[14px] font-semibold">${text.title}</h3>
                </div>
            </div>
            <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                <h3 class="text-[14px] font-semibold text-black mb-[6px]">${text.title}</h3>
                <div class="text-sm text-gray-700">${text.body ? text.body.replace(/<[^>]*>/g, '').substring(0, 150) + '...' : ''}</div>
            </div>
        `;
    },

    // Flipcard content creation
    createFlipCardContent(flipcard) {
        return `
            <div class="flip-wrapper h-full flex flex-col">
                <div class="aspect-[9/16] w-full">
                    <div class="group [perspective:1000px] select-none touch-manipulation h-full w-full" data-flip-card>
                        <div class="relative h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            <!-- Front -->
                            <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none p-4">
                                <div class="text-sm font-medium">
                                    <div class="text-[23px] font-bold mb-2">MYTH</div>
                                    <h4 class="text-[16px]">${flipcard.front_text}</h4>
                                </div>
                                <div class="absolute top-3 right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70"></i>
                                </div>
                            </div>
                            <!-- Back -->
                            <div class="absolute inset-0 rounded-lg bg-primary text-white text-center p-4 [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                <div class="text-sm font-medium">
                                    <div class="text-[23px] font-bold mb-2">FACT</div>
                                    ${flipcard.back_text}
                                </div>
                                <div class="absolute top-3 right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    },

    // Default content for unknown types
    createDefaultCardContent(item) {
        return `
            <div class="aspect-[9/16] flex items-center justify-center bg-gray-200">
                <div class="text-center text-gray-600 p-4">
                    <i class="fas fa-question-circle text-4xl mb-2"></i>
                    <h3 class="text-[14px] font-semibold">${item.title || 'Unknown Content'}</h3>
                    <p class="text-xs">Type: ${item.type}</p>
                </div>
            </div>
            <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                <h3 class="text-[14px] font-semibold text-black mb-[6px]">${item.title || 'Unknown Content'}</h3>
                <div class="text-sm text-gray-600">Unsupported content type: ${item.type}</div>
            </div>
        `;
    },

    // Default flipcard container
    createFlipCardContainer() {
        const flipCardContainer = document.createElement('div');
        flipCardContainer.className = 'rounded-[10px] overflow-hidden bg-[#FDF8FE] flex flex-col';
        flipCardContainer.innerHTML = `
            <div class="flip-wrapper h-full flex flex-col">
                <div class="aspect-[9/16] w-full">
                    <div class="group [perspective:1000px] select-none touch-manipulation h-full w-full" data-flip-card>
                        <div class="relative h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            <!-- Front -->
                            <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none p-4">
                                <div class="text-sm font-medium">
                                    <div class="text-[23px] font-bold mb-2">MYTH</div>
                                    <h4 class="text-[16px]">You can't exercise when you're on your period</h4>
                                </div>
                                <div class="absolute top-3 right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70"></i>
                                </div>
                            </div>
                            <!-- Back -->
                            <div class="absolute inset-0 rounded-lg bg-primary text-white text-center p-4 [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none">
                                <div class="text-sm font-medium">
                                    <div class="text-[23px] font-bold mb-2">FACT</div>
                                    Exercise can actually help with period symptoms
                                </div>
                                <div class="absolute top-3 right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        return flipCardContainer;
    },

    // Initialize flip card interactions
    initFlipCards() {
        const flipCards = document.querySelectorAll('[data-flip-card]');
        flipCards.forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.closest('[data-flip-card]')) {
                    this.classList.toggle('rotate-y-180');
                }
            });
        });
    },

    // Toggle read more/less functionality
    toggleReadMore(subtitleId, fullText, truncatedText, button) {
        const span = document.getElementById(subtitleId);
        const moreText = "More";
        const lessText = "Less";
        const isExpanded = button.textContent.trim() === lessText;

        if (isExpanded) {
            span.textContent = truncatedText;
            button.textContent = moreText;
        } else {
            span.textContent = fullText;
            button.textContent = lessText;
        }
    },

    // Haptic feedback for mobile
    triggerHapticFeedback(type = 'light') {
        if ('vibrate' in navigator) {
            try {
                navigator.vibrate(type === 'light' ? 10 : 25);
            } catch (e) {
                console.log('Vibration not supported');
            }
        }
    },

    matchCardHeights() {
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
    },

    resetAllFilters() {
        // Reset all filter inputs to default values
        const filtersContainer = document.getElementById('filters-container');
        if (filtersContainer) {
            const inputs = filtersContainer.querySelectorAll('input');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else if (input.type === 'radio') {
                    // Uncheck all radio buttons in the same group
                    const radios = filtersContainer.querySelectorAll(`input[name="${input.name}"]`);
                    radios.forEach(radio => radio.checked = false);
                } else if (input.type === 'range') {
                    const min = input.min || 0;
                    input.value = min;
                    const valueDisplay = document.getElementById(`${input.id}-value`);
                    if (valueDisplay) {
                        valueDisplay.textContent = min;
                    }
                } else {
                    input.value = '';
                }
            });
        }
    },

    getFilterValues() {
        const filters = {};
        const filtersContainer = document.getElementById('filters-container');
        
        if (filtersContainer) {
            const inputs = filtersContainer.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    if (input.checked) {
                        filters[input.name] = true;
                    }
                } else if (input.type === 'radio') {
                    if (input.checked) {
                        filters[input.name] = input.value === 'true';
                    }
                } else if (input.type === 'range') {
                    const value = parseInt(input.value);
                    const min = parseInt(input.min);
                    if (value > min) {
                        filters[input.name] = value;
                    }
                } else if (input.type === 'select-one') {
                    if (input.value && input.value !== "") {
                        filters[input.name] = input.value;
                    }
                } else if (input.value && input.value.trim()) {
                    filters[input.name] = input.value.trim();
                }
            });
        }
        
        return filters;
    },

    setupFilterToggle() {
        const filterToggle = document.getElementById('filter-toggle');
        const filterSection = document.getElementById('filter-section');
        const closeFilters = document.getElementById('close-filters');
        const applyFilters = document.getElementById('apply-filters');
        const resetFilters = document.getElementById('reset-filters');
        
        if (filterToggle && filterSection) {
            filterToggle.addEventListener('click', () => {
                filterSection.classList.toggle('hidden');
            });
        }
        
        if (closeFilters && filterSection) {
            closeFilters.addEventListener('click', () => {
                filterSection.classList.add('hidden');
            });
        }
        
        if (applyFilters) {
            applyFilters.addEventListener('click', () => {
                triggerApiCall();
            });
        }
        
        if (resetFilters) {
            resetFilters.addEventListener('click', () => {
                window.ContentRenderer.resetAllFilters();
                triggerApiCall();
            });
        }
    }
};
