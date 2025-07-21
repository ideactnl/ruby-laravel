@extends('layouts.participant.app')

@section('content')
<div class="w-full max-w-md mx-auto mt-10" x-data="loginForm()">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Participant Login</h2>

        <template x-if="error">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4" x-text="error"></div>
        </template>

        <form @submit.prevent="submit" method="POST">
            <x-form.group name="registration_number">
                <x-form.label name="registration_number" required>Registration Number</x-form.label>
                <x-form.input 
                    name="registration_number"
                    x-model="registration_number"
                    type="text"
                    required
                    autofocus
                />
            </x-form.group>

            <x-form.group name="password">
                <x-form.label name="password" required>Password</x-form.label>
                <x-form.input 
                    name="password"
                    x-model="password"
                    type="password"
                    required
                />
            </x-form.group>

            <div class="mt-6">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded" x-bind:disabled="loading">
                    <span x-show="!loading">Login</span>
                    <span x-show="loading">Logging in...</span>
                </button>
            </div>
        </form>
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
                const xsrfToken = decodeURIComponent(
                    document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN=')).split('=')[1]
                );
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
