@props([
    'label',
    'href' => null,
    'icon' => null,      // NEW
    'onclick' => null,
    'noArrow' => false,
    'danger' => false,
])

<a
    @if($href) href="{{ $href }}" @else href="#" @endif
    @if($onclick) onclick="{{ $onclick }}" @endif
    class="mx-4 mb-3 flex items-center justify-between rounded-full px-4 py-3 text-sm
           transition
           {{ $danger ? 'bg-red-500 text-white' : 'bg-[#E79A9E] text-white' }}"
>
    <div class="flex items-center gap-3">
        @if($icon)
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                <i class="fa-solid {{ $icon }} text-sm"></i>
            </span>
        @endif

        <span class="font-medium">{{ $label }}</span>
    </div>

    @unless($noArrow)
        <i class="fa-solid fa-chevron-right text-xs"></i>
    @endunless
</a>
