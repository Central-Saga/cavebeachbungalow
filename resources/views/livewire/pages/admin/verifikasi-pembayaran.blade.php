<?php

use function Livewire\Volt\{
    layout, title, state, mount, with, usesPagination
};
use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Storage;

layout('components.layouts.admin');
title('Verifikasi Pembayaran');

usesPagination();

state([
    'search' => '',
    'statusFilter' => '',
    'showVerifikasiModal' => false,
    'selectedPembayaran' => null,
    'verifikasiStatus' => '',
    'alasanPenolakan' => '',
    'isLoading' => false
]);

// Sinkronkan ke URL
state(['search'])->url();
state(['statusFilter'])->url();
state(['page' => 1])->url(as: 'p', history: true, keep: true);

mount(function () {
    // Initialize component
});

$filterByStatus = function ($status) {
    $this->statusFilter = $status;
    $this->resetPage();
};

$viewPembayaran = function ($id) {
    $this->selectedPembayaran = Pembayaran::with(['reservasi.kamar.tipeKamar', 'reservasi.pelanggan'])->find($id);
    $this->showVerifikasiModal = true;
    $this->reset(['verifikasiStatus', 'alasanPenolakan']);
};

$closeVerifikasiModal = function () {
    $this->showVerifikasiModal = false;
    $this->selectedPembayaran = null;
    $this->verifikasiStatus = '';
    $this->alasanPenolakan = '';
    $this->isLoading = false;

    // Force re-render dan dispatch event
    $this->dispatch('modal-closed');
};

$resetForm = function () {
    $this->reset(['verifikasiStatus', 'alasanPenolakan']);
};

