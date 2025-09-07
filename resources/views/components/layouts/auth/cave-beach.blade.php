<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="antialiased">
    <div class="min-h-screen bg-white flex items-center justify-center px-4">
        @if(request()->routeIs('register'))
            <!-- Register page with split layout -->
            <div class="w-full max-w-6xl flex flex-col md:flex-row overflow-hidden rounded-2xl shadow-xl border border-slate-100">
                <!-- Left Section with Image (Register only) -->
                <div class="w-full md:w-1/2 bg-cover bg-center hidden md:block" style="background-image: url('{{ asset('img/image2.png') }}');">
                    <div class="h-full w-full bg-black/20 flex items-center justify-center">
                        <div class="p-8 text-white text-center">
                            <h2 class="text-3xl font-bold mb-4">Welcome to Cave Beach Bungalow</h2>
                            <p class="text-lg">Experience luxury and comfort by the beach</p>
                        </div>
                    </div>
                </div>
                <!-- Right Section (Register Form) -->
                <div class="w-full md:w-1/2 bg-white">
                    <div class="w-full max-w-lg mx-auto p-6 md:p-8 space-y-6">
                        {{ $slot }}
                        
                        <!-- Footer -->
                        <div class="text-center text-sm text-slate-500">
                            <p>&copy; {{ date('Y') }} Cave Beach Bungalow. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Login page with card layout -->
            <div class="w-full max-w-sm md:max-w-md bg-white shadow-xl rounded-2xl border border-slate-100 p-6 md:p-8">
                <div class="space-y-6">
                    <!-- Logo -->
                    <div class="text-center">
                        <img
                            src="{{ asset('img/logo_cavebeach.PNG') }}"
                            alt="Cave Beach Bungalow"
                            class="h-32 md:h-40 w-auto mx-auto mb-4 object-contain"
                        >
                        <h1 class="text-2xl md:text-3xl font-semibold text-slate-800">Cave Beach Bungalow</h1>
                        <p class="mt-2 text-sm text-slate-500">Sign in to your account to continue</p>
                    </div>

                    {{ $slot }}
                    
                    <!-- Footer -->
                    <div class="text-center text-sm text-slate-500">
                        <p>&copy; {{ date('Y') }} Cave Beach Bungalow. All rights reserved.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @fluxScripts

    <!-- Theme Scripts -->
    <script src="{{ asset('js/theme-initializer.js') }}"></script>
    <script src="{{ asset('js/theme-manager.js') }}"></script>
    <script src="{{ asset('js/theme-debug.js') }}"></script>
</body>

</html>
