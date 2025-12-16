<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.participant.head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="bg-white min-h-screen">
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

    @if(session('show_expiry_warning'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('alert:warning', {
                detail: {
                    key: 'session', 
                    title: 'Session', 
                    message: ` Your session is about to expire..
                        <button
                            data-extend-session
                            class="text-blue-600 underline font-medium bg-transparent p-0 border-0 cursor-pointer">
                            Continue session
                        </button> `}
            }));
        })

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-extend-session]');
        if (!btn) return;

        try {
            btn.disabled = true;
            btn.textContent = 'Extending...';

            const res = await fetch("{{ route('participant.refresh.session') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (data.success && data.data?.url) {
                window.location.href = data.data.url;
            }
        } catch {
            window.dispatchEvent(new CustomEvent('alert:error', {
                detail: {
                    title: 'Session',
                    message: 'Could not extend session. Please login again.'
                }
            }));
        }
    });
    </script>
    @endif
</body>

</html>