$verifikasiPembayaran = function () {
    if (!$this->selectedPembayaran) return;

    // Validasi yang lebih ketat
    $this->validate([
        'verifikasiStatus' => 'required|in:terverifikasi,ditolak',
        'alasanPenolakan' => 'required_if:verifikasiStatus,ditolak|max:500',
    ]);

    // Validasi tambahan untuk memastikan data valid
    if ($this->verifikasiStatus === 'ditolak' && empty($this->alasanPenolakan)) {
        session()->flash('error', 'Alasan penolakan harus diisi.');
        return;
    }

    $this->isLoading = true;

    try {
        $pembayaran = $this->selectedPembayaran;

        // Pastikan pembayaran masih dalam status menunggu
        if ($pembayaran->status !== 'menunggu') {
            session()->flash('error', 'Pembayaran ini sudah tidak dapat diverifikasi.');
            $this->isLoading = false;
            return;
        }

        $pembayaran->status = $this->verifikasiStatus;

        if ($this->verifikasiStatus === 'ditolak') {
            $pembayaran->keterangan = $this->alasanPenolakan;
        }

        $pembayaran->save();

        // Update status reservasi jika pembayaran terverifikasi
        if ($this->verifikasiStatus === 'terverifikasi') {
            $reservasi = $pembayaran->reservasi;

            // Validasi data reservasi
            if (!$reservasi) {
                throw new \Exception('Data reservasi tidak ditemukan');
            }

            if (!$reservasi->total_harga || $reservasi->total_harga <= 0) {
                throw new \Exception('Total harga reservasi tidak valid');
            }

            // Log untuk debugging
            \Log::info('Verifikasi pembayaran terverifikasi', [
                'pembayaran_id' => $pembayaran->id,
                'reservasi_id' => $reservasi->id,
                'nominal_pembayaran' => $pembayaran->nominal,
                'total_harga_reservasi' => $reservasi->total_harga,
                'total_terbayar_sebelum' => $reservasi->total_terbayar,
                'total_terbayar_setelah' => $reservasi->total_terbayar + $pembayaran->nominal,
                'status_reservasi_sebelum' => $reservasi->status_reservasi,
                'is_lunas' => ($reservasi->total_terbayar + $pembayaran->nominal) >= $reservasi->total_harga
            ]);

            // Gunakan accessor methods yang sudah ada di model
            $totalTerbayar = $reservasi->total_terbayar + $pembayaran->nominal;
            $isLunas = $totalTerbayar >= $reservasi->total_harga;

                                // Update status reservasi menjadi terkonfirmasi karena pembayaran diverifikasi
            if ($reservasi->status_reservasi === 'pending') {
                $reservasi->status_reservasi = 'terkonfirmasi';
                $reservasi->save();

                \Log::info('Status reservasi diupdate menjadi terkonfirmasi', [
                    'reservasi_id' => $reservasi->id,
                    'status_baru' => 'terkonfirmasi'
                ]);
            }
    }

    // Set flash message SEBELUM modal ditutup
    \Log::info('Setting flash message', [
        'verifikasiStatus' => $this->verifikasiStatus,
        'alasanPenolakan' => $this->alasanPenolakan
    ]);

    if ($this->verifikasiStatus === 'terverifikasi') {
        $reservasi = $pembayaran->reservasi;
        $totalTerbayar = $reservasi->total_terbayar;
        $isLunas = $totalTerbayar >= $reservasi->total_harga;

        \Log::info('Flash message untuk terverifikasi', [
            'totalTerbayar' => $totalTerbayar,
            'totalHarga' => $reservasi->total_harga,
            'isLunas' => $isLunas,
            'message' => 'Pembayaran berhasil diverifikasi dan reservasi sudah lunas!'
        ]);

        // Kalau pembayaran diverifikasi, berarti langsung lunas
        session()->flash('message', 'Pembayaran berhasil diverifikasi dan reservasi sudah lunas!');
    } else {
        \Log::info('Flash message untuk ditolak', [
            'alasanPenolakan' => $this->alasanPenolakan
        ]);
        session()->flash('message', 'Pembayaran ditolak dengan alasan: ' . $this->alasanPenolakan);
    }

    // Tutup modal dan reset state SETELAH flash message
    $this->closeVerifikasiModal();

    } catch (\Exception $e) {
        \Log::error('Error verifikasi pembayaran', [
            'pembayaran_id' => $this->selectedPembayaran->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        session()->flash('error', 'Gagal verifikasi pembayaran: ' . $e->getMessage());
        $this->isLoading = false;
    }
};

with(function () {
    try {
        $query = Pembayaran::with(['reservasi.kamar.tipeKamar', 'reservasi.pelanggan']);

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('nominal', 'like', "%{$s}%")
                  ->orWhereHas('reservasi', fn ($r) => $r->where('kode_reservasi', 'like', "%{$s}%"))
                  ->orWhereHas('reservasi.pelanggan', fn ($p) => $p->where('nama_lengkap', 'like', "%{$s}%"));
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $pembayarans = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Pembayaran::count(),
            'menunggu' => Pembayaran::where('status', 'menunggu')->count(),
            'terverifikasi' => Pembayaran::where('status', 'terverifikasi')->count(),
            'ditolak' => Pembayaran::where('status', 'ditolak')->count(),
        ];

        return compact('pembayarans', 'stats');
    } catch (\Exception $e) {
        return [
            'pembayarans' => collect(),
            'stats' => ['total' => 0, 'menunggu' => 0, 'terverifikasi' => 0, 'ditolak' => 0],
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
                                        class="w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-green-800 to-emerald-800 dark:from-white dark:via-green-200 dark:to-emerald-200 bg-clip-text text-transparent">
                                            Verifikasi Pembayaran
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Verifikasi dan kelola status pembayaran pelanggan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('message'))
            <div
                class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-lg dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-700/30 dark:text-green-200 relative z-40">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
            @endif

            @if (session()->has('error'))
            <div
                class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-lg dark:from-red-900/20 dark:to-pink-900/20 dark:border-red-700/30 dark:text-red-200 relative z-40">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Pembayaran -->
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
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['total'] }}</h3>
                        <p class="text-blue-100 text-xs font-medium">Total Pembayaran</p>
                    </div>
                </div>

                <!-- Menunggu Verifikasi -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-yellow-500 via-amber-600 to-yellow-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-yellow-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['menunggu'] }}</h3>
                        <p class="text-yellow-100 text-xs font-medium">Menunggu Verifikasi</p>
                    </div>
                </div>

                <!-- Terverifikasi -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-500 via-emerald-600 to-green-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-green-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['terverifikasi'] }}</h3>
                        <p class="text-green-100 text-xs font-medium">Terverifikasi</p>
                    </div>
                </div>

                <!-- Ditolak -->
                <div
                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-red-500 via-pink-600 to-red-700 p-6 text-white shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-red-500/25 transform hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $stats['ditolak'] }}</h3>
                        <p class="text-red-100 text-xs font-medium">Ditolak</p>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="mb-8">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                    </div>
                    <div
                        class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl p-6 border border-white/30 dark:border-gray-700/50 shadow-2xl">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                            <!-- Search -->
                            <div class="flex-grow">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input wire:model.live.debounce.300ms="search" type="text"
                                        placeholder="Cari pembayaran (kode reservasi, nama pelanggan, nominal)..."
                                        class="block w-full pl-14 pr-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium">
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="flex gap-3">
                                <button wire:click="filterByStatus('')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 hover:from-gray-100 hover:to-gray-200 dark:hover:from-gray-600 dark:hover:to-gray-500 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1 {{ !$statusFilter ? 'ring-2 ring-blue-500' : '' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                        Semua
                                    </span>
                                </button>
                                <button wire:click="filterByStatus('menunggu')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 text-yellow-700 dark:text-yellow-300 hover:from-yellow-100 hover:to-amber-100 dark:hover:from-yellow-800/30 dark:hover:to-amber-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1 {{ $statusFilter === 'menunggu' ? 'ring-2 ring-blue-500' : '' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Menunggu
                                    </span>
                                </button>
                                <button wire:click="filterByStatus('terverifikasi')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 text-green-700 dark:text-green-300 hover:from-green-100 hover:to-emerald-100 dark:hover:from-green-800/30 dark:hover:to-emerald-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1 {{ $statusFilter === 'terverifikasi' ? 'ring-2 ring-blue-500' : '' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Terverifikasi
                                    </span>
                                </button>
                                <button wire:click="filterByStatus('ditolak')"
                                    class="group px-6 py-4 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 text-red-700 dark:text-red-300 hover:from-red-100 hover:to-pink-100 dark:hover:from-red-800/30 dark:hover:to-pink-800/30 transition-all duration-300 backdrop-blur-sm font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1 {{ $statusFilter === 'ditolak' ? 'ring-2 ring-blue-500' : '' }}">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        Ditolak
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembayaran Table -->
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
                                        Reservasi</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Pelanggan</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Nominal</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Tanggal</th>
                                    <th
                                        class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white/50 dark:bg-gray-800/50 divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($pembayarans as $pembayaran)
                                <tr
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 dark:hover:from-blue-900/10 dark:hover:to-purple-900/10 transition-all duration-300 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{
                                            $pembayaran->reservasi->kode_reservasi ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{
                                            $pembayaran->reservasi->kamar->tipeKamar->nama_tipe ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{
                                            $pembayaran->reservasi->pelanggan->nama_lengkap ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{
                                            $pembayaran->reservasi->pelanggan->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">Rp {{
                                            number_format($pembayaran->nominal, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if($pembayaran->status === 'menunggu')
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-700 dark:from-yellow-900/30 dark:to-amber-900/30 dark:text-yellow-300 border border-yellow-200/50 dark:border-yellow-700/50">
                                            Menunggu Verifikasi
                                        </span>
                                        @elseif($pembayaran->status === 'terverifikasi')
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300 border border-green-200/50 dark:border-green-700/50">
                                            Terverifikasi
                                        </span>
                                        @else
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-gradient-to-r from-red-100 to-pink-100 text-red-700 dark:from-red-900/30 dark:to-pink-900/30 dark:text-red-300 border border-red-200/50 dark:border-red-700/50">
                                            Ditolak
                                        </span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-8 py-6 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300 font-medium">
                                        {{ $pembayaran->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-3">
                                            @if($pembayaran->status === 'menunggu')
                                            <button wire:click="viewPembayaran({{ $pembayaran->id }})"
                                                class="group p-3 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-all duration-300 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-800/30 border border-blue-200/50 dark:border-blue-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>

                                            @if($pembayaran->bukti_path)
                                            <a href="{{ route('admin.pembayaran.bukti', $pembayaran->id) }}"
                                            target="_blank"
                                            class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                            title="Lihat bukti transfer">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <a href="{{ route('admin.pembayaran.download', $pembayaran->id) }}"
                                            class="group p-3 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-all duration-300 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-800/30 border border-indigo-200/50 dark:border-indigo-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                            title="Download bukti transfer">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 10l5 5m0 0 5-5m-5 5V4"></path>
                                                </svg>
                                            </a>
                                            @endif
                                    
                                            @else
                                            <button wire:click="viewPembayaran({{ $pembayaran->id }})"
                                                class="group p-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 transition-all duration-300 bg-gray-50 dark:bg-gray-900/20 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800/30 border border-gray-200/50 dark:border-gray-700/50 hover:shadow-lg transform hover:-translate-y-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </button>
                                            @if($pembayaran->bukti_path)
                                            <a href="{{ route('admin.pembayaran.bukti', $pembayaran->id) }}"
                                            target="_blank"
                                            class="group p-3 text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition-all duration-300 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800/30 border border-emerald-200/50 dark:border-emerald-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                            title="Lihat bukti transfer">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <a href="{{ route('admin.pembayaran.download', $pembayaran->id) }}"
                                            class="group p-3 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-all duration-300 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-800/30 border border-indigo-200/50 dark:border-indigo-700/50 hover:shadow-lg transform hover:-translate-y-1"
                                            title="Download bukti transfer">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 10l5 5m0 0 5-5m-5 5V4"></path>
                                                </svg>
                                            </a>
                                            @endif
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div
                                            class="mx-auto h-32 w-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-full flex items-center justify-center mb-8 shadow-lg">
                                            <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Tidak ada
                                            pembayaran</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">Belum ada pembayaran
                                            yang perlu diverifikasi.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($pembayarans->hasPages())
                    <div
                        class="px-8 py-6 bg-gradient-to-r from-gray-50/50 via-blue-50/30 to-purple-50/30 dark:from-gray-700/50 dark:via-blue-900/10 dark:to-purple-900/10">
                        {{ $pembayarans->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Verifikasi Modal -->
    @if($showVerifikasiModal && $selectedPembayaran)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{}" x-init="
             document.body.style.overflow = 'hidden';
             $el.addEventListener('keydown', (e) => {
                 if (e.key === 'Escape') {
                     $wire.closeVerifikasiModal();
                 }
             });

             $el.addEventListener('click', (e) => {
                 if (e.target === $el) {
                     $wire.closeVerifikasiModal();
                 }
             });
         " x-on:modal-closed.window="
             document.body.style.overflow = 'auto';
             $el.remove();
         ">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Backdrop dengan click handler -->
            <div class="fixed inset-0 backdrop-blur-md transition-opacity" wire:click="closeVerifikasiModal"></div>

            <!-- Modal Content -->
            <div
                class="relative transform overflow-hidden rounded-3xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl px-6 pb-6 pt-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-8 border border-white/30 dark:border-gray-700/50">

                <!-- Close Button -->
                <div class="absolute top-4 right-4">
                    <button type="button" wire:click="closeVerifikasiModal"
                        class="rounded-full p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/40 dark:to-purple-900/40 sm:mx-0 sm:h-14 sm:w-14 border border-blue-200/50 dark:border-blue-700/50">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Verifikasi Pembayaran</h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Verifikasi pembayaran untuk reservasi <strong
                                    class="text-gray-900 dark:text-white font-bold">{{
                                    $selectedPembayaran->reservasi->kode_reservasi ?? 'N/A' }}</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detail Pembayaran -->
                <div
                    class="mt-6 p-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700 dark:to-blue-900/20 rounded-2xl border border-gray-200/50 dark:border-gray-600/50">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Kode Reservasi:</span>
                            <p class="text-gray-900 dark:text-white font-semibold">{{
                                $selectedPembayaran->reservasi->kode_reservasi ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Pelanggan:</span>
                            <p class="text-gray-900 dark:text-white font-semibold">{{
                                $selectedPembayaran->reservasi->pelanggan->nama_lengkap ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Nominal:</span>
                            <p class="text-gray-900 dark:text-white font-semibold">Rp {{
                                number_format($selectedPembayaran->nominal, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal:</span>
                            <p class="text-gray-900 dark:text-white font-semibold">{{
                                $selectedPembayaran->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($selectedPembayaran->bukti_path)
                <!-- Bukti Transfer Preview -->
                <div class="mt-4">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-3">Bukti Transfer</h4>
                    <div
                        class="bg-white/70 dark:bg-gray-900/30 rounded-2xl p-3 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-start gap-4">
                            <a href="{{ Storage::disk('public')->url($selectedPembayaran->bukti_path) }}"
                                target="_blank" class="block">
                                <img src="{{ Storage::disk('public')->url($selectedPembayaran->bukti_path) }}"
                                    alt="Bukti transfer reservasi {{ $selectedPembayaran->reservasi->kode_reservasi ?? '' }}"
                                    class="w-28 h-28 object-cover rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200">
                            </a>
                            <div class="flex flex-col gap-2">
                                <a href="{{ Storage::disk('public')->url($selectedPembayaran->bukti_path) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow hover:bg-emerald-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Lihat Full Size
                                </a>
                                <a href="{{ Storage::disk('public')->url($selectedPembayaran->bukti_path) }}" download
                                    class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow hover:bg-indigo-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M7 10l5 5m0 0 5-5m-5 5V4">
                                        </path>
                                    </svg>
                                    Download Bukti
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form Verifikasi -->
                <form wire:submit.prevent="verifikasiPembayaran" class="mt-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Status
                            Verifikasi</label>
                        <div class="space-y-3">
                            <label
                                class="flex items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200/50 dark:border-green-700/50 hover:bg-green-100 dark:hover:bg-green-800/30 transition-all duration-300 cursor-pointer {{ $verifikasiStatus === 'terverifikasi' ? 'ring-2 ring-green-500 border-green-500' : '' }}">
                                <input type="radio" wire:model.live="verifikasiStatus" value="terverifikasi"
                                    class="mr-3 text-green-600 focus:ring-green-500">
                                <span
                                    class="text-sm font-medium text-green-700 dark:text-green-300">Terverifikasi</span>
                            </label>
                            <label
                                class="flex items-center p-3 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-xl border border-red-200/50 dark:border-red-700/50 hover:bg-red-100 dark:hover:bg-red-800/30 transition-all duration-300 cursor-pointer {{ $verifikasiStatus === 'ditolak' ? 'ring-2 ring-red-500 border-red-500' : '' }}">
                                <input type="radio" wire:model.live="verifikasiStatus" value="ditolak"
                                    class="mr-3 text-red-600 focus:ring-red-500">
                                <span class="text-sm font-medium text-red-700 dark:text-red-300">Ditolak</span>
                            </label>
                        </div>
                    </div>

                    @if($verifikasiStatus === 'ditolak')
                    <div class="mb-6" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100">
                        <label for="alasanPenolakan"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan
                            Penolakan <span class="text-red-500">*</span></label>
                        <textarea wire:model.live.debounce.300ms="alasanPenolakan" id="alasanPenolakan" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300/50 dark:border-gray-600/50 rounded-2xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 backdrop-blur-sm"
                            placeholder="Masukkan alasan penolakan..."></textarea>
                        @error('alasanPenolakan')
                        <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ strlen($alasanPenolakan) }}/500 karakter
                        </div>
                        @if(empty($alasanPenolakan))
                        <div class="mt-1 text-xs text-red-500">
                            ⚠️ Alasan penolakan wajib diisi untuk melanjutkan
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-4">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-blue-500/25 transition-all duration-300 transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
                            {{ $isLoading || ($verifikasiStatus==='ditolak' && empty($alasanPenolakan)) ? 'disabled'
                            : '' }}>
                            @if($isLoading)
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                            @else
                            Verifikasi
                            @endif
                        </button>
                        <button type="button" wire:click="closeVerifikasiModal"
                            class="mt-4 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 px-6 py-3 text-sm font-bold text-gray-700 dark:text-gray-200 shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>