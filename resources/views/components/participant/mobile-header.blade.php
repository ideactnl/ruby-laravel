<div class="md:hidden sticky top-0 z-50 bg-[#FDF8FE] px-4 py-3" x-data="filterMenu()" x-init="init()">
    <div class="flex items-center justify-between">
       <a href="{{ route('participant.dashboard') }}">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="Logo"
            class="h-14 object-contain"
        />
        </a>
        @if (request()->routeIs('participant.dashboard'))
            <x-participant.domain-dropdown />
        @endif
    </div>
</div>

