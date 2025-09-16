@extends('layouts.admin.guest')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Confirm Password') }}</h2>

        <div class="mb-4 text-gray-700 text-center">
            {{ __('Please confirm your password before continuing.') }}
        </div>

        <form 
            method="POST" 
            action="{{ route('password.confirm') }}"
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

            <x-form.group name="password">
                <x-form.label name="password" required>{{ __('Password') }}</x-form.label>
                <x-form.input 
                    name="password" 
                    type="password" 
                    required 
                    autocomplete="current-password"
                />
            </x-form.group>

            <div class="flex items-center justify-between mt-6">
                <x-form.button>
                    {{ __('Confirm Password') }}
                </x-form.button>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:underline ml-4" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
