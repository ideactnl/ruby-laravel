@props(['name', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium text-gray-800 mb-1']) }}>
    {{ $slot }}
    @if($required)
        <span class="text-red-600">*</span>
    @endif
</label>
