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
    class="mx-4 mb-5 flex items-center justify-between rounded-full px-4 py-3 font-bold text-[18px]
           transition
           {{ $danger ? 'bg-[#E28D94] text-black' : 'bg-[#E28D94] text-black' }}"
>
    <div class="flex items-center gap-3">
        @if($icon)
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                <i class="fa-solid {{ $icon }} text-[20px]"></i>
            </span>
        @endif

        <span class="font-medium">{{ $label }}</span>
    </div>

    @unless($noArrow)
        <i class="fa-solid fa-chevron-right text-{20px}"></i>
    @endunless
</a>
