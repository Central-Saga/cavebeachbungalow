<?php

use function Livewire\Volt\{layout, title, state, mount};

layout('components.layouts.admin');
title('Dashboard');

state([
    'totalKamar' => 0,
    'totalReservasi' => 0,
    'totalPelanggan' => 0,
    'pendapatanHariIni' => 0,
    'reservasiTerbaru' => [],
    'kamarTersedia' => 0,
    'aktivitasTerbaru' => []
]);

mount(function () {
    // Get dashboard data
    $this->totalKamar = \App\Models\Kamar::count();
    $this->totalReservasi = \App\Models\Reservasi::count();
    $this->totalPelanggan = \App\Models\Pelanggan::count();

    // Get today's revenue
    $this->pendapatanHariIni = \App\Models\Reservasi::whereDate('created_at', today())
        ->sum('total_harga') ?? 0;

    // Get latest reservations
    $this->reservasiTerbaru = \App\Models\Reservasi::with(['pelanggan', 'kamar'])
        ->latest()
        ->take(5)
        ->get()
        ->toArray();

    // Get available rooms
    $this->kamarTersedia = \App\Models\Kamar::where('status', 'tersedia')->count();

    // Get recent activities
    $this->aktivitasTerbaru = \App\Models\ActivityLog::with('causer')
        ->recent(5)
        ->get()
        ->toArray();
});
?>

<div
    class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900/20 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                {{ __('Selamat Datang') }}, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                {{ __('Kelola semuanya Disini') }}
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Kamar -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Kamar') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalKamar }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                        {{ $kamarTersedia }} {{ __('tersedia') }}
                    </span>
                </div>
            </div>

            <!-- Total Reservasi -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Reservasi') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalReservasi }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                        {{ __('Aktif') }}
                    </span>
                </div>
            </div>

            <!-- Total Pelanggan -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Pelanggan') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPelanggan }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">
                        {{ __('Terdaftar') }}
                    </span>
                </div>
            </div>

            <!-- Pendapatan Hari Ini -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Pendapatan Hari Ini') }}
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">
                        {{ __('Hari ini') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Reservasi Terbaru -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Reservasi Terbaru') }}</h3>
                    <a href="{{ route('admin.reservasi.index') }}"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('Lihat Semua') }}
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($reservasiTerbaru as $reservasi)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    {{ substr($reservasi['pelanggan']['nama'] ?? 'N/A', 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $reservasi['pelanggan']['nama'] ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $reservasi['kamar']['nomor_kamar'] ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($reservasi['total_harga'] ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($reservasi['created_at'])->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada reservasi') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('Aksi Cepat') }}</h3>

                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.reservasi.create') }}"
                        class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ __('Buat Reservasi')
                            }}</span>
                    </a>



                    <a href="{{ route('admin.kamar.index') }}"
                        class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <div
                            class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-purple-700 dark:text-purple-300">{{ __('Kelola Kamar')
                            }}</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                        class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div
                            class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Pengaturan') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Aktivitas Terbaru') }}</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Real-time') }}</span>
            </div>

            <div class="space-y-4">
                @forelse($aktivitasTerbaru as $aktivitas)
                <div class="flex items-center space-x-4 p-4 rounded-xl transition-all duration-200 hover:scale-[1.02]"
                    :class="'{{ $aktivitas['event'] }}' === 'created' ? 'bg-green-50 dark:bg-green-900/20' :
                           '{{ $aktivitas['event'] }}' === 'updated' ? 'bg-blue-50 dark:bg-blue-900/20' :
                           '{{ $aktivitas['event'] }}' === 'deleted' ? 'bg-red-50 dark:bg-red-900/20' :
                           'bg-gray-50 dark:bg-gray-700/50'">

                    <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="'{{ $aktivitas['event'] }}' === 'created' ? 'bg-green-100 dark:bg-green-900/30' :
                               '{{ $aktivitas['event'] }}' === 'updated' ? 'bg-blue-100 dark:bg-blue-900/30' :
                               '{{ $aktivitas['event'] }}' === 'deleted' ? 'bg-red-100 dark:bg-red-900/30' :
                               'bg-gray-100 dark:bg-gray-700'">

                        @if($aktivitas['event'] === 'created')
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        @elseif($aktivitas['event'] === 'updated')
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        @elseif($aktivitas['event'] === 'deleted')
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        @else
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @endif
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $aktivitas['description'] ?? 'Aktivitas sistem' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $aktivitas['causer']['name'] ?? 'System' }} • {{ $aktivitas['log_name'] ?? 'General' }}
                        </p>
                    </div>

                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ \Carbon\Carbon::parse($aktivitas['created_at'])->diffForHumans() }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada aktivitas') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>