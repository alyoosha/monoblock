<!DOCTYPE html>
<html lang="ru">
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="HandheldFriendly" content="true"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <meta name="theme-color" content="#ffffff"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}"/>
</head>
<body>

<div id="app">
    <div class="container container_small">

    @include('parts.header')

    @yield('content')

    @include('parts.modals')
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush

@stack('scripts')
</body>
</html>
