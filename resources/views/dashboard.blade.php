@extends('layouts.admin.app')

@section('navbar_title', 'Admin Console')
@section('navbar_subtitle', 'Administrative area')

@section('content')
@php
    $user = Auth::user();
    $role = $user?->getRoleNames()->first();
    $totalUsers = \App\Models\User::count();
    $researchers = \App\Models\User::role('researcher')->count();
    $logsCount = \Illuminate\Support\Facades\DB::table('activity_log')
    ->whereJsonContains('properties->event', 'completed')
    ->orWhereJsonContains('properties->event', 'failed')
    ->count();
@endphp

<div class="px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white shadow rounded-2xl p-6">
            <h1 class="text-xl font-semibold mb-1">Welcome, {{ $user?->name }}!</h1>
            <p class="text-sm text-gray-600">You are logged in as <span class="font-medium">{{ ucfirst($role ?? 'user') }}</span>.</p>

            @if ($role === 'superadmin')
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-gray-500">Total Users</div>
                        <div class="mt-1 text-2xl font-semibold">{{ number_format($totalUsers) }}</div>
                        <a href="{{ route('users.index') }}" class="text-[#5E0F0F] text-xs mt-2 inline-flex items-center gap-1 hover:underline">
                            Manage users
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-gray-500">Researchers</div>
                        <div class="mt-1 text-2xl font-semibold">{{ number_format($researchers) }}</div>
                        <a href="{{ route('pbac.export.form') }}" class="text-[#5E0F0F] text-xs mt-2 inline-flex items-center gap-1 hover:underline">
                            Export data
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-gray-500">PBAC Logs</div>
                        <div class="mt-1 text-2xl font-semibold">{{ number_format($logsCount) }}</div>
                        <a href="{{ route('logs') }}" class="text-[#5E0F0F] text-xs mt-2 inline-flex items-center gap-1 hover:underline">
                            View logs
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pbac.export.form') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-sm">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        Queue export
                    </a>
                    <a href="{{ route('logs') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-sm ml-2">
                        <i class="fa-solid fa-clipboard-list"></i>
                        Logs
                    </a>
                </div>
            @elseif ($role === 'researcher')
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-xl border p-4">
                        <div class="text-xs text-gray-500">Export</div>
                        <div class="mt-1 text-sm text-gray-600">Queue PBAC dataset exports</div>
                        <a href="{{ route('pbac.export.form') }}" class="mt-2 inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            Queue export
                        </a>
                    </div>
                </div>
            @else
                <div class="mt-6 text-sm text-gray-600">No widgets are available for your role yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
