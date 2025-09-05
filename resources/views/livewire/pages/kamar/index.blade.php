<?php

use function Livewire\Volt\{ layout, title, state, mount, with, updated, usesPagination };
use App\Models\Kamar;
use App\Models\TipeKamar;

layout('components.layouts.admin');
title('Kamar');

// Pagination
usesPagination();

state([
    'search' => '',
    'sortField' => 'nomor_kamar',
    'sortDirection' => 'asc',
    'showDeleteModal' => false,
    'roomToDelete' => null,
    'showDetailModal' => false,
    'selectedRoom' => null,
]);

// Sinkron URL
state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function () {
    // init jika perlu
});

updated([
    'search' => fn () => $this->resetPage(),
]);

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
    $this->resetPage();
};

$viewDetails = function ($roomId) {
    try {
        $k = Kamar::with(['tipeKamar.fasilitasKamars', 'hargas'])->findOrFail($roomId);

        // Ambil harga harian sebagai harga default
        $hargaHarian = $k->hargas()->where('tipe_paket', 'harian')->first();
        $hargaDefault = $hargaHarian ? $hargaHarian->harga : 0;

        $this->selectedRoom = [
            'id' => $k->id,
            'nomor_kamar' => $k->nomor_kamar,
            'status' => $k->status,
            'created_at' => optional($k->created_at)->format('d M Y H:i'),
            'updated_at' => optional($k->updated_at)->format('d M Y H:i'),
            'tipe' => optional($k->tipeKamar)->nama_tipe,
            'fasilitas' => optional($k->tipeKamar)?->fasilitasKamars->pluck('nama_fasilitas')->sort()->values()->toArray() ?? [],
            'hargas' => $k->hargas->map(function($harga) {
                return [
                    'tipe_paket' => $harga->tipe_paket,
                    'harga' => $harga->harga
                ];
            })->toArray(),
        ];
        $this->showDetailModal = true;
    } catch (\Exception $e) {
        session()->flash('error', 'Kamar tidak ditemukan.');
    }
};

$closeDetails = fn () => ($this->showDetailModal = false) && ($this->selectedRoom = null);

$confirmDelete = function ($roomId) {
    try {
        $room = Kamar::findOrFail($roomId);
        $this->roomToDelete = $room;
        $this->showDeleteModal = true;
    } catch (\Exception $e) {
        session()->flash('error', 'Kamar tidak ditemukan.');
    }
};

$deleteRoom = function ($roomId) {
    try {
        $room = Kamar::findOrFail($roomId);
        $room->delete();
        session()->flash('message', 'Kamar berhasil dihapus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus kamar: ' . $e->getMessage());
    }
    $this->resetPage();
    $this->showDeleteModal = false;
    $this->roomToDelete = null;
};

$cancelDelete = fn () => ($this->showDeleteModal = false) && ($this->roomToDelete = null);

with(function () {
    try {
        $query = Kamar::query()->with('tipeKamar');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhereHas('tipeKamar', fn($qq) => $qq->where('nama_tipe', 'like', "%{$search}%"));
            });
        }

        $sortable = ['nomor_kamar', 'status', 'created_at'];
        $sortField = in_array($this->sortField, $sortable) ? $this->sortField : 'nomor_kamar';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $kamars = $query->orderBy($sortField, $sortDirection)->paginate(10);

        // Stats global sederhana (hindari asumsi nilai status spesifik)
        $stats = [
            'totalRooms' => Kamar::count(),
            'withType' => Kamar::has('tipeKamar')->count(),
            'withoutType' => Kamar::doesntHave('tipeKamar')->count(),
            'totalTypes' => TipeKamar::count(),
            'totalHargas' => \App\Models\Harga::count(), // Total paket harga
        ];

        return [
            'rooms' => $kamars,
            'stats' => $stats,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ];
    } catch (\Exception $e) {
        \Log::error('Error loading rooms data', [
            'error' => $e->getMessage(),
        ]);
        return [
            'rooms' => collect(),
            'stats' => [
                'totalRooms' => 0,
                'withType' => 0,
                'withoutType' => 0,
                'totalTypes' => 0,
                'avgPrice' => 0,
            ],
            'sortField' => 'nomor_kamar',
            'sortDirection' => 'asc',
        ];
    }
});

