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

                    <div class="px-5 py-6 md:px-8">
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
    const REFRESH_INTERVAL = 60 * 1000; // 1 minute
    const isApiLogin = @json(session('api_login'));

    async function refreshSession() {

        const route = `{{ route('participant.refresh.session') }}`
        try {
            const res = await fetch(route, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!res.ok) {
                throw new Error('Session refresh failed');
            }

            const data = await res.json();

            if (!data.success) {
                throw new Error('Session expired');
            }

        } catch (e) {
            console.warn('Logging out due to session expiry');
            window.location.href = "/";
        }
    }

    if (isApiLogin) {
        setInterval(refreshSession, REFRESH_INTERVAL);
    }
    
</script>

</body>

</html>
