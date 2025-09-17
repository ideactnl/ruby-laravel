<aside
    class="fixed inset-y-0 left-0 z-[99] w-72 transform bg-[#3C0606] text-white shadow-none transition-transform duration-300
           md:inset-y-0 md:left-0 md:w-64 md:translate-x-0 flex flex-col overflow-x-hidden"
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    aria-label="Participant sidebar"
>
    <!-- Logo panel -->
    <div class="logo-panel mb-1 pl-3 pr-0 pt-0 pb-3">
        <div class="h-28 flex items-center justify-center px-0">
            <img src="{{ asset('images/logo.png') }}" alt="RubyNU logo" class="max-h-20 w-auto object-contain" />
        </div>
    </div>

    <div class="pl-3 pr-0">
        <nav class="flex-1 flex flex-col justify-between overflow-y-auto overflow-x-hidden">
            <div class="h-3"></div>
            <ul class="space-y-2 pl-1 pr-0 sidebar-list-nav">
            @php
                $items = [
                    ['label' => 'Calendar', 'href' => route('participant.dashboard'), 'active' => request()->routeIs('participant.dashboard')],
                    ['label' => 'Daily view', 'href' => route('participant.daily-view', ['date' => now()->toDateString()]), 'active' => request()->routeIs('participant.daily-view')],
                    ['label' => 'Education', 'href' => '#', 'active' => false],
                    ['label' => 'Selfmanagement', 'href' => '#', 'active' => false],
                    ['label' => 'Myths & facts', 'href' => '#', 'active' => false],
                    ['label' => 'Export', 'href' => Route::has('participant.pbac') ? route('participant.pbac') : '#', 'active' => request()->routeIs('participant.pbac')],
                    ['label' => 'General information', 'href' => '#', 'active' => false],
                ];
            @endphp

            @foreach ($items as $item)
                @php $isActive = $item['active']; @endphp
                <li>
                    <a href="{{ $item['href'] }}"
                       class="group relative flex h-14 items-center gap-3 pl-5 pr-6 text-sm font-medium transition
                              {{ $isActive
                                    ? ' active-nav bg-white text-[#111] rounded-l-[26px] rounded-r-[0px] shadow'
                                    : 'text-white hover:bg-white/10 rounded-xl' }}">

                        <!-- Icon (Font Awesome) -->
                      <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center shadow
    {{ $isActive ? 'rounded-lg bg-white text-black' : 'rounded-full bg-transparent text-white' }}">
    @php
        $iconMap = [
            'Calendar' => 'fa-calendar-days',
            'Daily view' => 'fa-eye',
            'Education' => 'fa-graduation-cap',
            'Selfmanagement' => 'fa-hand-holding-dollar',
            'Myths & facts' => 'fa-clipboard-check',
            'Export' => 'fa-file-export',
            'General information' => 'fa-circle-info',
        ];
        $iconCls = $iconMap[$item['label']] ?? 'fa-circle';
    @endphp
    <i class="fa-solid {{ $iconCls }} text-[18px]"></i>
</span>

                        <span class="whitespace-nowrap font-semibold tracking-wide">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
            </ul>
        </nav>
    </div>
</aside>
