@extends('layouts.admin.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Users</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New User</a>
    </div>

    @foreach ($users as $user)
        <div class="bg-white p-4 rounded shadow mb-4 flex justify-between items-center">
            <div>
                <p class="font-semibold">{{ $user->name }}</p>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                <p class="text-sm text-gray-500">Role: {{ $user->roles->pluck('name')->join(', ') }}</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
