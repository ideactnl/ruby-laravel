<!DOCTYPE html>
<html lang="en">
@include('components.admin.head')
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col">
        @include('components.admin.navbar')
        <main class="flex-1">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
