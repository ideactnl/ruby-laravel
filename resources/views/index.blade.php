@extends('layouts.participant.guest')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 px-4">
    <div class="w-full max-w-xl text-center p-6 bg-white rounded-lg shadow-md">

        <!-- Heading -->
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Welcome to the Ruby app
        </h1>
        <p class="text-gray-600 text-sm sm:text-base md:text-lg mb-6">
            Empowering Women to improve their menstrual health.
        </p>

        <!-- Illustration -->
        <div class="mb-6">
            <img src="{{ url('images/ruby-illustration.png') }}" 
                 alt="Illustration" 
                 class="mx-auto h-48 sm:h-64 md:h-72 w-auto">
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('participant.web.login') }}"
               class="px-4 py-2 sm:px-6 sm:py-3 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 shadow transition-all duration-200">
                User Login
            </a>
            <a href="{{ route('medical-specialist.login') }}"
               class="px-4 py-2 sm:px-6 sm:py-3 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 shadow transition-all duration-200">
                Share your calendar
            </a>
        </div>
    </div>
</div>
@endsection
