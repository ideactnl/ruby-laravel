@extends('layouts.participant.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-white p-4">
    <div class="bg-white rounded-lg  p-10 flex flex-col items-center text-center max-w-md w-full  shadow-md border border-[#5e0f0f]">
        <h2 class="text-3xl font-bold text-black-600 mb-4 uppercase">{{ __('participant.session_expired') }}</h2>
        <p class="text-gray-700 mb-6">{{ __('participant.please_refresh_your_session_to_continue') }}</p>
    </div>
</div>
@endsection
