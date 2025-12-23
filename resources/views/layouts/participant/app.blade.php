<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.participant.head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class=" min-h-screen  bg-[#FDF8FE]">
    <div class="min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 768 }"
        x-on:resize.window="sidebarOpen = window.innerWidth >= 768">
        @include('components.participant.navbar')

        <div class="relative">
            @auth('participant-web')
                @include('components.participant.sidebar')

                <div class="fixed inset-0 z-30 bg-black/40 md:hidden" x-show="sidebarOpen" x-transition.opacity
                    @click="sidebarOpen=false" style="display:none"></div>


                <main class="flex-1 md:ml-64">
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
    @include('components.participant.profile-modal')
    @stack('scripts')

    @if(session('api_login_expires_at'))
        <script>
            window.API_LOGIN_EXPIRES_AT = {{ session('api_login_expires_at')->timestamp }} * 1000;
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.API_LOGIN_EXPIRES_AT) return;

            const WARNING_BEFORE = 60 * 1000; // 60 seconds
            const expiresAt = window.API_LOGIN_EXPIRES_AT;

            let warningShown = false;

            const checkExpiry = () => {
                const now = Date.now();
                const remaining = expiresAt - now;

                // Hard expiry fallback (extra safety)
                if (remaining <= 0) {
                    window.location.href = "{{ route('participant.web.login') }}";
                    return;
                }

                // Show warning exactly when needed
                if (remaining <= WARNING_BEFORE && !warningShown) {
                    warningShown = true;

                    window.dispatchEvent(new CustomEvent('alert:warning', {
                        detail: {
                            key: 'session',
                            title: 'Session Expiring',
                            message: 'Your session is about to expire.'
                        }
                    }));
                }
            };

            // Run immediately + every second
            checkExpiry();
            setInterval(checkExpiry, 1000);
        });
    </script>
</body>

</html>
