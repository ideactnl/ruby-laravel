@extends('layouts.admin.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Create New User</h2>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <x-form.group name="name">
            <x-form.label name="name" required>Name</x-form.label>
            <x-form.input name="name" type="text" value="{{ old('name') }}" required />
        </x-form.group>

        <x-form.group name="email">
            <x-form.label name="email" required>Email</x-form.label>
            <x-form.input name="email" type="email" value="{{ old('email') }}" required />
        </x-form.group>

        <x-form.group name="password">
            <x-form.label name="password" required>Password</x-form.label>
            <x-form.input name="password" type="password" required />
        </x-form.group>

        <x-form.group name="password_confirmation">
            <x-form.label name="password_confirmation" required>Confirm Password</x-form.label>
            <x-form.input name="password_confirmation" type="password" required />
        </x-form.group>

        <x-form.group name="role">
            <x-form.label name="role" required>Role</x-form.label>
            <x-form.select name="role" required>
                <option value="">-- Select Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                @endforeach
            </x-form.select>
        </x-form.group>

        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create</button>
    </form>
</div>
@endsection
