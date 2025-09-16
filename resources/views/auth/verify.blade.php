@extends('layouts.admin.guest')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('Verify Your Email Address') }}</h2>

        {{-- Alert Messages --}}
        @if (session('resent'))
            <div class="mb-6">
                <div x-data="{ show: true }" x-show="show" class="bg-green-100 text-green-800 px-4 py-3 rounded relative" @click.away="show = false">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                    <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
                </div>
            </div>
        @endif

        <div class="mb-4 text-gray-700 text-center">
            {{ __('Before proceeding, please check your email for a verification link.') }}<br>
            {{ __('If you did not receive the email') }},
        </div>

        <form 
            method="POST" 
            action="{{ route('verification.resend') }}" 
            class="text-center"
            x-data="{ loading: false }"
            @submit.prevent="
                loading = true;
                $el.submit();
            "
        >
            @csrf
            <x-form.button>
                {{ __('Click here to request another') }}
            </x-form.button>
        </form>
    </div>
</div>
@endsection
