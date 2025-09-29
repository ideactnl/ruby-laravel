<aside
    class="fixed inset-y-0 left-0 z-[99] w-full bg-white text-black transform md:bg-primary md:text-white shadow-none transition-transform duration-300
           md:inset-y-0 md:left-0 md:w-64 md:translate-x-0 flex flex-col overflow-x-hidden"
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    aria-label="Participant sidebar"
>
    <!-- Logo panel -->
    <div class="logo-panel mb-1 pl-3 pr-0 pt-0">
        <div class="h-28 flex items-center justify-center px-0">
            <img src="{{ asset('images/logo.png') }}" alt="RubyNU logo" class="max-h-20 w-auto object-contain" />
        </div>
    </div>

    <div class="p-0 md:pl-3 md:pr-0">
        <nav class="flex-1 flex flex-col justify-between overflow-visible">
            <ul class="space-y-2 md:pl-1 pr-0 sidebar-list-nav">
            @php
                $items = [
                    ['label' => 'Calendar', 'href' => route('participant.dashboard'), 'active' => request()->routeIs('participant.dashboard')],
                    ['label' => 'Daily view', 'href' => route('participant.daily-view', ['date' => now()->toDateString()]), 'active' => request()->routeIs('participant.daily-view')],
                    ['label' => 'Education', 'href' => route('participant.education'), 'active' => request()->routeIs('participant.education')],
                    ['label' => 'Selfmanagement', 'href' => route('participant.self-management'), 'active' => request()->routeIs('participant.self-management')],
                    ['label' => 'Links to external websites', 'href' => route('participant.external-links'), 'active' => request()->routeIs('participant.external-links')],
                    ['label' => 'Export', 'href' => route('participant.pbac'), 'active' => request()->routeIs('participant.pbac')],
                    ['label' => 'General information', 'href' => route('participant.general-information'), 'active' => request()->routeIs('participant.general-information')],
                ];
            @endphp

            @foreach ($items as $item)
                @php $isActive = $item['active']; @endphp
                <li>
                    <a href="{{ $item['href'] }}"
                       class="group relative flex h-14 items-center gap-3 pl-5 pr-6 text-sm font-medium transition
                              {{ $isActive
                                    ? ' active-nav bg-white text-neutral-900 rounded-l-[26px] rounded-r-[0px] shadow'
                                    : 'text-white rounded-xl' }}">

                        <!-- Icon (Font Awesome) -->
                      <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center shadow
    {{ $isActive ? 'rounded-lg bg-white text-black' : 'rounded-full bg-transparent text-white' }}">
    @php
        $iconMap = [
            'Calendar' => 'fa-calendar-days',
            'Daily view' => 'fa-eye',
            'Education' => 'fa-graduation-cap',
            'Selfmanagement' => 'fa-hand-holding-dollar',
            'Links to external websites' => 'fa-external-link-alt',
            'Export' => 'fa-file-export',
            'General information' => 'fa-circle-info',
        ];
        $iconCls = $iconMap[$item['label']] ?? 'fa-circle';
    @endphp
    <i class="fa-solid {{ $iconCls }} text-[18px]"></i>
</span>

                        <span class="font-semibold tracking-wide leading-tight">
                            @if($item['label'] === 'Links to external websites')
                                Links to external<br>websites
                            @else
                                {{ $item['label'] }}
                            @endif
                        </span>
                    </a>
                </li>
            @endforeach
            </ul>
        </nav>
    </div>
</aside>
