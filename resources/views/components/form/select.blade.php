@props(['name'])

<select
    name="{{ $name }}"
    id="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'w-full px-3 py-2 border rounded shadow-sm ' . ($errors->has($name) ? 'border-red-500' : 'border-gray-300')
    ]) }}
>
    {{ $slot }}
</select>
