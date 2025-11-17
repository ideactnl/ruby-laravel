@extends('layouts.participant.app')

@section('navbar_title', __('participant.general_information'))
@section('navbar_subtitle', __('participant.important_information_guidelines'))

@section('content')
  <!-- WHY THE RUBY APP -->
  <div class="flex flex-col md:flex-row items-center gap-8 py-[50px]">
    <div class="md:w-1/3">
      <img src="{{ asset('images/app.png') }}" alt="Ruby App" class="rounded-lg w-full object-cover">
    </div>
    <div class="md:w-2/3">
      <h2 class="text-2xl font-bold mb-4 text-[#000]">Waarom de Ruby app</h2>
      <p class="text-gray-700 leading-relaxed mb-4">
        Met de Ruby App kan je makkelijk jouw menstruele cyclus en gezondheid bijhouden. Simpel, veilig en ontwikkeld in
        Nederland.
        Veel mensen hebben wel eens last van hun menstruatie. Bijvoorbeeld pijn vlak voor of tijdens de menstruatie of
        heel veel bloedverlies,
        maar ook andere klachten kunnen voorkomen.
      </p>
      <p class="text-gray-700 leading-relaxed mb-4">
        Wil je beter begrijpen welke klachten jij hebt, hoe vaak en wanneer? Dit kan je makkelijk bijhouden in de Ruby
        app. Als je klachten
        bijhoudt kan je er ook makkelijker over praten, vergelijken met andere mensen en beter hulp zoeken voor je
        klachten, als dat nodig is.
      </p>
      <p class="text-gray-700 leading-relaxed mb-4">
        Dit dashboard kan hierbij helpen! Op het dashboard kan je per maand of per dag zien wat je allemaal hebt
        bijgehouden. Ook vind je hier
        filmpjes over menstruatie en wat daarbij komt kijken.
      </p>
    </div>
  </div>

  <!-- ABOUT / OVERVIEW IMAGE -->
  <div class="flex flex-col md:flex-row-reverse items-center gap-8 py-[50px] bg-[#f7e7f9] px-[50px] rounded-[20px]">
    <div class="md:w-1/3">
      <img src="{{ asset('images/calendar.png') }}" alt="Overview" class="rounded-lg w-full object-cover">
    </div>
    <div class="md:w-2/3">
      <h2 class="text-2xl font-bold mb-4 text-[#000]">Over de app</h2>
      <p class="text-gray-700 leading-relaxed mb-4 w-[90%]">
        De Ruby app is in Nederland ontwikkeld volgens Europese privacy wetgeving. De app en het dashboard is alleen een
        tracker en geeft geen
        medisch advies. Voor medisch advies, raadpleeg je arts.
      </p>
    </div>
  </div>


@endsection