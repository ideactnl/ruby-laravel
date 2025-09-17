@extends('layouts.participant.guest')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="max-w-xl text-center p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Welcome to the Ruby app</h1>
        <p class="text-gray-600 mb-6">Empowering Women to improve their menstrual health.</p>

        <div class="mb-6">
            <img src="{{ url('images/ruby-illustration.png') }}" alt="Illustration" class="mx-auto h-64">
        </div>

        <div class="flex justify-center gap-4">
            <a href="{{ route('participant.web.login') }}"
               class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 shadow">
                User Login
            </a>
            <a href="{{ route('medical-specialist.login') }}"
               class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 shadow">
                Share your calendar
            </a>
        </div>
    </div>
</div>
@endsection
