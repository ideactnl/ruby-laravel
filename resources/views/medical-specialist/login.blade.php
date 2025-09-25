@extends('layouts.medical-specialist.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-white p-4">
    <div class="w-full max-w-4xl bg-white rounded-lg overflow-hidden flex flex-col md:flex-row shadow-[0_5px_15px_rgba(0,0,0,0.35)]">
        
        <!-- Left Section -->
        <div class="w-full md:w-1/2 bg-primary text-white flex flex-col justify-center items-center p-8 md:p-10">
            <div class="flex flex-col items-center text-center">
                <!-- Logo -->
                <div class="bg-white rounded-full p-4 mb-6">
                    <img src="/images/ruby-new-logo.png" alt="Ruby Logo" class="w-16 h-16 md:w-20 md:h-20">
                </div>
                <h2 class="text-xl font-semibold md:text-2xl">Ruby Medical Portal</h2>
                <h3 class="text-2xl font-bold mt-2 md:text-3xl">Secure Access</h3>
                <p class="mt-4 text-sm md:text-base leading-relaxed">
                    Authorized medical professionals can securely access and export patient data with temporary PIN-based authentication.
                </p>
                <div class="mt-6 text-xs md:text-sm opacity-90">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-shield-alt"></i>
                        <span>24-hour access window</span>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-lock"></i>
                        <span>PIN-protected authentication</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-md"></i>
                        <span>Medical professional access only</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center">
            <h2 class="text-2xl font-bold mb-0 text-left uppercase md:text-3xl">Medical Specialist</h2>
            <p class="mt-2 text-sm mb-6 leading-relaxed md:text-base">
                Enter your credentials to access patient data.
            </p>

            @if (session('error') || $errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    {{ session('error') ?: $errors->first() }}
                </div>
            @endif

            <form action="{{ route('medical-specialist.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                
                <x-form.group name="registration_number">
                    <x-form.label name="registration_number" required>Patient Registration Number</x-form.label>
                    <x-form.input 
                        name="registration_number" 
                        type="text" 
                        required 
                        autofocus 
                        value="{{ old('registration_number') }}"
                        placeholder="Enter patient's registration number" />
                </x-form.group>

                <x-form.group name="pin">
                    <x-form.label name="pin" required>Medical Specialist PIN</x-form.label>
                    <x-form.input 
                        name="pin" 
                        type="password" 
                        placeholder="Enter your 4-6 digit PIN" />
                </x-form.group>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-800 text-white font-semibold py-2 px-4 rounded transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Access Patient Data
                    </button>
                </div>
                    <p>Access is limited to 24 hours from PIN generation.</p>
                    <p>Contact the patient to renew expired access.</p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
