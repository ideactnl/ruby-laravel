<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.participant.head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class=" min-h-screen  bg-[#FDF8FE]">
    <div class="min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 768 }"
        x-on:resize.window="sidebarOpen = window.innerWidth >= 768">
        {{-- @include('components.participant.navbar') --}}

        {{-- Mobile --}}
@include('components.participant.mobile-header')

{{-- Desktop --}}
<div class="hidden md:block">
    @include('components.participant.navbar')
</div>

        <div class="relative">
            @auth('participant-web')
                @include('components.participant.sidebar')

                <div class="fixed inset-0 z-30 bg-black/40 md:hidden" x-show="sidebarOpen" x-transition.opacity
                    @click="sidebarOpen=false" style="display:none"></div>


                {{-- <main class="flex-1 md:ml-64"> --}}
                    <main class="flex-1 md:ml-64 pb-20 md:pb-0">

                    <div class="w-[90%] mx-auto p-0 md:w-auto md:mx-0 md:px-5 md:py-6">
                        <x-common.alerts />
                        @yield('content')
                    </div>
                </main>
            @else
                <main class="flex-1">
                    <div class="px-5 py-6 md:px-8">
                        <x-common.alerts />
                        @yield('content')
                    </div>
                </main>
            @endauth
        </div>

    </div>
    @include('components.participant.mobile-bottom-nav')

    @include('components.participant.profile-modal')
    @stack('scripts')

    @if(session('api_login_expires_at'))
        <script>
            window.API_LOGIN_EXPIRES_AT = {{ session('api_login_expires_at')->timestamp }} * 1000;
        </script>
    @endif

    <script>
    const REFRESH_INTERVAL = 30 * 1000; // 30 seconds
    const isApiLogin = @json(session('api_login'));

    async function refreshSession() {
        // Detect current section from URL
        const path = window.location.pathname;
        let section = 'dashboard';
        if (path.includes('/education')) section = 'education';
        else if (path.includes('/daily-view')) section = 'daily-view';
        else if (path.includes('/self-management')) section = 'self-management';
        else if (path.includes('/external-links')) section = 'external-links';
        else if (path.includes('/export')) section = 'export';
        else if (path.includes('/general-information')) section = 'general-information';
        else if (path.includes('/settings')) section = 'settings';

        const route = `{{ route('participant.refresh.session') }}`
        try {
            const res = await fetch(route, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ section: section }),
                credentials: 'same-origin',
                keepalive: true
            });

            if (!res.ok) {
                // Silent failure to avoid loops
                console.warn('Session refresh failed');
                return;
            }

            const data = await res.json();
            if (!data.success) {
                console.warn('Session expired');
            }

        } catch (e) {
            console.warn('Session refresh attempt failed:', e.message);
        }
    }

    // Send heartbeat every 30 seconds for all authenticated users
    @auth('participant-web')
        refreshSession(); // Initial call
        setInterval(refreshSession, REFRESH_INTERVAL);
    @endauth
</script>

</body>

</html>
