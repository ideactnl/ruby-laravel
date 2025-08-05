@extends('layouts.medical-specialist.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        Participant: {{ $participant->registration_number }}
                    </h2>
                    <div class="space-x-4">
                        <form method="POST" action="{{ route('medical-specialist.logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Logout
                            </button>
                        </form>
                        <form method="GET" action="#" class="inline">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Export to PDF
                            </button>
                        </form>
                    </div>
                </div>
                <div class="mb-4">
                    <strong>PIN Expires:</strong>
                    {{ $expiryDate ?? 'N/A' }}
                </div>
                <!-- Example chart placeholder, replace with server-rendered chart or image if needed -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">PBAC Score History</h3>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <!-- Replace this with a chart image or summary as needed -->
                        <p>Chart goes here (implement server-side or use a static image if needed).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection