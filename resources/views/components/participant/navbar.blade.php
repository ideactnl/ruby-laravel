<nav class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="text-xl font-bold text-indigo-700">{{ config('app.name', 'Laravel') }}</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth('participant-web')
                <a href="{{ route('participant.dashboard') }}" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
                <a href="{{ route('participant.pbac') }}" class="text-gray-700 hover:text-indigo-600">My Data</a>
                @endauth
            </div>
        </div>
    </div>
</nav> 