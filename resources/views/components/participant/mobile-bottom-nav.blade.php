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

<nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white " x-data="{ moreOpen: false }">
    <!-- BOTTOM TABS -->
    <div class="flex justify-around py-2 text-xs text-black text-[14px] ">
        @foreach ($items as $item)
            <a href="{{ $item['href'] }}"
                class="flex flex-col items-center gap-1 transition
                   {{ $item['active'] ? 'text-primary font-semibold' : '' }}">

                <img src="{{ $item['icon'] }}" alt="{{ $item['label'] }}" class="footer-img">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
