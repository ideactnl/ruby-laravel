<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'API Documentation' }}</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/scribe/css/custom-laravel-docs.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/scribe/css/theme-default.print.css') }}" media="print">
    @yield('head')
</head>
<body>
    <div class="header">
        <span class="logo">&#123; Laravel API Docs &#125;</span>
    </div>
    <div style="display: flex; min-height: 100vh;">
        <aside class="sidebar">
            @section('sidebar')
                {!! $sidebar !!}
            @show
        </aside>
        <main class="main-content" style="flex:1;">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>
</html>
