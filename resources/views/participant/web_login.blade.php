@extends('layouts.participant.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-white p-4">
  <div class="w-full max-w-4xl bg-white rounded-lg overflow-hidden flex flex-col md:flex-row shadow-[0_5px_15px_rgba(0,0,0,0.35)]">



        <!-- Left Section -->
        <div class="w-full md:w-1/2 bg-[#520606] text-white flex flex-col justify-center items-center p-8 md:p-10">
            <div class="flex flex-col items-center text-center">
                <!-- Logo -->
                <div class="bg-white rounded-full p-4 mb-6">
                    <img src="/images/ruby-new-logo.png" alt="Logo" class="w-16 h-16 md:w-20 md:h-20">
                </div>
                <h2 class="text-xl font-semibold md:text-2xl">Ruby app</h2>
                <h3 class="text-2xl font-bold mt-2 md:text-3xl">Welcome back!</h3>
                <p class="mt-4 text-sm md:text-base leading-relaxed">
                    Empowering Women to improve their menstrual health.
                </p>
                <a href="#"
                    class="mt-6 inline-block border border-white rounded-full px-6 py-2 text-white font-semibold hover:bg-white hover:text-[#520606] transition">
                    Share your calendar
                </a>
            </div>
        </div>

        <!-- Right Section -->
        <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center" x-data="loginForm()">
            <h2 class="text-2xl font-bold mb-0 text-left uppercase md:text-3xl">Participant Login</h2>
            <p class="mt-2 text-sm mb-6 leading-relaxed md:text-base">
                Log in here with your account.
            </p>

            <template x-if="error">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4" x-text="error"></div>
            </template>

            <form @submit.prevent="submit" method="POST" class="space-y-4">
                <x-form.group name="registration_number">
                    <x-form.label name="registration_number" required>Registration Number</x-form.label>
                    <x-form.input name="registration_number" x-model="registration_number" type="text" required autofocus />
                </x-form.group>

                <x-form.group name="password">
                    <x-form.label name="password" required>Password</x-form.label>
                    <x-form.input name="password" x-model="password" type="password" required />
                </x-form.group>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-[#520606] hover:bg-[#eb5666] text-white font-semibold py-2 px-4 rounded transition disabled:opacity-50"
                        x-bind:disabled="loading">
                        <span x-show="!loading">Login</span>
                        <span x-show="loading">Logging in...</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function loginForm() {
    return {
        registration_number: '',
        password: '',
        loading: false,
        error: '',
        async submit() {
            this.error = '';
            this.loading = true;
            try {
                await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
                const xsrfToken = decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN=')).split('=')[1]);
                const res = await fetch('/api/v1/participant/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': xsrfToken
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        registration_number: this.registration_number,
                        password: this.password,
                    })
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    this.error = data.message || 'Login failed. Check your credentials.';
                } else {
                    window.location.href = '/participant/dashboard';
                }
            } catch (e) {
                this.error = 'An unexpected error occurred: ' + (e.message || e);
                console.error(e);
            }
            this.loading = false;
        }
    }
}
</script>
@endpush
