<button
    type="submit"
    x-bind:disabled="loading"
    :class="loading ? 'opacity-50 cursor-not-allowed' : ''"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-[#5E0F0F] text-white rounded-xl shadow-sm hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30 transition duration-150 ease-in-out'
    ]) }}
>
    <template x-if="loading">
        <svg class="animate-spin mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
    </template>
    <span class="inline" x-show="!loading" x-cloak>{{ trim($slot) }}</span>
    <span class="hidden" x-show="loading" x-cloak>{{ __('Processing...') }}</span>
</button>
