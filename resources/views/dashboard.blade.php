<x-app>
<div class="w-full max-w-lg mx-auto mt-10" x-data="dashboard()">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Participant Dashboard</h2>
        <template x-if="error">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4" x-text="error"></div>
        </template>
        <div class="mb-6 text-center">
            <h3 class="text-xl font-semibold mb-2">Welcome, {{ $participant->registration_number }}!</h3>
            <p class="mb-1">Enable Data Sharing: <span class="font-medium">{{ $participant->enable_data_sharing ? 'Yes' : 'No' }}</span></p>
            <p class="mb-4">Opt In for Research: <span class="font-medium">{{ $participant->opt_in_for_research ? 'Yes' : 'No' }}</span></p>
        </div>
        <button @click="logout" class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded" x-bind:disabled="loading">
            <span x-show="!loading">Logout</span>
            <span x-show="loading">Logging out...</span>
        </button>
    </div>
</div>
<script>
function dashboard() {
    return {
        loading: false,
        error: '',
        async logout() {
            this.error = '';
            this.loading = true;
            try {
                const xsrfToken = decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN=')).split('=')[1]);
                const res = await fetch('/participant/web-logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': xsrfToken
                    },
                    credentials: 'include',
                });
                if (!res.ok) {
                    this.error = 'Logout failed.';
                } else {
                    window.location.href = '/participant/web-login';
                }
            } catch (e) {
                this.error = 'An unexpected error occurred.';
            }
            this.loading = false;
        }
    }
}
</script>
</x-app>

</html>
