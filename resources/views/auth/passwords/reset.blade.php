@extends('layouts.admin.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Reset Password') }}</h2>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-4">
                <label for="email" class="block text-gray-700">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror">
                                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror">
                                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
            <div class="mb-4">
                <label for="password-confirm" class="block text-gray-700">{{ __('Confirm Password') }}</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                    {{ __('Reset Password') }}
                                </button>
                <a class="text-sm text-indigo-600 hover:underline ml-4" href="{{ route('login') }}">
                    {{ __('Back to login') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
