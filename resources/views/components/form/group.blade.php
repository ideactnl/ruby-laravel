@props(['name'])

<div class="mb-4">
    {{ $slot }}

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
