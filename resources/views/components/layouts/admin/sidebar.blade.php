<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-gray-900">
    <flux:sidebar sticky stashable
        class="border-e border-gray-200 bg-gradient-to-b from-white to-gray-50 shadow-md dark:from-gray-800 dark:to-gray-900 dark:border-gray-700">
        <flux:sidebar.toggle
            class="lg:hidden text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors"
            icon="x-mark" />

        <a href="{{ auth()->user()->hasRole('customer') ? route('landing.home') : route('admin.dashboard') }}"
            class="flex justify-center items-center py-6 mb-2" wire:navigate>
            <div class="flex flex-col items-center text-center">
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-tight mb-1">Hotel</span>
                <span
                    class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent leading-tight">Pondok
                    Putri</span>
            </div>
        </a>

        <div class="mx-3 mb-6 border-b border-gray-200 dark:border-gray-700"></div>

        <flux:navlist variant="outline" class="px-2">
            <!-- Management Group - Hanya untuk admin dan owner -->
            @unlessrole('Pengunjung')
            <flux:navlist.group :heading="__('Manajemen')"
                class="grid space-y-1 font-medium text-gray-500 dark:text-gray-300 text-sm">

                <!-- Dashboard - Hanya admin dan owner -->
                <flux:navlist.item icon="home" :href="route('admin.dashboard')"
                    :current="request()->routeIs('admin.dashboard')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                <!-- Users - Hanya admin dan owner -->
                @can('mengelola user')
                <flux:navlist.item icon="users" :href="route('admin.users.index')"
                    :current="request()->routeIs('admin.users.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Pengguna') }}
                </flux:navlist.item>
                @endcan

                <!-- Pelanggan - Hanya admin dan owner -->
                @can('mengelola pelanggan')
                <flux:navlist.item icon="user-group" :href="route('admin.pelanggan.index')"
                    :current="request()->routeIs('admin.pelanggan.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.pelanggan.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Pelanggan') }}
                </flux:navlist.item>
                @endcan

                <!-- Roles - Hanya admin dan owner -->
                @can('mengelola role')
                <flux:navlist.item icon="shield-check" :href="route('admin.roles.index')"
                    :current="request()->routeIs('admin.roles.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.roles.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Roles') }}
                </flux:navlist.item>
                @endcan

                @canany(['mengelola tipe dan fasilitas kamar', 'mengelola ketersediaan kamar', 'mengelola galeri
                kamar'])
                <flux:navlist.item icon="adjustments-horizontal" :href="route('admin.fasilitas-kamar.index')"
                    :current="request()->routeIs('admin.fasilitas-kamar.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.tipe-dan-fasilitas-kamar.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Fasilitas Kamar') }}
                </flux:navlist.item>

                <flux:navlist.item icon="queue-list" :href="route('admin.tipe-kamar.index')"
                    :current="request()->routeIs('admin.tipe-kamar.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.tipe-kamar.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Tipe Kamar') }}
                </flux:navlist.item>
                @endcan

                @can('mengelola kamar')
                <flux:navlist.item icon="bell-snooze" :href="route('admin.kamar.index')"
                    :current="request()->routeIs('admin.kamar.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.kamar.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Kamar') }}
                </flux:navlist.item>
                @endcan

                @can('mengelola reservasi')
                <flux:navlist.item icon="calendar" :href="route('admin.reservasi.index')"
                    :current="request()->routeIs('admin.reservasi.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.reservasi.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Reservasi') }}
                </flux:navlist.item>
                @endcan

                <!-- Verifikasi Pembayaran - Always visible for testing -->
                <flux:navlist.item icon="credit-card" :href="route('admin.verifikasi-pembayaran')"
                    :current="request()->routeIs('admin.verifikasi-pembayaran')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.verifikasi-pembayaran') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Verifikasi Pembayaran') }}
                </flux:navlist.item>

            </flux:navlist.group>
            @else
            <!-- Settings Group - Semua role bisa akses -->
            <flux:navlist.group :heading="__('Pengaturan')"
                class="grid space-y-1 font-medium text-gray-500 dark:text-gray-300 text-sm {{ auth()->user()->hasRole('customer') ? '' : 'mt-6' }}">

                <!-- Settings - Semua role bisa akses -->
                <flux:navlist.item icon="cog" :href="route('admin.settings.index')"
                    :current="request()->routeIs('admin.settings.*')"
                    class="rounded-lg transition-colors hover:bg-blue-50 dark:hover:bg-blue-900/20 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-100' : 'dark:text-gray-300' }}"
                    wire:navigate>
                    {{ __('Settings') }}
                </flux:navlist.item>

            </flux:navlist.group>
            @endunlessrole
        </flux:navlist>

        <flux:spacer />

        <div class="mx-3 mt-4 mb-3 border-b border-gray-200 dark:border-gray-700"></div>

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block mb-6 mx-3" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down"
                class="w-full p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" />

            <flux:menu class="w-[280px] rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-3 px-3 py-3 text-start">
                            <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-full bg-blue-100 text-blue-700 font-semibold dark:bg-blue-900 dark:text-blue-200">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start leading-tight">
                                <span class="truncate font-semibold text-gray-900 dark:text-gray-100">{{
                                    auth()->user()->name }}</span>
                                <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email
                                    }}</span>
                                <span class="truncate text-xs text-blue-600 dark:text-blue-400">
                                    {{ auth()->user()->roles->first() ? ucfirst(auth()->user()->roles->first()->name) :
                                    'User' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator class="my-1" />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('admin.settings.index')" icon="cog" wire:navigate
                        class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md mx-1 my-0.5">
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator class="my-1" />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full transition-colors hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-700 dark:hover:text-red-400 rounded-md mx-1 my-0.5">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header
        class="lg:hidden shadow-md bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700">
        <flux:sidebar.toggle
            class="lg:hidden text-gray-600 hover:text-blue-700 dark:text-gray-300 dark:hover:text-white transition-colors"
            icon="bars-2" inset="left" />

        <div class="flex items-center">
            <div class="flex flex-col items-center text-center">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-tight mb-0.5">Hotel</span>
                <span
                    class="text-sm font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent leading-tight">Pondok
                    Putri</span>
            </div>
        </div>

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down"
                class="rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" />

            <flux:menu class="w-[280px] rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-3 px-3 py-3 text-start">
                            <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-full bg-blue-100 text-blue-700 font-semibold dark:bg-blue-900 dark:text-blue-200">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start leading-tight">
                                <span class="truncate font-semibold text-gray-900 dark:text-gray-100">{{
                                    auth()->user()->name }}</span>
                                <span class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email
                                    }}</span>
                                <span class="truncate text-xs text-blue-600 dark:text-blue-400">
                                    {{ auth()->user()->roles->first() ? ucfirst(auth()->user()->roles->first()->name) :
                                    'User' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator class="my-1" />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('admin.settings.index')" icon="cog" wire:navigate
                        class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md mx-1 my-0.5">
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator class="my-1" />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full transition-colors hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-700 dark:hover:text-red-400 rounded-md mx-1 my-0.5">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:main class="!p-0 lg:!p-0">
        {{ $slot }}
    </flux:main>

    @fluxScripts

    <script src="{{ asset('js/theme-initializer.js') }}"></script>
    <script src="{{ asset('js/theme-manager.js') }}"></script>
    <script src="{{ asset('js/theme-debug.js') }}"></script>
    <script src="{{ asset('js/theme-enforcer.js') }}"></script>

    <style>
        /* Custom styles for sidebar to match Pondok Putri Hotel aesthetic */
        .flux-sidebar {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            width: 240px;
        }

        /* Light mode sidebar styles - only apply when light class is present */
        .light .flux-sidebar {
            background: linear-gradient(150deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%) !important;
            border-right: 1px solid rgba(226, 232, 240, 0.8) !important;
            background-size: 200% 200%;
            animation: gradientAnimation 15s ease infinite;
        }

        /* Dark mode sidebar styles - only apply when dark class is present */
        .dark .flux-sidebar {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            background: linear-gradient(150deg, #1f2937 0%, #111827 50%, #0f172a 100%) !important;
            border-right: 1px solid rgba(59, 130, 246, 0.2) !important;
            background-size: 200% 200%;
            animation: gradientAnimation 15s ease infinite;
        }

        /* Navigation item hover and active states */
        .flux-navlist-item {
            border-radius: 8px;
            transition: all 0.2s ease;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .flux-navlist-item:hover {
            transform: translateX(2px);
        }

        .flux-navlist-item-current {
            font-weight: 500;
        }

        .flux-navlist-group-heading {
            font-size: 0.85rem;
            letter-spacing: 0.03em;
            padding-left: 0.5rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        /* User profile styles */
        .flux-profile {
            transition: all 0.2s ease;
        }

        .flux-profile:hover {
            transform: translateY(-1px);
        }

        /* Menu styling */
        .flux-menu {
            border-radius: 12px;
            overflow: hidden;
        }

        /* Mobile header styling */
        .flux-header {
            padding: 0.75rem 1rem;
            height: 60px;
        }

        /* Icon styles */
        .flux-icon {
            opacity: 0.85;
        }

        .flux-navlist-item:hover .flux-icon {
            opacity: 1;
            color: #2563eb;
        }

        .flux-navlist-item-current .flux-icon {
            opacity: 1;
        }

        /* Active item indicator */
        .flux-navlist-item-current::before {
            content: '';
            position: absolute;
            left: -0.25rem;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 3px;
            background: #2563eb;
            border-radius: 0 3px 3px 0;
        }

        /* Light mode specific styles */
        .light .flux-navlist-item:hover .flux-icon {
            color: #1d4ed8;
        }

        .light .flux-navlist-item-current::before {
            background: #1d4ed8;
        }

        .light .flux-navlist-item-current {
            background: rgba(29, 78, 216, 0.1);
            color: #1e40af;
        }

        /* Dark mode specific styles */
        .dark .flux-navlist-item:hover .flux-icon {
            color: #3b82f6;
        }

        .dark .flux-navlist-item-current::before {
            background: #3b82f6;
        }

        .dark .flux-navlist-item-current {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
        }

        /* Custom glow effect for active items */
        .light .flux-navlist-item-current {
            box-shadow: 0 0 15px rgba(29, 78, 216, 0.1);
        }

        .dark .flux-navlist-item-current {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.1);
        }

        /* Animated gradient background for both themes */
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</body>

</html>