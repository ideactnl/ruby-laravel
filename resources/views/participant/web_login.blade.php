@extends('layouts.participant.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-white p-4">
    <div class="bg-white rounded-lg  p-10 flex flex-col items-center text-center max-w-md w-full  shadow-md border border-[#5e0f0f]">
        <h2 class="text-3xl font-bold text-black-600 mb-4 uppercase">{{ __('participant.session_expired') }}</h2>
        <p class="text-gray-700 mb-6">{{ __('participant.please_refresh_your_session_to_continue') }}</p>

        {{-- <button 
            x-data
            x-on:click="refreshSession($el)"
            class="bg-[#800000] hover:bg-[#660000] text-white font-semibold px-8 py-3 rounded-xl shadow-md transition-colors">
            Refresh Session
        </button> --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshSession(el){
    el.disabled = true;
    el.innerText = 'Refreshing...';

    fetch('{{ route("participant.refresh.session") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(res => {
        console.log(res)
        if(res.success){
            window.dispatchEvent(new CustomEvent('alert:success', { detail: { message: 'Session refreshed successfully!' }}));
            setTimeout(() => window.location.href = res.data.url, 1000);
        } else {
            window.dispatchEvent(new CustomEvent('alert:error', { detail: { message: 'Failed to refresh session. Please login again.' }}));
            el.disabled = false;
            el.innerText = 'Refresh Session';
        }
    })
    .catch((err) => {
        console.log(err)
        window.dispatchEvent(new CustomEvent('alert:error', { detail: { message: 'Network error. Try again.' }}));
        el.disabled = false;
        el.innerText = 'Refresh Session';
    });
}
</script>
@endpush
