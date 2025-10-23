@extends('layouts.participant.app')

@section('navbar_title', 'EXTERNAL LINKS')
@section('navbar_subtitle', 'Helpful external websites and resources')

@section('content')

<section class="max-w-7xl mx-auto px-4 py-0">
      <!-- Heading -->
      <h2 class="text-[25px] md:text-{25px} font-bold mb-6">WEBSITES LINKS</h2>

      <!-- Image Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Image 1 -->
        <a href="#" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
          <img src="{{ asset("images/Aboutus.jpg") }}" alt="Website 1" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Image 2 -->
        <a href="#" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
          <img src="{{ asset("images/Privacys.png") }}" alt="Website 2" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Image 3 -->
        <a href="#" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
          <img src="{{ asset("images/Aboutus.jpg") }}" alt="Website 3" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Image 4 -->
        <a href="#" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
          <img src="{{ asset("images/Privacys.png") }}" alt="Website 4" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Image 5 -->
        <a href="#" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
          <img src="{{ asset("images/Aboutus.jpg") }}" alt="Website 5" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>

        <!-- Image 6 -->
        <a href="#" target="_blank" class="block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
          <img src="{{ asset("images/Privacys.png") }}" alt="Website 6" class="w-full h-64 object-cover transform hover:scale-105 transition duration-500">
        </a>
      </div>
    </section>

@endsection