?>

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-3xl p-8 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <flux:icon name="bell-snooze" class="w-8 h-8 text-white" />
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Kelola Kamar</h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Kelola data
                                            kamar & keterkaitannya dengan tipe kamar</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.kamar.create') }}"
                                class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <flux:icon name="plus" class="relative z-10 h-6 w-6" />
                                <span class="relative z-10">Tambah Kamar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <!-- Total Kamar -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-blue-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="bell-snooze" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalRooms'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Kamar</p>
                    </div>
                </div>

                <!-- Kamar dgn Tipe -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-purple-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="check-circle" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['withType'] }}</h3>
                        <p class="text-purple-100 text-xs font-medium">Memiliki Tipe</p>
                    </div>
                </div>

                <!-- Kamar tanpa Tipe -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-emerald-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="exclamation-triangle" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['withoutType'] }}</h3>
                        <p class="text-emerald-100 text-xs font-medium">Tanpa Tipe</p>
                    </div>
                </div>

                <!-- Total Tipe Kamar -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-amber-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-amber-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="queue-list" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalTypes'] }}</h3>
                        <p class="text-amber-100 text-xs font-medium">Total Tipe</p>
                    </div>
                </div>

                <!-- Total Harga -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-500 via-sky-600 to-blue-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-blue-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <flux:icon name="currency-dollar" class="size-8" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['totalHargas'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Paket Harga</p>
                    </div>
                </div>
            </div>

            <!-- Search & Sort -->
            <div class="mb-8">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl p-6 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                            <div class="flex-grow">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <flux:icon name="magnifying-glass" class="h-6 w-6 text-gray-400" />
                                    </div>
                                    <input wire:model.live="search" type="text"
                                        placeholder="Cari kamar (nomor / tipe)..."
                                        class="block w-full pl-14 pr-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium">
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="sortBy('nomor_kamar')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 text-blue-600 dark:text-blue-400 hover:from-blue-100 hover:to-purple-100 dark:hover:from-blue-800/30 dark:hover:to-purple-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="bars-3" class="w-5 h-5" />
                                        Nomor
                                    </span>
                                    @if($sortField === 'nomor_kamar')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('status')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 text-blue-600 dark:text-blue-400 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-800/30 dark:hover:to-indigo-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="shield-check" class="w-5 h-5" />
                                        Status
                                    </span>
                                    @if($sortField === 'status')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                                <button wire:click="sortBy('created_at')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="calendar" class="w-5 h-5" />
                                        Created
                                    </span>
                                    @if($sortField === 'created_at')
                                    <span class="ml-2 text-sm">@if($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
            <div
                class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-lg dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-700/30 dark:text-green-200">
                <div class="flex items-center gap-3">
                    <flux:icon name="check-circle" class="w-6 h-6" />
                    {{ session('message') }}
                </div>
            </div>
            @endif

            @if (session()->has('error'))
            <div
                class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-lg dark:from-red-900/20 dark:to-pink-900/20 dark:border-red-700/30 dark:text-red-200">
                <div class="flex items-center gap-3">
                    <flux:icon name="exclamation-triangle" class="w-6 h-6" />
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Rooms Table -->
            <div class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                </div>
                <div
                    class="relative bg-white/90 dark:bg-gray-800/90 shadow-2xl rounded-3xl overflow-hidden border border-white/30 dark:border-gray-700/50 backdrop-blur-xl">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                            <thead
                                class="bg-gradient-to-r from-gray-50/90 via-blue-50/50 to-purple-50/50 dark:from-gray-700/90 dark:via-blue-900/20 dark:to-purple-900/20">
                                <tr>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Nomor</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Harga</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Dibuat</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white/50 dark:bg-gray-800/50 divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($rooms as $room)
                                <tr
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 dark:hover:from-blue-900/10 dark:hover:to-purple-900/10 transition-all duration-300 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{
                                            $room->nomor_kamar }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{
                                            $room->tipeKamar->nama_tipe ?? '—' }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            @php
                                            $hargaHarian = $room->hargas()->where('tipe_paket', 'harian')->first();
                                            $hargaMingguan = $room->hargas()->where('tipe_paket', 'mingguan')->first();
                                            $hargaBulanan = $room->hargas()->where('tipe_paket', 'bulanan')->first();
                                            @endphp
                                            @if($hargaHarian)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Harian: Rp {{
                                                number_format($hargaHarian->harga, 0, ',', '.') }}</div>
                                            @endif
                                            @if($hargaMingguan)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Mingguan: Rp {{
                                                number_format($hargaMingguan->harga, 0, ',', '.') }}</div>
                                            @endif
                                            @if($hargaBulanan)
                                            <div class="text-xs text-gray-600 dark:text-gray-400">Bulanan: Rp {{
                                                number_format($hargaBulanan->harga, 0, ',', '.') }}</div>
                                            @endif
                                            @if(!$hargaHarian && !$hargaMingguan && !$hargaBulanan)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Belum ada harga</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-300 border border-blue-200/50 dark:border-blue-700/50">{{
                                            ucfirst($room->status) }}</span>
                                    </td>
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">
                                        {{ optional($room->created_at)->format('d M Y H:i') }}</td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button wire:click="viewDetails({{ $room->id }})"
                                                class="group p-3 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-all duration-300 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-800/30 border border-blue-200/50 dark:border-blue-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('admin.kamar.edit', $room) }}"
                                                class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                                wire:navigate>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button wire:click="confirmDelete({{ $room->id }})"
                                                class="group p-3 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-all duration-300 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-800/30 border border-red-200/50 dark:border-red-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div
                                            class="mx-auto h-32 w-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-8 shadow-lg">
                                            <flux:icon name="bell-snooze"
                                                class="h-16 w-16 text-gray-400 dark:text-gray-500" />
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Belum ada kamar
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">Mulai dengan
                                            menambahkan kamar baru.</p>
                                        <a href="{{ route('admin.kamar.create') }}"
                                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-pink-600 via-rose-600 to-red-600 hover:from-pink-700 hover:via-rose-700 hover:to-red-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-rose-500/25 transform hover:-translate-y-1 transition-all duration-300"
                                            wire:navigate>
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            <span>Tambah Kamar Pertama</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($rooms->hasPages())
                    <div
                        class="px-8 py-6 bg-gradient-to-r from-gray-50/50 via-blue-50/30 to-purple-50/30 dark:from-gray-700/50 dark:via-blue-900/10 dark:to-purple-900/10">
                        {{ $rooms->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="cancelDelete"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-100 to-pink-100 dark:from-red-900/40 dark:to-pink-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-red-200/50 dark:border-red-700/50">
                        <flux:icon name="exclamation-triangle" class="h-8 w-8 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Hapus Kamar</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @if($roomToDelete)
                                Apakah Anda yakin ingin menghapus kamar <strong
                                    class="text-gray-900 dark:text-white font-bold">{{ $roomToDelete->nomor_kamar
                                    }}</strong> (ID: {{ $roomToDelete->id }})?
                                <br><br>
                                <span class="text-red-600 dark:text-red-400 font-medium">Tindakan ini tidak dapat
                                    dibatalkan.</span>
                                @else
                                Apakah Anda yakin ingin menghapus kamar ini? Tindakan ini tidak dapat dibatalkan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <button wire:click="deleteRoom({{ $roomToDelete ? $roomToDelete->id : 0 }})" type="button"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-red-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Hapus Kamar
                    </button>
                    <button wire:click="cancelDelete" type="button"
                        class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Room Modal -->
    @if($showDetailModal && $selectedRoom)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="closeDetails"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-pink-100 to-rose-100 dark:from-pink-900/40 dark:to-rose-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-pink-200/50 dark:border-pink-700/50">
                        <div
                            class="h-10 w-10 rounded-2xl bg-gradient-to-br from-pink-600 to-rose-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                            <span class="text-white font-bold text-base">{{
                                strtoupper(substr($selectedRoom['nomor_kamar'] ?? 'K', 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Detail Kamar</h3>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="font-semibold text-gray-900 dark:text-white">Nomor: {{
                                $selectedRoom['nomor_kamar'] }}</div>
                            <div class="">Tipe: {{ $selectedRoom['tipe'] ?? '—' }}</div>
                            <div class="text-xs mt-1">Dibuat: {{ $selectedRoom['created_at'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                        <div class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-2">Fasilitas</div>
                        <div class="flex flex-wrap gap-2 max-h-48 overflow-auto pr-1">
                            @php $fasilitas = $selectedRoom['fasilitas'] ?? []; @endphp
                            @forelse($fasilitas as $f)
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 dark:from-gray-600 dark:to-gray-500 dark:text-gray-200 border border-gray-200/50 dark:border-gray-500/50">{{
                                $f }}</span>
                            @empty
                            <span class="text-xs text-gray-500 dark:text-gray-400">Tidak ada fasilitas.</span>
                            @endforelse
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-4 border border-blue-200/50 dark:border-blue-700/50">
                        <div class="text-xs font-bold uppercase text-blue-700 dark:text-blue-300 mb-2">Ringkasan</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200">
                            <div class="py-1">
                                <span class="font-semibold">Harga:</span>
                                @if(isset($selectedRoom['hargas']) && count($selectedRoom['hargas']) > 0)
                                @foreach($selectedRoom['hargas'] as $harga)
                                <div class="text-xs mt-1">
                                    <span class="capitalize">{{ $harga['tipe_paket'] }}:</span>
                                    <span class="font-bold">Rp {{ number_format($harga['harga'], 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                @else
                                <div class="text-xs text-gray-500">Belum ada harga</div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between py-1"><span>Status</span><span
                                    class="font-bold">{{ ucfirst($selectedRoom['status'] ?? '') }}</span></div>
                            <div class="flex items-center justify-between py-1"><span>Diperbarui</span><span
                                    class="font-bold">{{ $selectedRoom['updated_at'] }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <a href="{{ isset($selectedRoom['id']) ? route('admin.kamar.edit', $selectedRoom['id']) : '#' }}"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit Kamar
                    </a>
                    <button wire:click="closeDetails" type="button"
                        class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>