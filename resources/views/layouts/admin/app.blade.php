<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.admin.head')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="bg-white min-h-screen">
    <div class="min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 768 }" x-on:resize.window="sidebarOpen = window.innerWidth >= 768">
        @include('components.admin.navbar')

        <div class="relative flex items-stretch">
            @auth
                @include('components.admin.sidebar')

                <div class="fixed inset-0 z-30 bg-black/40 md:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen=false" style="display:none"></div>

                <main class="flex-1 md:ml-64">
                    <div class="relative px-5 py-6 sm:px-8 lg:px-10">
                        <x-common.alerts />
                        @yield('content')
                    </div>
                </main>
            @else
                <main class="flex-1">
                    <div class="relative px-5 py-6 sm:px-8 lg:px-10">
                        <x-common.alerts />
                        @yield('content')
                    </div>
                </main>
            @endauth
        </div>
    </div>
    @stack('scripts')
    @include('components.common.confirm')
</body>
</html>
