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
                        videoCard.className = 'rounded-[10px] overflow-hidden bg-[#FDF8FE] flex flex-col';


                        videoCard.innerHTML = `
                            <div class="aspect-[9/16]">
                                <iframe class="w-full h-full"
                                        src="${video.embed_url}"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy"></iframe>
                            </div>


                             <div class="p-4 flex-1 flex flex-col items-start rounded-tl-none rounded-tr-none rounded-b-[10px] rounded-tl-none rounded-tr-none border border-t-0 border-primary bg-[#FDF8FE]">
                                <h3 class="text-[14px] font-semibold text-black mb-[6px]">${video.title}</h3>
                            </div>

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

    </script>
@endpush