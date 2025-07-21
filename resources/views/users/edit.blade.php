@extends('layouts.admin.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Edit User</h2>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <x-form.group name="name">
            <x-form.label name="name" required>Name</x-form.label>
            <x-form.input name="name" type="text" value="{{ old('name', $user->name) }}" required />
        </x-form.group>

        <x-form.group name="email">
            <x-form.label name="email" required>Email</x-form.label>
            <x-form.input name="email" type="email" value="{{ old('email', $user->email) }}" required />
        </x-form.group>

        <x-form.group name="password">
            <x-form.label name="password">Password (Leave blank to keep current)</x-form.label>
            <x-form.input name="password" type="password" />
        </x-form.group>

        <x-form.group name="password_confirmation">
            <x-form.label name="password_confirmation">Confirm Password</x-form.label>
            <x-form.input name="password_confirmation" type="password" />
        </x-form.group>

        <x-form.group name="role">
            <x-form.label name="role" required>Role</x-form.label>
            <x-form.select name="role" required>
                <option value="">-- Select Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->roles->first()->name ?? '') === $role)>
                        {{ $role }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.group>

        <div class="flex justify-between items-center">
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>
@endsection
