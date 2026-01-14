@extends('layouts.participant.app')
@section('navbar_title', __('participant.visualise_symptoms_monthly_overview'))
@section('navbar_subtitle', __('participant.monthly_overview_showing_relationship'))

@section('content')
    <div x-data="filterMenu()" x-init="init()">
        <div class="mb-10 flex items-center justify-between main-hed">
            <!-- Mobile: Date with Navigation -->

            <div class="md:hidden flex items-center justify-center gap-2 flex-1 min-w-0">
                <div class="flex items-center justify-between w-full gap-6 mt-4">

                    <!-- PREV -->
                <button id="btn-prev-month" aria-label="Previous Month">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div id="mobile-date-container"
                        onclick="goToCurrentMonth()"
                        title="{{ __('participant.tap_to_go_current_month') }}"
                        class="flex md:hidden items-center justify-center cursor-pointer rounded-lg px-4 py-2 hover:bg-gray-100 transition text-gray-900 text-[18px] font-normal">
                    <span id="mobile-month">{{ now()->format('F') }}</span>
                    <span class="mx-1">-</span>
                    <span id="mobile-year">{{ now()->format('Y') }}</span>
                </div>

                    <!-- NEXT -->
                <button id="btn-next-month" aria-label="Next Month">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                </div>
            </div>


            <!-- Desktop: Month label -->
            <h2 id="cal-month-label" x-cloak
                class="hidden md:block text-xl sm:text-2xl font-extrabold tracking-tight text-gray-900">&nbsp;
            </h2>

            <!-- Desktop: Navigation Buttons -->
            <div class="hidden md:flex items-center gap-2">
                <button id="btn-prev-month-desktop"
                    class="w-40 px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary-800 transition-colors whitespace-nowrap"
                    title="{{ __('participant.previous_month') }}" aria-label="{{ __('participant.previous_month') }}">
                    <i class="fa-solid fa-angles-left"></i> {{ __('participant.previous_month_btn') }}
                </button>
                <button id="btn-next-month-desktop"
                    class="w-40 px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-primary-800 transition-colors whitespace-nowrap"
                    title="{{ __('participant.next_month') }}" aria-label="{{ __('participant.next_month') }}">
                    {{ __('participant.next_month_btn') }} <i class="fa-solid fa-angles-right"></i>
                </button>
            </div>
            <!-- Domain Filter Dropdown -->
                <x-participant.domain-dropdown :className="'hidden md:flex'"/>
        </div>

        <div class="relative">
            <div id="participantCalendar" class="p-0"></div>
        </div>

        <!-- Back to current month button -->
        <div class="mt-4 flex justify-end">
            <button id="btn-back-current" title="{{ __('participant.back_to_current_month') }}" aria-label="{{ __('participant.back_to_current_month') }}" x-cloak
                class="hidden inline-flex items-center gap-2 rounded-full bg-primary text-white shadow-lg hover:bg-primary-800 px-4 py-3 cursor-pointer transition-colors">
                <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                <span class="text-sm font-medium">{{ __('participant.back_to_current_month') }}</span>
            </button>
        </div>
    </div>
@endsection

@include('participant.partials.calendar-translations')
