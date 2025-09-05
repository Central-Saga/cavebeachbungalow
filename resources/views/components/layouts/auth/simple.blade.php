<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body
    class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900/20 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-2xl flex-col gap-2">
            <!-- Logo and Brand -->
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 mb-6" wire:navigate>
                <div
                    class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl shadow-lg">
                    <span class="text-2xl font-bold text-white">PP</span>
                </div>
                <div class="text-center">
                    <h1
                        class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        Pondok Putri
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Hotel Management System</p>
                </div>
            </a>

            <!-- Auth Content -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
                <p>&copy; {{ date('Y') }} Pondok Putri Hotel. All rights reserved.</p>
            </div>
        </div>
    </div>

    @fluxScripts

    <!-- Theme Scripts -->
    <script src="{{ asset('js/theme-initializer.js') }}"></script>
    <script src="{{ asset('js/theme-manager.js') }}"></script>
    <script src="{{ asset('js/theme-debug.js') }}"></script>
</body>

</html>
