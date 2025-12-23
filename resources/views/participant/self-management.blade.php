@extends('layouts.participant.app')

@section('navbar_title', __('participant.selfmanagement'))
@section('navbar_subtitle', __('participant.tools_resources_managing_health'))

@section('content')
    <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex gap-2">
                <select class="border border-gray-300 rounded-md px-6 py-4 text-sm text-white bg-[#7B1C1C]">
                    <option>{{ __('participant.category') }}</option>
                </select>
                <select class="border border-gray-300 rounded-md px-3 py-3 text-sm text-white bg-[#7B1C1C]">
                    <option>{{ __('participant.recommended') }}</option>
                </select>
            </div>
        </div>

        <div id="selfmanagement-loading" class="py-8 text-gray-500 text-center">{{ __('participant.loading') }}</div>

        <div id="selfmanagement-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4"
            style="display: none;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch('/api/v1/participant/videos/self-management');
                const data = await response.json();
                const videos = data.videos || [];

                const selfManagementGrid = document.getElementById('selfmanagement-grid');
                const loadingElement = document.getElementById('selfmanagement-loading');

                if (selfManagementGrid) {
                    selfManagementGrid.innerHTML = '';

                    videos.forEach((video, index) => {
                        const videoCard = document.createElement('div');
                        videoCard.className = 'rounded-[10px] overflow-hidden shadow-md bg-white flex flex-col';

                        const maxLength = 25;
                        let subtitleContent = '';
                        if (video.subtitle) {
                            const isLong = video.subtitle.length > maxLength;
                            const truncated = isLong ? video.subtitle.substring(0, maxLength) + '...' : video.subtitle;
                            const subtitleId = `subtitle-${video.id}`;

                            subtitleContent = `
                                <div class="text-sm text-gray-600 sm-video-caption">
                                    <span id="${subtitleId}">${truncated}</span>
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
                        <div class="aspect-[9/16]">
                            <iframe class="w-full h-full"
                                    src="${video.embed_url}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy"></iframe>
                        </div>

                        <div class="p-4 flex-1 flex items-start rounded-tl-none rounded-tr-none rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary">${subtitleContent || '<div class="text-sm text-gray-600 sm-video-caption"></div>'}</div>
                        `;

                        selfManagementGrid.appendChild(videoCard);
                    });

                    loadingElement.style.display = 'none';
                    selfManagementGrid.style.display = 'grid';
                }
            } catch (error) {
                console.error('Error fetching videos:', error);
                const loadingElement = document.getElementById('selfmanagement-loading');
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
    </script>
@endpush
