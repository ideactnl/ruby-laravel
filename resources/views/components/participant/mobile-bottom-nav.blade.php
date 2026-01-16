@php
    $currentLocale = app()->getLocale();
    $routePrefix = $currentLocale !== config('app.locale') ? $currentLocale . '.' : '';

    $items = [
        [
            'key' => 'home',
            'label' => __('participant.home'),
            'href' => route($routePrefix . 'participant.dashboard'),
            'active' => request()->routeIs('*participant.dashboard'),
            'icon' => asset('icons/home.png'),
        ],
        [
            'key' => 'daily_view',
            'label' => __('participant.daily_view'),
            'href' => route($routePrefix . 'participant.daily-view', ['date' => now()->toDateString()]),
            'active' => request()->routeIs('*participant.daily-view'),
            'icon' => asset('icons/view.png'),
        ],
        [
            'key' => 'education',
            'label' => __('participant.education'),
            'href' => route($routePrefix . 'participant.education'),
            'active' => request()->routeIs('*participant.education'),
            'icon' => asset('icons/education.png'),
        ],
        [
            'key' => 'more',
            'label' => __('participant.more'),
            'href' => route($routePrefix . 'participant.settings'),
            'active' => request()->routeIs('*participant.settings'),
            'icon' => asset('icons/more.png'),
        ],
    ];
@endphp

<nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-[#FDF8FE]" x-data="{ moreOpen: false, navigating: false }">
    <!-- Loader Overlay -->
    <div x-show="navigating" class="fixed inset-0 z-[60] flex items-center justify-center "
        style="display: none;">
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

    <!-- BOTTOM TABS -->
    <div class="flex justify-around py-2 text-xs text-black text-[14px] ">
        @foreach ($items as $item)
            <a href="{{ $item['href'] }}" @click="navigating = true; if('vibrate' in navigator) { try { navigator.vibrate(10); } catch(e) {} }"
                class="flex flex-col items-center gap-1 transition
                   {{ $item['active'] ? 'text-primary font-semibold' : '' }}">

                <img src="{{ $item['icon'] }}" alt="{{ $item['label'] }}" class="footer-img">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
