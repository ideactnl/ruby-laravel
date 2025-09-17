@props(['name'])

<select
    name="{{ $name }}"
    id="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'w-full px-3.5 py-2.5 border rounded-lg bg-white/90 appearance-none '
            . ($errors->has($name)
                ? 'border-red-500 focus:ring-1 focus:ring-red-200 focus:outline-0 focus:border-red-500'
                : 'border-gray-300 focus:border-[#555] focus:ring-1 focus:outline-0 focus:ring-[#5E0F0F]/20')
    ]) }}
>
    {{ $slot }}
</select>
