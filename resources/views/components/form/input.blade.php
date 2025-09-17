@props(['name', 'type' => 'text', 'variant' => 'admin'])

@php
$baseClasses = 'w-full px-3.5 py-2.5 border rounded-xl bg-white/90 shadow-sm placeholder:text-gray-400 ';
$adminClasses = $errors->has($name)
    ? 'border-red-500 focus:ring-2 focus:ring-red-200 focus:border-red-500'
    : 'border-gray-300 focus:border-[#5E0F0F] focus:ring-2 focus:ring-[#5E0F0F]/20';
$participantClasses = 'rounded-md border border-[#5E0F0F] px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30';

$classes = $baseClasses . ($variant === 'participant' ? $participantClasses : $adminClasses);
@endphp

<input
    x-data="{}"
    x-init="() => {
        if ('{{ $type }}' === 'date') {
            const fp = flatpickr($el, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: false
            });
            const ph = $el.getAttribute('data-ph');
            if (ph && fp?.altInput) { fp.altInput.setAttribute('placeholder', ph); }
        }
    }"
    name="{{ $name }}"
    id="{{ $name }}"
    type="{{ $type === 'date' ? 'text' : $type }}"
    @if($type === 'date') inputmode="none" pattern="\\d{4}-\\d{2}-\\d{2}" @endif
    {{ $attributes->merge([
        'class' => $classes,
        'data-ph' => $attributes->get('placeholder')
    ]) }}
>
