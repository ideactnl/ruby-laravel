@props(['name', 'enhanced' => false])

@php
$chipClasses = 'inline-flex w-full items-center shadow rounded-md border border-gray-300 px-3.5 py-2.5 text-sm font-semibold text-neutral-600 bg-white
 focus:ring-1 focus:outline-0 focus:ring-white focus:text-primary focus:bg-primary/5 focus:border-primary';
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
    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-700">
        <i class="fa-solid fa-chevron-down text-xs"></i>
    </span>
</div>
@endif
