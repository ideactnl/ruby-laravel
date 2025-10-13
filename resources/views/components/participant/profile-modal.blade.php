<div x-data="{
    open: false,
    loading: true,
    profile: null,
    async loadProfile() {
        if (!this.open) return;
        this.loading = true;
        try {
            const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
            const res = await fetch('{{ route('participant.api.profile') }}', {
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            });
            const payload = await res.json();
            if (res.ok) {
                this.profile = payload.profile;
            } else {
                window.dispatchEvent(new CustomEvent('alert:error', { detail: { key: 'profile', title: 'Profile', message: 'Could not load profile.' } }));
                this.open = false;
            }
        } catch (e) {
            window.dispatchEvent(new CustomEvent('alert:error', { detail: { key: 'profile', title: 'Profile', message: 'Something went wrong.' } }));
            this.open = false;
        }
        this.loading = false;
    }
}" x-on:profile:open.window="open=true; loadProfile()">
    <template x-teleport="body">
        <div class="fixed inset-0 z-[100]" x-show="open" x-transition.opacity.duration.300ms x-cloak style="display:none">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open=false"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4" @click.self="open=false">
                <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-neutral-200/50" x-show="open"
                    x-transition.scale.origin.center.duration.300ms @click.stop>
                    <!-- Header with gradient -->
                    <div
                        class="relative px-6 py-5 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-800)] rounded-t-3xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <i class="fas fa-user text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">My Profile</h3>
                                    <p class="text-white/80 text-sm">Account information & settings</p>
                                </div>
                            </div>
                            <button @click="open=false"
                                class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors backdrop-blur-sm">
                                <i class="fas fa-times text-white text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6 flex flex-col overflow-y-auto max-h-[calc(100vh-8rem)] sm:max-h-[calc(100vh-10rem)]"
                        style="scrollbar-width: thin; scrollbar-color: var(--color-primary) transparent;">
                        <!-- Loading State -->
                        <div x-show="loading" x-transition.opacity.duration.200ms
                            class="flex-1 flex flex-col items-center justify-center py-12">
                            <div class="relative">
                                <!-- Outer ring -->
                                <div class="w-12 h-12 border-2 border-[var(--color-primary)]/10 rounded-full"></div>
                                <!-- Spinning ring -->
                                <div
                                    class="absolute inset-0 w-12 h-12 border-2 border-transparent border-t-[var(--color-primary)] border-r-[var(--color-primary)]/60 rounded-full animate-spin">
                                </div>
                                <!-- Inner pulse -->
                                <div
                                    class="absolute inset-2 w-8 h-8 bg-[var(--color-primary)]/10 rounded-full animate-pulse">
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="text-[var(--color-neutral-700)] text-sm font-medium">Loading profile...</p>
                                <p class="text-[var(--color-neutral-700)]/60 text-xs mt-1">Please wait a moment</p>
                            </div>
                        </div>

                        <!-- Profile Content -->
                        <div x-show="!loading && profile" x-transition.opacity.duration.300ms class="space-y-5 flex-1">
                            <!-- Registration Info Card -->
                            <div
                                class="bg-[var(--color-accent-50)] rounded-2xl p-4 border border-[var(--color-accent)]/10">
                                <h4
                                    class="text-sm font-semibold text-[var(--color-accent)] mb-3 flex items-center gap-2">
                                    <i class="fas fa-id-card text-sm"></i>
                                    Registration Details
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-[var(--color-neutral-700)]">Registration Number</span>
                                        <span
                                            class="font-semibold text-sm text-[var(--color-neutral-900)] bg-white px-3 py-1 rounded-lg"
                                            x-text="profile?.registration_number"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-[var(--color-neutral-700)]">Registered On</span>
                                        <span class="font-medium text-sm text-[var(--color-neutral-900)]"
                                            x-text="profile?.created_at ? new Date(profile.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : ''"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Card -->
                            <div class="bg-white rounded-2xl p-4 border border-[var(--color-neutral-200)]">
                                <h4
                                    class="text-sm font-semibold text-[var(--color-neutral-900)] mb-3 flex items-center gap-2">
                                    <i class="fas fa-user-shield text-sm"></i>
                                    Privacy Settings
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-[var(--color-neutral-700)]">Data Sharing</span>
                                        <span class="px-3 py-1.5 text-xs font-medium rounded-xl"
                                            :class="profile?.enable_data_sharing ?
                                                'bg-[var(--color-success-50)] text-[var(--color-success)] border border-[var(--color-success)]/20' :
                                                'bg-[var(--color-neutral-200)] text-[var(--color-neutral-700)] border border-[var(--color-neutral-200)]'"
                                            x-text="profile?.enable_data_sharing ? 'Enabled' : 'Disabled'"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-[var(--color-neutral-700)]">Research
                                            Participation</span>
                                        <span class="px-3 py-1.5 text-xs font-medium rounded-xl"
                                            :class="profile?.opt_in_for_research ?
                                                'bg-[var(--color-success-50)] text-[var(--color-success)] border border-[var(--color-success)]/20' :
                                                'bg-[var(--color-neutral-200)] text-[var(--color-neutral-700)] border border-[var(--color-neutral-200)]'"
                                            x-text="profile?.opt_in_for_research ? 'Opted In' : 'Opted Out'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Specialist Access Card -->
                            <div class="bg-white rounded-2xl p-4 border border-[var(--color-neutral-200)]">
                                <h4
                                    class="text-sm font-semibold text-[var(--color-neutral-900)] mb-3 flex items-center gap-2">
                                    <i class="fas fa-user-md text-sm"></i>
                                    Medical Specialist Access
                                </h4>
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <span class="text-sm text-[var(--color-neutral-700)]">PIN Expires</span>
                                        <p class="font-medium text-sm text-[var(--color-neutral-900)] mt-1"
                                            x-text="profile?.medical_specialist_temporary_pin_expires_at ? new Date(profile.medical_specialist_temporary_pin_expires_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Not set'">
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span x-show="profile?.medical_specialist_pin_expired"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-[var(--color-danger-50)] text-[var(--color-danger)] border border-[var(--color-danger)]/20 flex items-center gap-1">
                                            <i class="fas fa-exclamation-circle text-xs"></i>
                                            Expired
                                        </span>
                                        <span
                                            x-show="!profile?.medical_specialist_pin_expired && profile?.medical_specialist_temporary_pin_expires_at"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-[var(--color-success-50)] text-[var(--color-success)] border border-[var(--color-success)]/20 flex items-center gap-1">
                                            <i class="fas fa-check-circle text-xs"></i>
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Note Dropdown -->
                            <div class="bg-[var(--color-accent-50)]/50 rounded-2xl border border-[var(--color-accent)]/10 mt-4"
                                x-data="{ expanded: false }">
                                <button @click="expanded = !expanded"
                                    class="w-full p-4 flex items-center justify-between text-left hover:bg-[var(--color-accent-50)]/70 transition-colors rounded-2xl">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-mobile-alt text-[var(--color-accent)] text-sm"></i>
                                        <span class="font-medium text-[var(--color-neutral-900)] text-sm">Want to change
                                            these settings?</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[var(--color-accent)] text-xs transition-transform duration-200"
                                        :class="{ 'rotate-180': expanded }"></i>
                                </button>
                                <div x-show="expanded" x-transition.opacity.duration.200ms class="px-4 pb-4">
                                    <p class="text-sm text-[var(--color-neutral-700)] leading-relaxed pl-7">
                                        Visit the <strong>Ruby</strong> mobile app to update your privacy preferences,
                                        data sharing options, and medical specialist access settings.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="px-6 py-4 bg-[var(--color-neutral-200)]/30 rounded-b-3xl border-t border-[var(--color-neutral-200)]/50">
                        <div class="flex justify-end">
                            <button type="button"
                                class="px-6 py-2.5 bg-white hover:bg-[var(--color-neutral-200)]/50 border border-[var(--color-neutral-200)] text-[var(--color-neutral-700)] hover:text-[var(--color-neutral-900)] text-sm font-medium rounded-xl transition-all duration-200 shadow-sm hover:shadow-md"
                                @click="open=false">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
