@extends('layouts.admin.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Login') }}</h2>

            <form
                method="POST"
                action="{{ route('login') }}"
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
                    class="mt-1 block w-full focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror"
                />
            </x-form.group>

            <x-form.group name="password">
                <x-form.label name="password" required>{{ __('Password') }}</x-form.label>
                <x-form.input 
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror"
                />
            </x-form.group>

            <x-form.group name="remember">
                <div class="flex items-center">
                    <input 
                        name="remember" 
                        id="remember"
                        type="checkbox"
                        {{ old('remember') ? 'checked' : '' }}
                        class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <label for="remember" class="text-gray-700">{{ __('Remember Me') }}</label>
                </div>
            </x-form.group>

            <div class="flex items-center justify-between mt-6">
                <x-form.button>
                 {{ __('Login') }}
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
