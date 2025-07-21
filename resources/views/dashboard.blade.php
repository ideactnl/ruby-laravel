@extends('layouts.admin.app')

@section('content')
<div class="container mx-auto mt-10 px-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Welcome, {{ Auth::user()->name }}!</h1>

        <div class="mb-6">
            @php
                $role = Auth::user()->getRoleNames()->first();
            @endphp

            @if ($role === 'superadmin')
                <div class="text-green-700 font-semibold">
                    You are logged in as a <strong>Super Admin</strong>.
                </div>
            @elseif ($role === 'admin')
                <div class="text-blue-700 font-semibold">
                    You are logged in as an <strong>Admin</strong>.
                </div>
            @elseif ($role === 'researcher')
                <div class="text-gray-700 font-semibold">
                    You are logged in as a <strong>Researcher</strong>.
                </div>
            @else
                <div class="text-red-700 font-semibold">
                    Your role <strong>{{ $role }}</strong> is not recognized.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
