@extends('layouts.medical-specialist.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Medical Specialist Login
            </h2>
        </div>
        
        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="text-sm text-red-700">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('medical-specialist.login.submit') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="registration_number" class="sr-only">Registration Number</label>
                    <input id="registration_number" name="registration_number" type="text" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Registration Number" 
                        value="{{ old('registration_number') }}"
                        autofocus>
                </div>
                <div>
                    <label for="pin" class="sr-only">PIN</label>
                    <input id="pin" name="pin" type="password" required
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="PIN">
                </div>
            </div>

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <div class="text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
