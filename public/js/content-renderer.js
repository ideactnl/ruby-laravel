// Shared Content Renderer for Education and Self-Management pages
// Handles all content types: video, audio, text, flipcard

window.ContentRenderer = {
    // Main content card creation function
    createContentCard(item) {
        // Skip if item is null or video type but no video_url
        if(!item || (item?.type === 'video' && !item?.video_url)) {
            return '';
        }

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

    // Interleave content: Video, Flipcard, Video, Flipcard...
    interleaveContent(content) {
        if (!content || !Array.isArray(content)) return [];
        
        const videos = content.filter(item => item.type === 'video');
        const flipcards = content.filter(item => item.type === 'flipcard');
        const others = content.filter(item => item.type !== 'video' && item.type !== 'flipcard');
        
        const interleaved = [];
        const maxLength = Math.max(videos.length, flipcards.length);
        
        for (let i = 0; i < maxLength; i++) {
            if (videos[i]) interleaved.push(videos[i]);
            if (flipcards[i]) interleaved.push(flipcards[i]);
        }
        
        // Append any remaining items (audio, text, etc) at the end
        return interleaved.concat(others);
    },

    // YouTube URL conversion
    convertYouTubeUrl(url) {
        try {
            const videoIdMatch = url.match(/(?:shorts\/|v=)([^?&]+)/);
            if (!videoIdMatch) return url;

            const videoId = videoIdMatch[1];

            return `https://www.youtube-nocookie.com/embed/${videoId}`;
        } catch (error) {
            return url;
        }
    },

    // Video content creation
    createVideoCardContent(video) {
        let videoEmbedHtml;
        const videoIdMatch = video?.video_url?.match(/(?:shorts\/|v=)([^?&]+)/);
        
        if (videoIdMatch && videoIdMatch[1]) {
            const videoId = videoIdMatch[1];
            const thumbnailUrl = `https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`;
            const embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
            
            // Render thumbnail with the YouTube Shorts facade overlay
            videoEmbedHtml = `
                <div class="w-full h-full relative group cursor-pointer bg-black" 
                     onclick="this.innerHTML='<iframe class=\\'w-full h-full\\' src=\\'${embedUrl}\\' frameborder=\\'0\\' allow=\\'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\\' allowfullscreen></iframe>'">
                    
                    <!-- Thumbnail Background -->
                    <img src="${thumbnailUrl}" alt="${video?.title || 'Video'}" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-300">
                    
                    <!-- Top Overlay: Avatar, Title, Channel -->
                    <div class="absolute top-0 left-0 right-0 p-3 md:p-4 bg-gradient-to-b from-black/80 via-black/40 to-transparent flex items-center gap-2 md:gap-3 select-none">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F15A24] text-white rounded-full flex items-center justify-center text-xl font-bold shadow-md flex-shrink-0">
                            M
                        </div>
                        <div class="flex flex-col justify-center min-w-0 flex-1">
                            <div class="text-white font-bold text-[14px] md:text-[16px] leading-tight truncate drop-shadow-md">${video?.title || 'Video'}</div>
                            <div class="text-white/90 text-[11px] md:text-[12px] leading-tight mt-0.5 drop-shadow-md">MGA Studie</div>
                        </div>
                    </div>

                    <!-- Center Overlay: Large YouTube Shorts Logo -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
                        <div class="w-14 h-14 md:w-16 md:h-16 flex items-center justify-center drop-shadow-[0_4px_12px_rgba(0,0,0,0.5)] transform group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Red Shorts 'S' -->
                                <path d="m18.931 9.99-1.441-.601 1.717-.913a4.48 4.48 0 0 0 1.874-6.078 4.506 4.506 0 0 0-6.09-1.874L4.792 5.929a4.504 4.504 0 0 0-2.402 4.193 4.521 4.521 0 0 0 2.666 3.904c.036.012 1.442.6 1.442.6l-1.706.901a4.51 4.51 0 0 0-2.369 3.967A4.528 4.528 0 0 0 6.93 24c.725 0 1.437-.174 2.08-.508l10.21-5.406a4.494 4.494 0 0 0 2.39-4.192 4.525 4.525 0 0 0-2.678-3.904Z" fill="#FF0000"/>
                                <!-- White Inner Play Triangle -->
                                <path d="M9.597 15.19V8.824l6.007 3.184Z" fill="#FFFFFF"/>
                            </svg>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Fallback to standard iframe for non-YouTube or unrecognized URLs
            videoEmbedHtml = `
                <iframe class="w-full h-full"
                        src="${this.convertYouTubeUrl(video?.video_url)}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        loading="lazy"></iframe>
            `;
        }

        return `
            <div class="aspect-[9/16] edu-video-media overflow-hidden rounded-t-[10px]">
                ${videoEmbedHtml}
            </div>
            <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                <h3 class="text-[14px] font-semibold text-black mb-[6px]">${video?.title}</h3>
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

    // Format back text for flipcards to handle bullets and spacing
    formatBackContent(text) {
        if (!text) return '';
        
        // Ensure any ul tags coming from API or markdown get bullet point styling
        let processedText = text;
        
        if (/<[a-z][\s\S]*>/i.test(processedText)) {
            // It's already HTML from the CMS API. Inject bullet styling manually.
            processedText = processedText.replace(/<ul\b[^>]*>/gi, '<ul class="flip-card-list">');
            processedText = processedText.replace(/<p\b[^>]*>/gi, '<div>');
            processedText = processedText.replace(/<\/p>/gi, '</div>');
            return `<div class="back-content-container">${processedText}</div>`;
        }
        
        const lines = processedText.split('\n');
        let html = '<div class="back-content-container">';
        let inList = false;

        lines.forEach(line => {
            const trimmed = line.trim();
            if (trimmed.startsWith('•') || trimmed.startsWith('- ')) {
                if (!inList) {
                    html += '<ul class="flip-card-list">';
                    inList = true;
                }
                const content = trimmed.substring(1).trim();
                html += `<li>${content}</li>`;
            } else if (trimmed === '') {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                html += '<div class="h-1"></div>';
            } else {
                if (inList) {
                    html += '</ul>';
                    inList = false;
                }
                html += `<div>${trimmed}</div>`;
            }
        });

        if (inList) {
            html += '</ul>';
        }

        html += '</div>';
        return html;
    },

    // Flipcard content creation
    createFlipCardContent(flipcard) {
        const rawBackText = flipcard.back_text || '';
        const formattedBackText = this.formatBackContent(rawBackText);
        
        const hasList = formattedBackText.includes('<ul') || formattedBackText.includes('<li');
        
        const backBgColor = hasList ? 'bg-[#8c0d38]' : 'bg-primary';
        
        return `
            <div class="flip-wrapper h-full flex flex-col">
                <div class="aspect-[9/16] w-full">
                    <div class="group [perspective:1000px] select-none touch-manipulation h-full w-full" data-flip-card>
                        <div class="relative h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            <!-- Front -->
                            <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-t-[10px] [backface-visibility:hidden] flex flex-col justify-center items-center cursor-pointer select-none p-3 md:p-4 overflow-hidden">
                                <div class="text-sm font-medium w-full">
                                    <div class="text-[14px] md:text-[18px] font-bold mb-1 md:mb-2 leading-tight px-1 break-words hyphens-auto" lang="nl">${flipcard.front_text || flipcard.title}</div>
                                </div>
                                <div class="absolute top-2 right-2 md:top-3 md:right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70 text-xs md:text-sm"></i>
                                </div>
                            </div>
                            <!-- Back -->
                            <div class="absolute inset-0 rounded-t-[10px] ${backBgColor} text-white text-left p-3 md:p-4 md:px-6 [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col items-start cursor-pointer select-none overflow-y-auto shadow-none">
                                <div class="font-medium leading-snug w-full my-auto text-[14px]">
                                    ${formattedBackText}
                                </div>
                                <div class="absolute top-2 right-2 md:top-3 md:right-3">
                                    <i class="fas fa-sync-alt text-white opacity-70 text-xs md:text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-2 md:p-4 flex-1 flex flex-col items-start rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                    <h3 class="text-[14px] font-semibold text-black mb-[6px] opacity-0 select-none">.</h3>
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
                    // this.classList.toggle('rotate-y-180');
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
            const inputs = filtersContainer.querySelectorAll('input,select');
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
