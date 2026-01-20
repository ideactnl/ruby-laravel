<div class="md:hidden sticky top-0 z-50 bg-[#FDF8FE] px-4 py-3" x-data="filterMenu()" x-init="init()">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 flex-1 min-w-0"
             x-data="{
                wrappedData: null,
                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    return `${day}-${month}`;
                },
                getHeaderText() {
                    if (!this.wrappedData || !this.wrappedData.can_calculate) return '';
                    let text = @js(__('participant.wrapped_header'));
                    return text
                        .replace(':start', `<span class='text-primary font-extrabold'>${this.formatDate(this.wrappedData.start_date)}</span>`)
                        .replace(':end', `<span class='text-primary font-extrabold'>${this.formatDate(this.wrappedData.end_date)}</span>`);
                }
             }"
             @wrapped-data-loaded.window="wrappedData = $event.detail">

            <a href="{{ route('participant.dashboard') }}" class="flex-shrink-0"
               @click="if('vibrate' in navigator) { try { navigator.vibrate(20); } catch(e) {} }">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain" />
            </a>

            <div x-show="wrappedData && wrappedData.can_calculate"
                 x-transition.opacity
                 class="flex-1 min-w-0">
                <p class="text-[16px] leading-tight text-gray-900 font-bold" x-html="getHeaderText()"></p>
            </div>
        </div>
    </div>
</div>

