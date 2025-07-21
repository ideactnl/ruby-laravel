@extends('layouts.admin.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Register') }}</h2>

        <form
            method="POST"
            action="{{ route('register') }}"
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

            <x-form.group name="name">
                <x-form.label name="name" required>{{ __('Name') }}</x-form.label>
                <x-form.input 
                    name="name" 
                    type="text" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                />
            </x-form.group>

            <x-form.group name="email">
                <x-form.label name="email" required>{{ __('Email Address') }}</x-form.label>
                <x-form.input 
                    name="email" 
                    type="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email"
                />
            </x-form.group>

            <x-form.group name="password">
                <x-form.label name="password" required>{{ __('Password') }}</x-form.label>
                <x-form.input 
                    name="password" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                />
            </x-form.group>

            <x-form.group name="password_confirmation">
                <x-form.label name="password_confirmation" required>{{ __('Confirm Password') }}</x-form.label>
                <x-form.input 
                    name="password_confirmation" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                />
            </x-form.group>

            <div class="flex items-center justify-between mt-6">
                <x-form.button>
                    {{ __('Register') }}
                </x-form.button>
                <a class="text-sm text-indigo-600 hover:underline ml-4" href="{{ route('login') }}">
                    {{ __('Already have an account?') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
