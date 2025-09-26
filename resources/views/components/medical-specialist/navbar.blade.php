<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-t-0 border-white">
    <div class="mx-auto w-full px-5 md:px-8 border-b border-neutral-200">
        <div class="flex h-20 items-center justify-between border-b border-gray-100/70">
            <div class="flex min-w-0 items-center gap-4">
                <div class="truncate hidden md:block">
                    <h1 class="truncate text-lg font-bold tracking-wide text-neutral-900 sm:text-2xl">@yield('navbar_title', 'RUBY - MEDICAL SPECIALIST')</h1>
                    <p class="text-xs text-gray-600 mt-2 sm:text-sm">@yield('navbar_subtitle', 'Export Patient Data - Secure Medical Professional Access')</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden rounded-full bg-gray-100 px-4 py-2 text-xs font-medium text-gray-700 sm:inline">{{ now()->format('d M Y, l') }}</span>
                @if(session('medical_specialist_id'))
                    @php
                        $participant = \App\Models\Participant::find(session('medical_specialist_id'));
                        $expiryDate = optional($participant->medical_specialist_temporary_pin_expires_at)?->format('M j, Y H:i');
                    @endphp
                    <div class="relative" x-data="{open:false}" @click.outside="open=false">
                        <button @click="open=!open" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white focus:outline-none cursor-pointer hover:bg-primary-800 transition-colors" aria-label="Account">
                            <span>{{ strtoupper(substr($participant->registration_number ?? 'M', 0, 1)) }}</span>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-44 rounded-md border border-gray-200 bg-white shadow-lg py-1 z-50" style="display:none">
                            <button @click="logout()" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50 cursor-pointer">Logout</button>
                            <div class="font-medium">{{ $participant->registration_number ?? 'Medical Specialist' }}</div>
                            @if($expiryDate)
                                <div class="text-red-600 mt-1">Access expires: {{ $expiryDate }}</div>
                            @endif
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50 cursor-pointer">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('medical-specialist.login') }}" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:bg-primary-800 transition-colors">Login</a>
                @endif
            </div>
        </div>
    </div>
</nav>