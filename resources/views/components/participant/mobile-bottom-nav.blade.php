@php
    $currentLocale = app()->getLocale();
    $routePrefix = $currentLocale !== config('app.locale') ? $currentLocale . '.' : '';
    $items = [
                [
                    'key' => 'home',
                    'label' => __('participant.home'),
                    'href' => route($routePrefix . 'participant.dashboard'),
                    'active' => request()->routeIs('*participant.dashboard'),
                    'icon' => 'fa-calendar-days',
                ],
                [
                    'key' => 'daily_view',
                    'label' => __('participant.daily_view'),
                    'href' => route($routePrefix . 'participant.daily-view', ['date' => now()->toDateString()]),
                    'active' => request()->routeIs('*participant.daily-view'),
                    'icon' => 'fa-eye',
                ],
                [
                    'key' => 'education',
                    'label' => __('participant.education'),
                    'href' => route($routePrefix . 'participant.education'),
                    'active' => request()->routeIs('*participant.education'),
                    'icon' => 'fa-circle-play',
                ],
                [
                    'key' => 'more',
                    'label' => __('participant.more'),
                    'href' => route($routePrefix . 'participant.settings'),
                    'active' => request()->routeIs('*participant.settings'),
                    'icon' => 'fa-ellipsis',
                ],

                       
            ];
@endphp

<nav
    class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white border-t"
    x-data="{ moreOpen: false }"
>
    <!-- BOTTOM TABS -->
    <div class="flex justify-around py-2 text-xs text-gray-700">
        @foreach ($items as $item)
      
                <a href="{{ $item['href'] }}"
                   class="flex flex-col items-center gap-1 transition
                   {{ $item['active'] ? 'text-primary font-semibold' : '' }}">
                   
                    <i class="fa-solid {{ $item['icon'] }} text-lg"></i>
                    {{ $item['label'] }}
                </a>
           
        @endforeach
    </div>
</nav>