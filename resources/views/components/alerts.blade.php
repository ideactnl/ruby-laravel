<div class="max-w-4xl mx-auto mb-4 space-y-2">
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="bg-green-100 text-green-800 px-4 py-3 rounded relative" @click.away="show = false">
            {{ session('success') }}
            <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="bg-red-100 text-red-800 px-4 py-3 rounded relative" @click.away="show = false">
            {{ session('error') }}
            <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded relative" @click.away="show = false">
            <strong>Validation Errors:</strong>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
        </div>
    @endif
</div>
