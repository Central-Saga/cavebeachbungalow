<?php

use function Livewire\Volt\{
    layout, title, state, mount, with,
    updated, usesPagination, url
};
use App\Models\Reservasi;

layout('components.layouts.admin');
title('Kelola Reservasi');

// aktifkan pagination
usesPagination();

state([
    'search' => '',
    'sortField' => 'created_at',
    'sortDirection' => 'desc',
    'showDeleteModal' => false,
    'reservasiToDelete' => null,
    'selectedReservasi' => null,
]);

// sinkronkan ke URL
state(['search'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function () {
    // Initialize component
});

// lifecycle: reset page saat search berubah
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

$delete = function ($id) {
    $this->reservasiToDelete = Reservasi::find($id);
    $this->showDeleteModal = (bool) $this->reservasiToDelete;
};

$confirmDelete = function () {
    if ($this->reservasiToDelete) {
        $kode = $this->reservasiToDelete->kode_reservasi;
        $this->reservasiToDelete->delete();
        session()->flash('message', "Reservasi {$kode} berhasil dihapus.");
        $this->showDeleteModal = false;
        $this->reservasiToDelete = null;
        $this->resetPage();
    }
};

$cancelDelete = fn () => ($this->showDeleteModal = false) && ($this->reservasiToDelete = null);

$viewReservasi = function ($id) {
    $this->selectedReservasi = Reservasi::with(['kamar.tipeKamar', 'pelanggan'])->find($id);
};

$closeReservasiModal = fn () => ($this->selectedReservasi = null);

$konfirmasiPembayaran = function ($id) {
    $reservasi = Reservasi::find($id);
    if ($reservasi && $reservasi->status_reservasi === 'pending' && $reservasi->bukti_transfer) {
        $reservasi->status_reservasi = 'confirmed';
        $reservasi->save();
        session()->flash('message', 'Pembayaran berhasil dikonfirmasi.');
        $this->selectedReservasi = $reservasi->fresh(['kamar.tipeKamar', 'pelanggan']);
    } else {
        session()->flash('error', 'Konfirmasi gagal. Pastikan bukti transfer sudah diupload.');
    }
};

with(function () {
    try {
        $query = Reservasi::with(['kamar.tipeKamar', 'pelanggan']);

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_reservasi', 'like', "%{$s}%")
                  ->orWhereHas('kamar', fn ($k) => $k->where('nomor_kamar', 'like', "%{$s}%"))
                  ->orWhereHas('pelanggan', fn ($p) => $p->where('nama_lengkap', 'like', "%{$s}%"));
            });
        }

        $reservasis = $query
            ->orderBy($this->sortField ?: 'created_at', $this->sortDirection ?: 'desc')
            ->paginate(10);

        $stats = [
            'total'         => Reservasi::count(),
            'pending'       => Reservasi::where('status_reservasi', 'pending')->count(),
            'confirmed'     => Reservasi::where('status_reservasi', 'confirmed')->count(),
            'cancelled'     => Reservasi::where('status_reservasi', 'cancelled')->count(),
            'completed'     => Reservasi::where('status_reservasi', 'completed')->count(),
        ];

        \Log::info('Reservasi data loaded', [
            'count'          => $reservasis->count(),
            'total'          => $stats['total'],
            'search'         => $this->search,
            'sort_field'     => $this->sortField,
            'sort_direction' => $this->sortDirection,
        ]);

        return compact('reservasis', 'stats');
    } catch (\Exception $e) {
        \Log::error('Error loading reservasi data', [
            'error' => $e->getMessage(),
        ]);

        return [
            'reservasis' => collect(),
            'stats' => ['total' => 0, 'menunggu' => 0, 'terkonfirmasi' => 0, 'dibatalkan' => 0],
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
                                        <flux:icon name="bolt" class="w-8 h-8 text-white" />
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Kelola Reservasi
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Kelola data reservasi kamar & pelanggan
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.reservasi.create') }}"
                                class="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <!-- Plus icon -->
                                <svg class="relative z-10 h-6 w-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="relative z-10">Tambah Reservasi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <!-- Total -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-blue-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 mb-4">
                            <flux:icon name="bolt" class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $stats['total'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Reservasi</p>
                    </div>
                </div>

                <!-- Pending -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-amber-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-amber-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 mb-4">
                            <flux:icon name="clock" class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $stats['pending'] }}</h3>
                        <p class="text-amber-100 text-xs font-medium">Pending</p>
                    </div>
                </div>

                <!-- Confirmed -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-emerald-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 mb-4">
                            <flux:icon name="check-circle" class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $stats['confirmed'] }}</h3>
                        <p class="text-emerald-100 text-xs font-medium">Confirmed</p>
                    </div>
                </div>

                <!-- Cancelled -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-500 via-red-600 to-red-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-red-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 mb-4">
                            <flux:icon name="x-circle" class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $stats['cancelled'] }}</h3>
                        <p class="text-red-100 text-xs font-medium">Cancelled</p>
                    </div>
                </div>

                <!-- Completed -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-purple-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 mb-4">
                            <flux:icon name="check" class="h-7 w-7" />
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $stats['completed'] }}</h3>
                        <p class="text-purple-100 text-xs font-medium">Completed</p>
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
                                        <!-- Search icon -->
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input wire:model.live="search" type="text"
                                        placeholder="Cari kode reservasi, kamar, atau pelanggan..."
                                        class="block w-full pl-14 pr-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium">
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="sortBy('total_harga')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 text-emerald-600 dark:text-emerald-400 hover:from-emerald-100 hover:to-teal-100 dark:hover:from-emerald-800/30 dark:hover:to-teal-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <span class="flex items-center gap-2">
                                        <flux:icon name="currency-dollar" class="w-5 h-5" />
                                        Total
                                    </span>
                                    @if($sortField === 'total_harga')
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

            <!-- Flash -->
            @if (session()->has('message'))
            <div
                class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 shadow-lg">
                <div class="flex items-center gap-3">
                    <!-- Check circle -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
            @endif

            @if (session()->has('error'))
            <div
                class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 shadow-lg">
                <div class="flex items-center gap-3">
                    <!-- Info icon -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Tabel -->
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
                                        Kode</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Kamar</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Pelanggan</th>

                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white/50 dark:bg-gray-800/50 divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($reservasis as $reservasi)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 dark:hover:from-blue-900/10 dark:hover:to-purple-900/10 transition-all duration-300 group"
                                    wire:key="reservasi-{{ $reservasi->id }}">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{
                                            $reservasi->kode_reservasi }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $reservasi->kamar->nomor_kamar ?? '-' }}<br>
                                            <span class="text-xs text-gray-500">{{
                                                $reservasi->kamar->tipeKamar->nama_tipe ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{
                                            $reservasi->pelanggan->nama_lengkap ?? '-' }}</div>
                                    </td>

                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-300 border border-blue-200/50 dark:border-blue-700/50">
                                            {{ ucfirst($reservasi->status_reservasi) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <!-- View -->
                                            <button wire:click="viewReservasi({{ $reservasi->id }})"
                                                class="group p-3 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-all duration-300 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-800/30 border border-indigo-200/50 dark:border-indigo-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <!-- Edit -->
                                            <a href="{{ route('admin.reservasi.edit', $reservasi->id) }}"
                                                class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                                wire:navigate>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <!-- Delete -->
                                            <button wire:click="delete({{ $reservasi->id }})"
                                                class="group p-3 text-rose-600 dark:text-rose-400 hover:text-rose-900 dark:hover:text-rose-300 transition-all duration-300 bg-rose-50 dark:bg-rose-900/20 rounded-xl hover:bg-rose-100 dark:hover:bg-rose-800/30 border border-rose-200/50 dark:border-rose-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div
                                            class="mx-auto h-32 w-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-8 shadow-lg">
                                            <flux:icon name="bolt" class="h-16 w-16 text-gray-400" />
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Tidak ada
                                            reservasi</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">Mulai dengan membuat
                                            reservasi pertama Anda.</p>
                                        <a href="{{ route('admin.reservasi.create') }}"
                                            class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300"
                                            wire:navigate>
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Tambah Reservasi Pertama</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($reservasis->hasPages())
                    <div
                        class="px-8 py-6 bg-gradient-to-r from-gray-50/50 via-blue-50/30 to-purple-50/30 dark:from-gray-700/50 dark:via-blue-900/10 dark:to-purple-900/10">
                        {{ $reservasis->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="cancelDelete"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-100 to-pink-100 dark:from-red-900/40 dark:to-pink-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-red-200/50 dark:border-red-700/50">
                        <!-- Shield exclamation -->
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Hapus Reservasi</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                @if($reservasiToDelete)
                                Apakah Anda yakin ingin menghapus reservasi
                                <strong class="text-gray-900 dark:text-white font-bold">
                                    {{ $reservasiToDelete->kode_reservasi }}
                                </strong>?
                                <br><br>
                                <span class="text-red-600 dark:text-red-400 font-medium">Tindakan ini tidak dapat
                                    dibatalkan.</span>
                                @else
                                Apakah Anda yakin ingin menghapus reservasi ini? Tindakan ini tidak dapat dibatalkan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <button wire:click="confirmDelete" type="button"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-red-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto">
                        <!-- Trash -->
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                    <button wire:click="cancelDelete" type="button"
                        class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                        <!-- X -->
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($selectedReservasi)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="closeReservasiModal"></div>
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-8 border border-white/30 dark:border-gray-700/50">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-pink-100 to-rose-100 dark:from-pink-900/40 dark:to-rose-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-pink-200/50 dark:border-pink-700/50">
                        <div
                            class="h-10 w-10 rounded-2xl bg-gradient-to-br from-pink-600 to-rose-700 flex items-center justify-center shadow-lg border-2 border-white/20">
                            <span class="text-white font-bold text-base">{{
                                strtoupper(substr($selectedReservasi->kode_reservasi ?? 'R', 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Detail Reservasi</h3>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="font-semibold text-gray-900 dark:text-white">Kode: {{
                                $selectedReservasi->kode_reservasi }}</div>
                            <div class="">Kamar: {{ $selectedReservasi->kamar->nomor_kamar ?? '—' }} ({{
                                $selectedReservasi->kamar->tipeKamar->nama_tipe ?? '—' }})</div>
                            <div class="text-xs mt-1">Dibuat: {{ $selectedReservasi->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                        <div class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-2">Informasi
                            Reservasi</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 space-y-2">
                            <div class="flex items-center justify-between py-1"><span>Check In</span><span
                                    class="font-bold">{{ $selectedReservasi->tanggal_check_in }}</span></div>
                            <div class="flex items-center justify-between py-1"><span>Check Out</span><span
                                    class="font-bold">{{ $selectedReservasi->tanggal_check_out }}</span></div>
                            <div class="flex items-center justify-between py-1"><span>Total Harga</span><span
                                    class="font-bold">Rp {{ number_format($selectedReservasi->total_harga, 0, ',', '.')
                                    }}</span></div>
                            <div class="flex items-center justify-between py-1"><span>Status</span><span
                                    class="font-bold">{{ ucfirst($selectedReservasi->status_reservasi) }}</span></div>
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-4 border border-blue-200/50 dark:border-blue-700/50">
                        <div class="text-xs font-bold uppercase text-blue-700 dark:text-blue-300 mb-2">Informasi
                            Pelanggan</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 space-y-2">
                            <div class="flex items-center justify-between py-1"><span>Nama</span><span
                                    class="font-bold">{{ $selectedReservasi->pelanggan->nama_lengkap ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1"><span>Email</span><span
                                    class="font-bold">{{ $selectedReservasi->pelanggan->email ?? '—' }}</span></div>
                            <div class="flex items-center justify-between py-1"><span>Telepon</span><span
                                    class="font-bold">{{ $selectedReservasi->pelanggan->nomor_telepon ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-4 border border-emerald-200/50 dark:border-emerald-700/50">
                        <div class="text-xs font-bold uppercase text-emerald-700 dark:text-emerald-300 mb-2">Status
                            Pembayaran</div>
                        <div class="text-sm text-gray-700 dark:text-gray-200 space-y-2">
                            <div class="flex items-center justify-between py-1"><span>Total Bayar</span><span
                                    class="font-bold text-emerald-600">Rp {{
                                    number_format($selectedReservasi->total_bayar ?? 0, 0, ',', '.') }}</span></div>
                            <div class="flex items-center justify-between py-1"><span>Sisa Bayar</span><span
                                    class="font-bold {{ ($selectedReservasi->sisa_bayar ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format($selectedReservasi->sisa_bayar ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1"><span>Status</span><span
                                    class="font-bold {{ ($selectedReservasi->lunas ?? false) ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ ($selectedReservasi->lunas ?? false) ? 'Lunas' : 'Belum Lunas' }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Pembayaran -->
                @if($selectedReservasi->pembayarans && $selectedReservasi->pembayarans->count() > 0)
                <div class="mt-6">
                    <div
                        class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-2xl p-4 border border-indigo-200/50 dark:border-indigo-700/50">
                        <div class="text-xs font-bold uppercase text-indigo-700 dark:text-indigo-300 mb-3">Riwayat
                            Pembayaran</div>
                        <div class="space-y-3">
                            @foreach($selectedReservasi->pembayarans as $pembayaran)
                            <div
                                class="bg-white/50 dark:bg-gray-800/50 rounded-xl p-3 border border-indigo-200/30 dark:border-indigo-700/30">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $pembayaran->status === 'menunggu' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                           ($pembayaran->status === 'terverifikasi' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                           ($pembayaran->status === 'ditolak' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                           'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300')) }}">
                                        <i class="fas {{ $pembayaran->status === 'menunggu' ? 'fa-clock' :
                                                       ($pembayaran->status === 'terverifikasi' ? 'fa-check-circle' :
                                                       ($pembayaran->status === 'ditolak' ? 'fa-times-circle' :
                                                       'fa-question-circle')) }} mr-1"></i>
                                        {{ ucfirst($pembayaran->status) }}
                                    </span>
                                </div>
                                @if($pembayaran->keterangan)
                                <div class="text-xs text-gray-600 dark:text-gray-400 italic">
                                    "{{ $pembayaran->keterangan }}"
                                </div>
                                @endif
                                @if($pembayaran->bukti_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/'.$pembayaran->bukti_path) }}" target="_blank"
                                        class="inline-flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200">
                                        <i class="fas fa-image"></i>
                                        Lihat Bukti Transfer
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-6">
                    <div
                        class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-credit-card text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Belum ada riwayat pembayaran</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                    <a href="{{ route('admin.reservasi.edit', $selectedReservasi->id) }}"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit Reservasi
                    </a>
                    @if($selectedReservasi->status_reservasi == 'pending' && $selectedReservasi->bukti_transfer)
                    <button wire:click="konfirmasiPembayaran({{ $selectedReservasi->id }})"
                        class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-green-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:w-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Konfirmasi Pembayaran
                    </button>
                    @endif
                    <button wire:click="closeReservasiModal" type="button"
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