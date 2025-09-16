<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.participant.head')
</head>
<body class="min-h-screen bg-white">
    <div class="min-h-screen flex flex-col">
        <!-- Simple top bar with logo -->
        <header class="w-full border-b border-[#ebebeb] bg-[#520606] backdrop-blur">
            <div class="mx-auto max-w-7xl w-full px-5 sm:px-8 lg:px-10 h-16 flex items-center justify-between">
                <a href="/" class="inline-flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="RubyNU logo" class="h-13 w-auto object-contain" />
                    <span class="sr-only">RubyNU</span>
                </a>
                <div class="text-sm text-white">Participant Portal</div>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="w-full py-6 bg-[#520606]  text-center text-xs text-white">
            &copy; {{ date('Y') }} RubyNU. All rights reserved.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
