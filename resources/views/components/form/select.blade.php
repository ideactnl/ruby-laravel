@props(['name', 'enhanced' => false])

@php
$chipClasses = 'inline-flex w-full items-center shadow rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-[#555] bg-[#fff]
 focus:ring-1 focus:outline-0 focus:ring-[#fff] focus:text-[#5E0F0F] focus:bg-[#5E0F0F]/5 focus:border-[#5E0F0F]';
$classes = $chipClasses;
@endphp

@if($enhanced)
<div x-data="{}" x-init="new TomSelect($refs.select, { create: false, sortField: { field: 'text', direction: 'asc' }, controlInput: null })">
    <select x-ref="select" name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </select>
</div>
@else
<div class="relative inline-flex w-full">
    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge(['class' => $classes . ' pr-9 appearance-none']) }}>
        {{ $slot }}
    </select>
    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-[#555]">
        <i class="fa-solid fa-chevron-down text-xs"></i>
    </span>
</div>
@endif
