<nav class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="text-xl font-bold text-indigo-700">{{ config('app.name', 'Laravel') }}</a>
            </div>
            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-indigo-600">Register</a>
                    @endif
                @else
                    <a href="{{ url('/home') }}" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-red-600 ml-2">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav> 