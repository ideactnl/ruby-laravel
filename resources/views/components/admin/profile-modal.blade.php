<div x-data="{ 
  open:false,
  isBusy:false,
  async submit(e){
    e.preventDefault();
    if(this.isBusy) return;
    this.isBusy=true;
    try{
      // Prepare form data
      const form = e.target.closest('form');
      const data = new FormData(form);
      // CSRF
      const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
      const res = await fetch('{{ route('profile.update') }}', {
        method:'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
        body: data,
      });
      const payload = await res.json().catch(()=>({}));
      if(res.ok && payload?.ok){
        window.dispatchEvent(new CustomEvent('alert:success', { detail:{ key:'profile', title:'Profile', message: payload.message || 'Updated.' } }));
        window.dispatchEvent(new CustomEvent('profile:updated', { detail: { user: payload.user || {} } }));
        this.open=false;
      }else{
        const msg = payload?.message || 'Update failed.';
        window.dispatchEvent(new CustomEvent('alert:error', { detail:{ key:'profile', title:'Profile', message: msg } }));
      }
    }catch(err){
      window.dispatchEvent(new CustomEvent('alert:error', { detail:{ key:'profile', title:'Profile', message:'Something went wrong.' } }));
    }
    this.isBusy=false;
  }
}" x-on:profile:open.window="open=true">
  <template x-teleport="body">
    <div class="fixed inset-0 z-[100]" x-show="open" x-transition.opacity style="display:none">
      <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl" x-show="open" x-transition.scale>
          <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="text-base font-semibold">My Profile</h3>
            <button class="p-2 rounded-md hover:bg-gray-100" @click="open=false" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <form action="{{ route('profile.update') }}" method="POST" class="px-5 py-4 space-y-5" @submit.prevent="submit($event)">
            @csrf
            <x-form.group name="name">
              <x-form.label for="modal_name">Name</x-form.label>
              <x-form.input name="name" id="modal_name" type="text" value="{{ old('name', auth()->user()?->name) }}" />
            </x-form.group>

            <x-form.group name="email">
              <x-form.label for="modal_email">Email</x-form.label>
              <x-form.input name="email" id="modal_email" type="email" value="{{ auth()->user()?->email }}" disabled aria-disabled="true" tabindex="-1" class="bg-gray-50 cursor-not-allowed" />
            </x-form.group>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <x-form.group name="current_password">
                <x-form.label for="modal_current_password">Current Password</x-form.label>
                <x-form.input name="current_password" id="modal_current_password" type="password" autocomplete="current-password" />
              </x-form.group>
              <x-form.group name="new_password">
                <x-form.label for="modal_new_password">New Password</x-form.label>
                <x-form.input name="new_password" id="modal_new_password" type="password" autocomplete="new-password" />
              </x-form.group>
              <div class="sm:col-span-2">
                <x-form.group name="new_password_confirmation">
                  <x-form.label for="modal_new_password_confirmation">Confirm New Password</x-form.label>
                  <x-form.input name="new_password_confirmation" id="modal_new_password_confirmation" type="password" autocomplete="new-password" />
                </x-form.group>
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
              <button type="button" class="rounded-md bg-white hover:bg-primary/5 border border-primary/30 text-primary px-4 py-2 text-md font-semibold shadow hover:opacity-90 inline-flex items-center gap-2 transition-colors" @click="open=false" :disabled="isBusy">Cancel</button>
              <button type="submit" class="rounded-md bg-primary border border-primary px-4 py-2 text-md font-semibold text-white shadow hover:bg-primary-800 hover:border-primary-800 inline-flex items-center gap-2 transition-colors" :class="isBusy ? 'opacity-60 pointer-events-none' : ''">
                <i class="fa-solid fa-floppy-disk"></i>
                <span x-text="isBusy ? 'Saving…' : 'Save'"></span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
</div>
