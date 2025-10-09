@extends('layouts.participant.app')

@section('navbar_title', 'EDUCATION')
@section('navbar_subtitle', 'Educational resources and learning materials')

@section('content')
 <section>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold">EDUCATION VIDEOS</h2>
            <div class="flex gap-2">
                <select class="border border-gray-300 rounded px-3 py-1 text-sm text-white bg-[#7B1C1C]">
                    <option>Category</option>
                </select>
                <select class="border border-gray-300 rounded px-3 py-1 text-sm text-white bg-[#7B1C1C]">
                    <option>Recommended</option>
                </select>
            </div>
        </div>

        <div class="swiper educationSwiper">
           <div class="swiper-wrapper pb-3">

  <!-- Slide 1 - YouTube Video -->
  <div class="swiper-slide">
    <div class="rounded overflow-hidden shadow-md bg-white">
      <div class="aspect-video">
        <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY" allowfullscreen></iframe>
      </div>
      <div class="p-4 text-sm">Learn more about the subject in this video.</div>
    </div>
  </div>

  <!-- Slide 2 - Flip Card -->
  <div class="swiper-slide px-2">
    <div class="group [perspective:1000px]">
      <div class=" card-new-dec relative h-70 w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">

        <!-- Front -->
        <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md backface-hidden  flex flex-col justify-center items-center">
          <div class="p-4 text-sm font-medium">
           <div class="text-[23px] font-bold mb-2">MYTH</div>
           <h4 class="text-[20px]">Taking the anticonception pill without a break is bad</h4>
          </div>
          <div class="absolute top-3 right-3">
            <i class="fas fa-sync-alt text-white opacity-70"></i>
          </div>
        </div>

        <!-- Back -->
        <div class="absolute inset-0 rounded-lg bg-primary text-white text-center p-6 [transform:rotateY(180deg)] backface-hidden flex flex-col justify-center items-center">
          <div class="text-sm font-medium">
           You don’t have to stop taking the pill every 21 days. You can also take the pill continuously, and that’s totally fine. After 2 or 3
            months, you might get some breakthrough bleeding. In that case, it can be helpful to include a seven day pill break. 
          </div>
          <div class="absolute top-3 right-3">
            <i class="fas fa-sync-alt text-white opacity-70"></i>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Slide 3 - YouTube Video -->
  <div class="swiper-slide">
    <div class="rounded overflow-hidden shadow-md bg-white">
      <div class="aspect-video">
        <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw" allowfullscreen></iframe>
      </div>
      <div class="p-4 text-sm">Another educational topic presented here.</div>
    </div>
  </div>

  <!-- Slide 4 - Flip Card -->
  <div class="swiper-slide px-2">
    <div class="group [perspective:1000px]">
      <div class=" card-new-dec relative h-70 w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">

     <!-- Front -->
        <div class="absolute inset-0 bg-[#FC9490] text-white text-center rounded-lg shadow-md backface-hidden  flex flex-col justify-center items-center">
          <div class="p-4 text-sm font-medium">
           <div class="text-[23px] font-bold mb-2">MYTH</div>
           <h4 class="text-[20px]">You can’t exercise when you’re on your period</h4>
          </div>
          <div class="absolute top-3 right-3">
            <i class="fas fa-sync-alt text-white opacity-70"></i>
          </div>
        </div>

        <!-- Back -->
        <div class="absolute inset-0 rounded-lg bg-primary text-white text-center p-6 [transform:rotateY(180deg)] backface-hidden flex flex-col justify-center items-center">
          <div class="text-sm font-medium">
           Exercising can actually help with period symptoms! It can reduce cramps, and your
            body releases feel-good chemicals that can improve your mood. 
          </div>
          <div class="absolute top-3 right-3">
            <i class="fas fa-sync-alt text-white opacity-70"></i>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Slide 4 - YouTube Video -->
  <div class="swiper-slide">
    <div class="rounded overflow-hidden shadow-md bg-white">
      <div class="aspect-video">
        <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw" allowfullscreen></iframe>
      </div>
      <div class="p-4 text-sm">Another educational topic presented here.</div>
    </div>
  </div>
</div>

            <!-- Navigation -->

    </section>

    <!-- VIDEOS -->
    <section class="mt-12">
        <h2 class="text-2xl font-bold mb-4">VIDEOS</h2>
        <div class="swiper videoSwiper">
            <div class="swiper-wrapper pb-3">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/jNQXAC9IVRw"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Another educational topic presented here.</div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="rounded overflow-hidden shadow-md bg-white">
                        <div class="aspect-video">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/tgbNymZ7vqY"
                                allowfullscreen></iframe>
                        </div>
                        <div class="p-4 text-sm">Learn more about the subject in this video.</div>
                    </div>
                </div>
            </div>
            <!-- Navigation -->

    </section>

@endsection
@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            new Swiper('.educationSwiper', {
                slidesPerView: 1.3,
                spaceBetween: 16,
                breakpoints: {
                    1024: {
                        slidesPerView: 2.9,
                    },
                }
            });

            new Swiper('.videoSwiper', {
                slidesPerView: 1.3,
                spaceBetween: 16,
                breakpoints: {
                    1024: {
                        slidesPerView: 2.9,
                    },
                }
            });
        });
    </script>
@endpush
