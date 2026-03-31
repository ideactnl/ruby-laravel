@extends('layouts.participant.app')

@section('navbar_title', __('participant.education'))
@section('navbar_subtitle', __('participant.educational_resources_learning_materials'))

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex gap-2">
                <select id="category-filter" class="border border-gray-300 rounded-md px-3 py-1 text-sm text-white bg-primary">
                    <option value="">{{ __('participant.category') }}</option>
                </select>
                <select id="recommended-filter" class="border border-gray-300 rounded-md px-3 py-3 text-sm text-white bg-primary">
                    <option value="">{{ __('participant.recommended') }}</option>
                </select>

                <button id="filter-toggle" class="inline-flex items-center border border-gray-300 rounded-md px-3 py-1 text-sm text-white bg-primary hover:bg-primary/90 transition-colors hidden">
                    <i class="fas fa-filter mr-2"></i>{{ __('participant.filters') }}
                </button>
            </div>
        </div>

        <!-- Collapsible Filter Section -->
        <div id="filter-section" class="hidden mb-6 p-6 bg-[#FDF8FE] border border-primary rounded-md">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-2xl  font-semibold text-gray-800">{{ __('participant.filter_by') }}</h3>
                <button id="close-filters" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="filters-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Filters will be populated here -->
            </div>
            <div class="mt-3 flex gap-2">
                <button id="apply-filters" class="px-3 py-3 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors text-sm">
                    {{ __('participant.apply_filters') }}
                </button>
                <button id="reset-filters" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-sm">
                    {{ __('participant.reset_filters') }}
                </button>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="fixed inset-0 bg-[#00000085] bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="rounded-lg p-6 flex flex-col items-center">
                <div class="relative mb-4">
                    <div class="w-12 h-12 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                    <div class="absolute top-0 left-0 w-12 h-12 border-4 border-transparent border-t-primary rounded-full animate-spin" style="animation-delay: 0.15s;"></div>
                </div>
                <p class="text-gray-700 text-sm">{{ __('participant.loading') }}</p>
            </div>
        </div>

        <div id="education-no-content" class="py-8 text-gray-500 text-center" style="display: none;">
            <div class="text-center">
                <i class="fas fa-inbox text-4xl mb-4 text-gray-400"></i>
                <p class="text-lg font-medium">{{ __('participant.no_content_available') }}</p>
                <p class="text-sm mt-2">{{ __('participant.try_different_filters_or_check_back_later') }}</p>
            </div>
        </div>

        <div id="education-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4"
            style="display: none;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/content-renderer.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', async () => {
            await triggerApiCall();
            await loadCategories();
            window.ContentRenderer.setupFilterToggle();
        });

        async function loadCategories() {
            try {
                const response = await fetch("{{ route('participant.categories.filter.api') }}");
                const data = await response.json();
                
                const filtersContainer = document.getElementById('filters-container');
                const filterToggle = document.getElementById('filter-toggle');
                
                if (filtersContainer && data.categories) {
                    filtersContainer.innerHTML = '';
                    
                    // Create filter inputs based on category types
                    data.categories.forEach(category => {
                        // Skip location filter as it's redundant with page context
                        if (category.slug === 'location') {
                            return;
                        }
                        const filterDiv = createFilterInput(category);
                        filtersContainer.appendChild(filterDiv);
                    });
                    
                    // Show filter button only if there are filters to display
                    if (filtersContainer.children.length > 0) {
                        if (filterToggle) {
                            filterToggle.classList.remove('hidden');
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        async function triggerApiCall() {
            const loadingOverlay = document.getElementById('loading-overlay');
            
            try {
                // Show overlay loader
                if (loadingOverlay) {
                    loadingOverlay.classList.remove('hidden');
                }
                
                const response = await fetch("{{ route('participant.videos.fetch.api') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(window.ContentRenderer.getFilterValues())
                });
                const data = await response.json();

                const content = data?.content || [];
                const educationGrid = document.getElementById('education-grid');
                const noContentElement = document.getElementById('education-no-content');

                if (educationGrid) {
                    educationGrid.innerHTML = '';

                    if (content.length === 0) {
                        if (noContentElement) {
                            noContentElement.style.display = 'block';
                        }
                        if (educationGrid) {
                            educationGrid.style.display = 'none';
                        }
                    } else {
                        if (noContentElement) {
                            noContentElement.style.display = 'none';
                        }
                        if (educationGrid) {
                            educationGrid.style.display = 'grid';
                        }

                        content.forEach(item => {
                            const contentCard = window.ContentRenderer.createContentCard(item);
                            if (contentCard) {
                                educationGrid.appendChild(contentCard);
                            }
                        });

                        window.ContentRenderer.equalizeCardHeights();
                        window.ContentRenderer.initFlipCards();
                    }
                }
            } catch (error) {
                console.error('Error fetching content:', error);
            } finally {
                // Hide overlay loader
                if (loadingOverlay) {
                    loadingOverlay.classList.add('hidden');
                }
            }
        }

        function createFilterInput(category) {
            const filterDiv = document.createElement('div');
            filterDiv.className = 'bg-[#FDF8FE] shadow-sm border border-primary p-2 rounded-md';
            
            // Capitalize first letter of category name
            const displayName = category.name.charAt(0).toUpperCase() + category.name.slice(1);
            
            let inputHtml = '';
            
            switch (category.value_type) {
                case 'numeric':
                    inputHtml = `
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            ${displayName}
                            ${category.metadata?.unit ? `<span class="text-xs text-gray-400">(${category.metadata.unit})</span>` : ''}
                        </label>
                        <div class="flex items-center gap-1">
                            <input type="range" 
                                   id="filter-${category.slug}" 
                                   name="${category.slug}"
                                   min="${category.metadata?.min || 0}"
                                   max="${category.metadata?.max || 10}"
                                   value="${category.metadata?.min || 0}"
                                   class="flex-1 h-1 accent-primary">
                            <span id="filter-${category.slug}-value" class="text-xs text-gray-600 w-4">${category.metadata?.min || 0}</span>
                        </div>
                    `;
                    break;
                    
                case 'boolean':
                    inputHtml = `
                        <label class="block text-xs font-medium text-gray-700 mb-1 cursor-pointer hover:text-gray-800 transition-colors">
                            ${displayName}
                        </label>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-0.5 rounded">
                                <input type="radio" 
                                       id="filter-${category.slug}-yes" 
                                       name="${category.slug}"
                                       value="true"
                                       class="mr-1 text-xs">
                                <span class="text-xs text-gray-700">{{ __('participant.yes') }}</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-50 p-0.5 rounded">
                                <input type="radio" 
                                       id="filter-${category.slug}-no" 
                                       name="${category.slug}"
                                       value="false"
                                       class="mr-1 text-xs">
                                <span class="text-xs text-gray-700">{{ __('participant.no') }}</span>
                            </label>
                        </div>
                    `;
                    break;
                    
                case 'text':
                    if (category.metadata?.allowed_values) {
                        // Dropdown for text type with allowed values
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${displayName}
                            </label>
                            <select id="filter-${category.slug}" 
                                    name="${category.slug}"
                                    class="w-full px-2 py-1 text-xs bg-[#FDF8FE]  border border-primary rounded-md">
                                <option value="">{{ __('participant.select') }}</option>
                                ${category.metadata.allowed_values.map(value => 
                                    `<option value="${value}">${value}</option>`
                                ).join('')}
                            </select>
                        `;
                    } else {
                        // Regular text input
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${displayName}
                            </label>
                            <input type="text" 
                                   id="filter-${category.slug}" 
                                   name="${category.slug}"
                                   placeholder="{{ __('participant.enter_value') }}"
                                   class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md">
                        `;
                    }
                    break;
                    
                default:
                    inputHtml = `
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            ${displayName}
                        </label>
                        <input type="text" 
                               id="filter-${category.slug}" 
                               name="${category.slug}"
                               placeholder="{{ __('participant.enter_value') }}"
                               class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md">
                    `;
                    break;
            }
            
            filterDiv.innerHTML = inputHtml;
            
            // Add event listeners for range inputs
            if (category.value_type === 'numeric') {
                const rangeInput = filterDiv.querySelector(`#filter-${category.slug}`);
                const valueDisplay = filterDiv.querySelector(`#filter-${category.slug}-value`);
                
                if (rangeInput && valueDisplay) {
                    rangeInput.addEventListener('input', (e) => {
                        valueDisplay.textContent = e.target.value;
                    });
                }
            }
            
            return filterDiv;
        }

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
        window.ContentRenderer.matchCardHeights();
    </script>
@endpush
