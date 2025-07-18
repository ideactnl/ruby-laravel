@extends('layouts.admin.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Verify Your Email Address') }}</h2>
                    @if (session('resent'))
            <div class="mb-4 text-green-600 text-center">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
        <div class="mb-4 text-gray-700 text-center">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
        </div>
        <form class="text-center" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                {{ __('Click here to request another') }}
            </button>
        </form>
    </div>
</div>
@endsection
