@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-lg">
            <!-- Security Badge & Heading -->
            <div class="text-center mb-6">
                <span
                    class="inline-block px-3 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded-full mb-3">
                    STEP 2 OF 2: SERVER AUTHENTICATION
                </span>
                <h1 class="text-xl font-black text-neutral-900 tracking-tight">
                    Secure Database Access
                </h1>
            </div>

            <!-- Verification Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-neutral-200/50 border border-neutral-100 overflow-hidden">
                <div class="p-6 md:p-8">

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

                    <div class="text-center mb-6">
                        <p class="text-xs text-neutral-500 font-semibold">
                            Enter Adminer Server-Level credentials to proceed.
                        </p>
                    </div>

                    <form action="{{ route('admin.server-auth.challenge') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Username Input -->
                        <div>
                            <label for="username"
                                class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2 ml-1">
                                Username
                            </label>
                            <div class="relative group">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400 group-focus-within:text-primary transition-colors">
                                    <i class="fa-solid fa-user text-sm"></i>
                                </span>
                                <input type="text" name="username" id="username" required autofocus
                                    class="w-full h-12 pl-11 pr-4 bg-white border-2 border-neutral-100 rounded-2xl focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-neutral-900 placeholder:text-neutral-300 text-sm"
                                    placeholder="Server username">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password"
                                class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2 ml-1">
                                Password
                            </label>
                            <div class="relative group">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400 group-focus-within:text-primary transition-colors">
                                    <i class="fa-solid fa-key text-sm"></i>
                                </span>
                                <input type="password" name="password" id="password" required
                                    class="w-full h-12 pl-11 pr-4 bg-white border-2 border-neutral-100 rounded-2xl focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none text-neutral-900 placeholder:text-neutral-300 text-sm"
                                    placeholder="••••••••••••">
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button type="submit"
                            class="w-full h-12 bg-primary hover:bg-primary-dark text-white font-bold rounded-2xl shadow-lg shadow-primary/20 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                            <span class="text-sm">Login to Database</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </form>

                    <!-- Back Navigation -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('dashboard') }}"
                            class="group inline-flex items-center gap-2 text-sm font-semibold text-neutral-400 hover:text-primary transition-colors">
                            <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                            <span>Cancel and Return to Dashboard</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
