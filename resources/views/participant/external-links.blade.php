@extends('layouts.participant.app')

@section('navbar_title', __('participant.links_external_websites'))
@section('navbar_subtitle', __('participant.helpful_external_websites_resources'))

@section('content')

<section class="max-w-7xl mx-auto px-0 py-0">

    <!-- Image Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Thuisarts -->
        <a href="https://thuisarts.nl" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/thuisarts.png') }}" alt="Thuisarts" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- De Gynaecoloog -->
        <a href="https://degynaecoloog.nl" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/degynaecoloog.png') }}" alt="De Gynaecoloog" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Endometriose -->
        <a href="https://endometriose.nl" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/endometriose.png') }}" alt="Endometriose" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Ongewoon Ongesteld -->
        <a href="https://ongewoonongesteld.nl" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/ongewoon-gesteld.png') }}" alt="Ongewoon Ongesteld" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- PMDD Nederland -->
        <a href="https://www.pmddnederland.nl" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/pmdd.png') }}" alt="PMDD Nederland" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- NHG / Dysmenorroe -->
        <a href="https://richtlijnen.nhg.org/behandelrichtlijnen/dysmenorroe" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/nhg.png') }}" alt="NGH Dysmenorroe" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Twijfeltelefoon -->
        <a href="https://twijfeltelefoon.nl/ik-twijfel-over-een-medisch-onderwerp/anticonceptie" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/twijfeltelefoon.png') }}" alt="Twijfeltelefoon" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Period.nl -->
        <a href="http://period.nl" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition duration-300">
            <img src="{{ asset('images/period.png') }}" alt="Period" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

    </div>
</section>

@endsection
