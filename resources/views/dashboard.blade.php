<x-app>
<div class="w-full max-w-lg mx-auto mt-10" x-data="dashboard()" x-init="init()">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Participant Dashboard</h2>
        <template x-if="error">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4" x-text="error"></div>
        </template>
        <div class="mb-6 text-center" x-show="participant">
            <h3 class="text-xl font-semibold mb-2">Welcome, <span x-text="participant?.registration_number"></span>!</h3>
            <p class="mb-1">Enable Data Sharing: <span class="font-medium" x-text="participant?.enable_data_sharing ? 'Yes' : 'No'"></span></p>
            <p class="mb-4">Opt In for Research: <span class="font-medium" x-text="participant?.opt_in_for_research ? 'Yes' : 'No'"></span></p>
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
        participant: null,
        async fetchParticipant() {
            this.error = '';
            try {
                const res = await fetch('/api/v1/participant/dashboard', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                });
                if (!res.ok) {
                    this.error = 'Failed to fetch participant data.';
                    if (res.status === 401) {
                        window.location.href = '/participant/web-login';
                    }
                    return;
                }
                const data = await res.json();
                this.participant = data.participant;
            } catch (e) {
                this.error = 'An unexpected error occurred.';
            }
        },
        async logout() {
            this.error = '';
            this.loading = true;
            try {
                const xsrfToken = decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN=')).split('=')[1]);
                const res = await fetch('/api/v1/participant/logout', {
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
        },
        init() {
            this.fetchParticipant();
        }
    }
}
</script>
</x-app>

</html>
