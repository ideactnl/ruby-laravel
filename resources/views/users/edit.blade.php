@extends('layouts.admin.app')

@section('navbar_title', 'ADMIN CONSOLE - UPDATE USER')
@section('navbar_subtitle', 'Edit user details')

@section('content')
<div class="max-w-3xl w-full mr-auto bg-white shadow rounded-xl">
    <form method="POST" action="{{ route('users.update', $user) }}" class="p-6 space-y-4" autocomplete="off">
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

        <input type="password" name="fake_password" style="display: none;" autocomplete="off" />
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form.group name="password">
                <x-form.label name="password">Password <span class="text-xs text-gray-500">(leave blank to keep current)</span></x-form.label>
                <x-form.input name="password" type="password" autocomplete="new-password" value="" />
            </x-form.group>
            <x-form.group name="password_confirmation">
                <x-form.label name="password_confirmation">Confirm Password</x-form.label>
                <x-form.input name="password_confirmation" type="password" autocomplete="new-password" value="" />
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

        <div class="pt-6 flex items-center gap-4">
            <button type="submit" class="rounded-md bg-primary border border-primary px-4 py-2 text-md font-semibold text-white shadow hover:bg-primary-800 hover:border-primary-800 inline-flex items-center gap-2 cursor-pointer transition-colors">Update</button>
            <a href="{{ route('users.index') }}" class="rounded-md bg-white hover:bg-primary/5 border border-primary/30 text-primary px-4 py-2 text-md font-semibold shadow hover:opacity-90 inline-flex items-center gap-2 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
