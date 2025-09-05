<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    {{ $slot }}

    @fluxScripts

    <script src="{{ asset('js/theme-manager.js') }}"></script>
</body>

</html>