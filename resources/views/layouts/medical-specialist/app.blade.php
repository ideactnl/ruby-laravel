<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.medical-specialist.head')
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col">
        @include('components.medical-specialist.navbar')
        <main class="flex-1">
            <x-common.alerts />
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>