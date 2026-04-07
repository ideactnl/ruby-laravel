@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-lg">
            <!-- Security Badge & Heading -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-black text-neutral-900 tracking-tight mb-1">
                    Secure Database Access
                </h1>

            </div>

            <!-- Verification Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-neutral-200/50 border border-neutral-100 overflow-hidden">
                <div class="p-8 md:p-12">

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
                            <ul class="text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="text-center mb-8">
                        <p class="text-neutral-600 leading-relaxed">
                            You are about to enter a highly sensitive area. For your protection, please confirm your account
                            password.
                        </p>
                    </div>

                    <form action="{{ route('admin.db-verify.challenge') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- User Context -->
                        <div
                            class="flex items-center justify-center gap-3 py-3 px-4 bg-neutral-50 rounded-2xl border border-neutral-100">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-neutral-700">{{ auth()->user()->email }}</span>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password"
                                class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2 ml-1">
                                Confirm Password
                            </label>
                            <div class="relative group">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400 group-focus-within:text-primary transition-colors">
                                    <i class="fa-solid fa-key text-sm"></i>
                                </span>
                                <input type="password" name="password" id="password" required autofocus
                                    class="w-full h-14 pl-11 pr-4 bg-white border-2 border-neutral-100 rounded-2xl focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-neutral-900 placeholder:text-neutral-300"
                                    placeholder="••••••••••••">
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button type="submit"
                            class="w-full h-14 bg-primary hover:bg-primary-dark text-white font-bold rounded-2xl shadow-lg shadow-primary/20 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                            <span>Open Secure Vault</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </form>

                    <!-- Back Navigation -->
                    <div class="mt-10 text-center">
                        <a href="{{ route('dashboard') }}"
                            class="group inline-flex items-center gap-2 text-sm font-semibold text-neutral-400 hover:text-primary transition-colors">
                            <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                            <span>Return to Safety</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
