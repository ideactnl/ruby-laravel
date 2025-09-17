@extends('layouts.admin.guest')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Reset Password') }}</h2>

        @if (session('status'))
            <div class="mb-4 text-green-600 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form 
            method="POST" 
            action="{{ route('password.email') }}"
            x-data="{ loading: false }"
            @submit.prevent="
                if ($el.checkValidity()) {
                    loading = true;
                    $el.submit();
                } else {
                    $el.reportValidity();
                }
            "
        >
            @csrf

            <x-form.group name="email">
                <x-form.label name="email" required>{{ __('Email Address') }}</x-form.label>
                <x-form.input 
                    name="email" 
                    type="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="email"
                />
            </x-form.group>

            <div class="flex items-center justify-between mt-6">
                <x-form.button>
                    {{ __('Send Password Reset Link') }}
                </x-form.button>

                <a class="text-sm text-indigo-600 hover:underline ml-4" href="{{ route('login') }}">
                    {{ __('Back to login') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
