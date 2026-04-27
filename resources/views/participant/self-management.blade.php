@extends('layouts.participant.app')

@section('navbar_title', __('participant.selfmanagement'))
@section('navbar_subtitle', __('participant.tools_resources_managing_health'))

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex gap-2">
                <select id="category-filter"
                    class="border border-gray-300 rounded-md px-3 py-3 text-sm text-white bg-primary">
                    <option value="all">{{ __('participant.all') ?? 'All' }}</option>
                    <option value="education">{{ __('participant.education') }}</option>
                    <option value="selfmanagement" selected>{{ __('participant.selfmanagement') }}</option>
                </select>
            </div>
        </div>

        <!-- Loader Overlay -->
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

        <div id="selfmanagement-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4"
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
            await triggerApiCall(categorySelect ? categorySelect.value : 'selfmanagement');
        });

        async function triggerApiCall(category = 'selfmanagement') {
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
                        location: category
                    })
                });
                const data = await response.json();

                const content = data?.content || [];
                const educationGrid = document.getElementById('selfmanagement-grid');
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

                        const arrangedContent = window.ContentRenderer.interleaveContent(content);
                        arrangedContent.forEach(item => {
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

        if (categorySelect) {
            categorySelect.addEventListener('change', () => {
                triggerApiCall(categorySelect.value);
            });
        }
    </script>
@endpush
