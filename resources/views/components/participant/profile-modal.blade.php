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
    <div class="fixed inset-0 z-[100]" x-show="open" x-transition.opacity style="display:none">
      <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl" x-show="open" x-transition.scale>
          <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="text-base font-semibold">My Profile</h3>
          </div>

          <div class="px-5 py-6">
            <div x-show="loading" class="text-center text-gray-500">Loading...</div>
            <div x-show="!loading && profile" class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Registration Number</span>
                <span class="font-medium text-sm" x-text="profile?.registration_number"></span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Registered On</span>
                <span class="font-medium text-sm" x-text="profile?.created_at ? new Date(profile.created_at).toLocaleDateString() : ''"></span>
              </div>
              <hr class="my-4">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Data Sharing Enabled</span>
                <span class="px-2 py-0.5 text-xs rounded-full" :class="profile?.enable_data_sharing ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" x-text="profile?.enable_data_sharing ? 'Yes' : 'No'"></span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Opt-in for Research</span>
                <span class="px-2 py-0.5 text-xs rounded-full" :class="profile?.opt_in_for_research ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" x-text="profile?.opt_in_for_research ? 'Yes' : 'No'"></span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Specialist PIN Expires</span>
                <span class="font-medium text-sm" x-text="profile?.medical_specialist_temporary_pin_expires_at ? new Date(profile.medical_specialist_temporary_pin_expires_at).toLocaleString() : 'Not set'"></span>
              </div>
            </div>
          </div>
          <div class="px-5 py-3 bg-gray-50 rounded-b-2xl text-right">
            <button type="button" class="px-4 py-2.5 rounded-xl border text-sm" @click="open=false">Close</button>
          </div>
        </div>
      </div>
    </div>
  </template>
</div>
