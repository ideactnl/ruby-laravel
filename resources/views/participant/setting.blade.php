@extends('layouts.participant.app')

@php
    $currentLocale = app()->getLocale();
    $routePrefix = $currentLocale !== config('app.locale') ? $currentLocale . '.' : '';
@endphp

@section('content')
<div class="min-h-screen pb-24">

    <!-- HEADER -->
    <div class="sticky top-0 z-10 py-4 text-center text-base  text-[20px]  font-[700]">
        {{ __('participant.more') }}
    </div>


    <x-participant.setting-item
        icon="fa-lightbulb"
        label="{{ __('participant.selfmanagement') }}"
        href="{{ route($routePrefix.'participant.self-management') }}"
    />

    <x-participant.setting-item
        icon="fa-external-link-alt"
        label="{{ __('participant.links_external_websites') }}"
        href="{{ route($routePrefix.'participant.external-links') }}"
    />

    <x-participant.setting-item
        icon="fa-share-from-square"
        label="{{ __('participant.export') }}"
        href="{{ route($routePrefix.'participant.export') }}"
    />

    <x-participant.setting-item
        icon="fa-circle-info"
        label="{{ __('participant.general_information') }}"
        href="{{ route($routePrefix.'participant.general-information') }}"
    />



    <x-participant.setting-item
        icon="fa-user-circle"
        label="{{ __('participant.Profile') }}"
        href="#"
        onclick="window.dispatchEvent(new CustomEvent('profile:open'))"
    />

    <x-participant.setting-item
        icon="fa-right-from-bracket"
        label="{{ __('participant.Logout') }}"
        danger
        href="#"
        onclick="
            (async () => {
                try {
                    await fetch('/sanctum/csrf-cookie', { credentials: 'include' });
                    const token = decodeURIComponent(
                        (document.cookie.split('; ').find(c => c.startsWith('XSRF-TOKEN=')) || '')
                        .split('=')[1] || ''
                    );
                    await fetch('{{ url('/api/v1/participant/logout') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-XSRF-TOKEN': token
                        },
                        credentials: 'include'
                    });
                } catch (e) {}
                window.location.href = '/';
            })();
        "
    />

</div>
@endsection
