@extends('layouts.admin.app')

@section('navbar_title', 'Users')
@section('navbar_subtitle', 'Edit user')

@section('content')
<div class="max-w-3xl w-full mr-auto bg-white shadow rounded-2xl">
    <form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-4">
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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form.group name="password">
                <x-form.label name="password">Password <span class="text-xs text-gray-500">(leave blank to keep current)</span></x-form.label>
                <x-form.input name="password" type="password" />
            </x-form.group>
            <x-form.group name="password_confirmation">
                <x-form.label name="password_confirmation">Confirm Password</x-form.label>
                <x-form.input name="password_confirmation" type="password" />
            </x-form.group>
        </div>

        <x-form.group name="role">
            <x-form.label name="role" required>Role</x-form.label>
            <x-form.select name="role" required>
                <option value="">-- Select Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $user->roles->first()->name ?? '') === $role)>
                        {{ ucfirst($role) }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.group>

        <div class="pt-2 flex items-center gap-2">
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#5E0F0F] text-white rounded-xl shadow-sm hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-[#5E0F0F]/30 cursor-pointer">Update</button>
            <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl border text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
