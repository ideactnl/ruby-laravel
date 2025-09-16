<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-t-0 border-[#fff] md:ml-64">
    <div class="mx-auto w-full px-5 sm:px-8 lg:px-10 border-b border-[#ebebeb]">
        <div class="flex h-20 items-center justify-between border-b border-gray-100/70">
            <div class="flex min-w-0 items-center gap-4">
                <button class="md:hidden rounded-md p-2 text-gray-700 hover:bg-gray-100" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="truncate">
                    <h1 class="truncate text-lg font-bold tracking-wide text-[#000] sm:text-2xl">@yield('navbar_title', 'RUBY WOMEN ')</h1>
                    <p class="text-xs text-gray-600 mt-2 sm:text-sm">@yield('navbar_subtitle', 'Your Monthly Overview of data from the Ruby Mobile App')</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden rounded-full bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 sm:inline">{{ now()->format('d M Y, l') }}</span>
                @auth('participant-web')
                    <div class="relative" x-data="{open:false, async logout(){
                        try {
                          await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
                          const token = decodeURIComponent((document.cookie.split('; ').find(c=>c.startsWith('XSRF-TOKEN='))||'').split('=')[1]||'');
                          await fetch('{{ url('/api/v1/participant/logout') }}', { method:'POST', headers: { 'Accept':'application/json', 'X-XSRF-TOKEN': token }, credentials:'include' });
                        } catch(e) {}
                        window.location.href = '{{ route('participant.web.login') }}';
                    }}" @click.outside="open=false">
                        <button @click="open=!open" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#5E0F0F] text-white focus:outline-none" aria-label="Account">
                            P
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-44 rounded-md border border-gray-200 bg-white shadow-lg py-1 z-50" style="display:none">
                            <a href="{{ route('participant.dashboard') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <button @click="logout()" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50">Logout</button>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
