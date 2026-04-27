@extends('layouts.participant.app')

@section('navbar_title', __('participant.education'))
@section('navbar_subtitle', __('participant.educational_resources_learning_materials'))

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex gap-2">
                <select id="category-filter"
                    class="border border-gray-300 rounded-md px-3 py-3 text-sm text-white bg-primary">
                    <option value="">{{ __('participant.category') }}</option>
                    <option selected value="education">{{ __('participant.education') }}</option>
                    <option value="self">{{ __('participant.selfmanagement') }}</option>
                </select>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="fixed inset-0 z-[60] flex items-center justify-center hidden">
            <div class="flex flex-col items-center bg-[#f2cfcc] p-3 rounded-full">
                <svg class="animate-spin h-10 w-10 text-primary " xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
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
        const categorySelect = document.getElementById('category-filter');
        window.addEventListener('DOMContentLoaded', async () => {
            await triggerApiCall();
            // await loadCategories();
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

        async function triggerApiCall(category = 'education') {
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
                    body: JSON.stringify({
                        ...window.ContentRenderer.getFilterValues(),
                        location: category
                    })
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

                        // window.ContentRenderer.equalizeCardHeights();
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
                } catch (e) {}
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
                    }, {
                        passive: true
                    });

                    card.addEventListener('touchmove', (e) => {
                        if (!e.touches[0]) return;

                        const touch = e.touches[0];
                        const deltaX = Math.abs(touch.clientX - touchStartX);
                        const deltaY = Math.abs(touch.clientY - touchStartY);

                        if (deltaX > 10 || deltaY > 5) {
                            hasMoved = true;
                        }
                    }, {
                        passive: true
                    });

                    card.addEventListener('touchend', (e) => {
                        setTimeout(() => {
                            hasMoved = false;
                        }, 100);
                    }, {
                        passive: true
                    });

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

        categorySelect.addEventListener('change', () => {
            triggerApiCall(categorySelect.value);
        });
    </script>
@endpush
