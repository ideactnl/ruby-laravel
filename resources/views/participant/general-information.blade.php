@extends('layouts.participant.app')

@section('navbar_title', __('participant.general_information'))
@section('navbar_subtitle', __('participant.important_information_guidelines'))

@section('content')
      <!-- WHY THE RUBY APP -->
      <div class="flex flex-col md:flex-row items-center gap-8 py-[50px]">
        <div class="md:w-1/3">
          <img src=" {{ asset("images/app.png") }}" alt="Ruby App" class="rounded-lg w-full object-cover ">
        </div>
        <div class="md:w-2/3">
          <h2 class="text-2xl font-bold mb-4 text-[#000]">{{ __('participant.why_the_ruby_app') }}</h2>
          <p class="text-gray-700 leading-relaxed mb-4">
            Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin
            literature from 45 BC, making it over 2000 years old.
          </p>

          <div class="space-y-5">
          <!-- Myth 1 -->
          <div class="flex items-start gap-3">
             <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: MENSTRUATION IS DIRTY OR IMPURE.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Menstrual blood is clean in any biological sense.
                The body’s process is natural.
              </p>
            </div>
          </div>

          <!-- Myth 2 -->
          <div class="flex items-start gap-3">
            <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: WOMEN SHOULDN’T EXERCISE DURING PERIODS.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Light exercise, yoga, or walking can actually reduce
                cramps and boost mood.
              </p>
            </div>
          </div>
          </div>
        </div>
      </div>

      <!-- ABOUT THE APP -->
      <div class="flex flex-col md:flex-row-reverse items-center gap-8  py-[50px]">
        <div class="md:w-1/3">
          <img src="{{ asset("images/Aboutus.jpg") }}" alt="About the App" class="rounded-lg w-full object-cover ">
        </div>
        <div class="md:w-2/3">
          <h2 class="text-2xl font-bold mb-4 text-[#000]">{{ __('participant.about_the_app') }}</h2>
          <p class="text-gray-700 leading-relaxed mb-4">
            Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin
            literature from 45 BC, making it over 2000 years old.
          </p>


          <div class="space-y-5">
          <!-- Myth 1 -->
          <div class="flex items-start gap-3">
             <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: MENSTRUATION IS DIRTY OR IMPURE.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Menstrual blood is clean in any biological sense.
                The body’s process is natural.
              </p>
            </div>
          </div>

          <!-- Myth 2 -->
          <div class="flex items-start gap-3">
            <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: WOMEN SHOULDN’T EXERCISE DURING PERIODS.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Light exercise, yoga, or walking can actually reduce
                cramps and boost mood.
              </p>
            </div>
          </div>
          </div>
        </div>
      </div>

      <!-- ABOUT THE RESEARCH -->
      <div class="flex flex-col md:flex-row items-center gap-8 py-[50px]" >
        <div class="md:w-1/3">
          <img src="{{ asset("images/research.png") }}" alt="About the Research" class="rounded-lg w-full object-cover ">
        </div>
        <div class="md:w-2/3">
          <h2 class="text-2xl font-bold mb-4 text-[#000]">{{ __('participant.about_the_research') }}</h2>
          <p class="text-gray-700 leading-relaxed mb-4">
            Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin
            literature from 45 BC, making it over 2000 years old.
          </p>


          <div class="space-y-5">
          <!-- Myth 1 -->
          <div class="flex items-start gap-3">
             <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: MENSTRUATION IS DIRTY OR IMPURE.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Menstrual blood is clean in any biological sense.
                The body’s process is natural.
              </p>
            </div>
          </div>

          <!-- Myth 2 -->
          <div class="flex items-start gap-3">
            <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: WOMEN SHOULDN’T EXERCISE DURING PERIODS.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Light exercise, yoga, or walking can actually reduce
                cramps and boost mood.
              </p>
            </div>
          </div>
          </div>
        </div>
      </div>

      <!-- PRIVACY INFORMATION -->
      <div class="flex flex-col md:flex-row-reverse items-center gap-8">
        <div class="md:w-1/3">
          <img src="{{ asset("images/Privacys.png") }}" alt="Privacy Info" class="rounded-lg w-full object-cover">
        </div>
        <div class="md:w-2/3">
          <h2 class="text-2xl font-bold mb-4 text-[#000]">{{ __('participant.privacy_information') }}</h2>
          <p class="text-gray-700 leading-relaxed mb-4">
            Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin
            literature from 45 BC, making it over 2000 years old.
          </p>


          <div class="space-y-5">
          <!-- Myth 1 -->
          <div class="flex items-start gap-3">
             <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: MENSTRUATION IS DIRTY OR IMPURE.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Menstrual blood is clean in any biological sense.
                The body’s process is natural.
              </p>
            </div>
          </div>

          <!-- Myth 2 -->
          <div class="flex items-start gap-3">
            <i class="fas fa-check-square  text-[#520606] mt-1 text-[30px]"></i>
            <div>
              <p class="text-[#520606] font-semibold">MYTH: WOMEN SHOULDN’T EXERCISE DURING PERIODS.</p>
              <p class="text-gray-700">
                <span class="font-semibold">Fact:</span> Light exercise, yoga, or walking can actually reduce
                cramps and boost mood.
              </p>
            </div>
          </div>
          </div>
        </div>
      </div>
      <section class="max-w-6xl mx-auto px-4 py-16">
      <!-- Heading -->
      <div class="mb-6">
        <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ __('participant.send_us_message') }}</h2>
        <p class="text-gray-700">Contrary to popular belief, Lorem Ipsum is not simply random text.</p>
      </div>

      <!-- Flex container -->
      <div class="flex flex-col md:flex-row items-start gap-10">
        <!-- Left: Image -->
        <div class="w-full md:w-1/2">
          <img
            src="{{ asset("images/Aboutus.jpg") }}"
            alt="Handshake"
            class="rounded-lg w-full h-full object-cover shadow-md"
          />
        </div>

        <!-- Right: Form -->
        <div class="w-full md:w-1/2">
          <form action="#" method="post" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 msg-send">
              <input
                type="text"
                placeholder="First Name"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#7a1831]"
              />
              <input
                type="text"
                placeholder="Last Name"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#7a1831]"
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 msg-send">
              <input
                type="email"
                placeholder="Email Address"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#7a1831]"
              />
              <input
                type="tel"
                placeholder="Phone Number"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#7a1831]"
              />
            </div>

            <textarea
              rows="4"
              placeholder="Tell us about your requirement*"
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#7a1831]"
            ></textarea>

           <button
  type="submit"
  class="bg-[#7a1831] text-white font-semibold px-8 py-3 rounded-lg cursor-not-allowed opacity-50"
  disabled
>
  Yes I want
</button>

          </form>
        </div>
      </div>
    </section>

@endsection
