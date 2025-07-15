<x-app>
<div class="w-full max-w-md mx-auto mt-10" x-data="loginForm()">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Participant Login</h2>
        <template x-if="error">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4" x-text="error"></div>
        </template>
        <form @submit.prevent="submit" action="/participant/web-login" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Registration Number</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" x-model="registration_number" required autofocus>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700">Password</label>
                <input type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" x-model="password" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded" x-bind:disabled="loading">
                <span x-show="!loading">Login</span>
                <span x-show="loading">Logging in...</span>
            </button>
        </form>
    </div>
</div>
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
                const res = await fetch('/participant/web-login', {
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
                let data;
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    this.error = 'Invalid server response.';
                    this.loading = false;
                    return;
                }
                if (!res.ok || !data.success) {
                    this.error = (data && data.message) || 'Login failed. Check your credentials.';
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
</x-app>