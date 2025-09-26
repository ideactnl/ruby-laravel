<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-t-0 border-white md:ml-64">
    <div class="mx-auto w-full px-5 md:px-8 border-b border-neutral-200">
        <div class="flex h-20 items-center justify-between border-b border-gray-100/70">
            <div class="flex min-w-0 items-center gap-4">
                <!-- Sidebar toggle (mobile) -->
                <button class="md:hidden rounded-md p-2 text-gray-700 hover:bg-gray-100" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="truncate hidden md:block">
                    <h1 class="truncate text-lg font-bold tracking-wide text-neutral-900 sm:text-2xl">@yield('navbar_title', 'Admin Console')</h1>
                    <p class="text-xs text-gray-600 mt-2 sm:text-sm">@yield('navbar_subtitle', 'Administrative area')</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden rounded-full bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 sm:inline">{{ now()->format('d M Y, l') }}</span>
                @auth
                    <div class="relative" x-data="{open:false, avatarInitial: '{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}'}" @click.outside="open=false" x-init="window.appCurrentUserName='{{ addslashes(Auth::user()->name ?? 'Admin') }}'; window.addEventListener('profile:updated', e=>{ const n=(e.detail?.user?.name||'').trim(); if(n){ avatarInitial = n.charAt(0).toUpperCase(); window.appCurrentUserName = n; } });">
                        <button @click="open=!open" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white focus:outline-none cursor-pointer hover:bg-primary-800 transition-colors" aria-label="Account">
                            <span x-text="avatarInitial">A</span>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-44 rounded-md border border-gray-200 bg-white shadow-lg py-1 z-50" style="display:none">
                            <button type="button" @click="open=false; window.dispatchEvent(new CustomEvent('profile:open'))" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">Profile</button>
                            <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                                @csrf
                                <button type="submit" class="w-full text-left text-sm text-red-600 hover:bg-gray-50 cursor-pointer">Logout</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>