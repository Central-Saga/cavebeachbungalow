<?php

use function Livewire\Volt\{
    layout, title, state, mount, uses
};
use App\Models\Kamar;
use App\Models\Pelanggan;
use App\Helpers\ReservasiHelper;

layout('components.layouts.admin');
title('Create Reservasi');

state([
    'kode_reservasi' => '',
    'kamar_id' => '',
    'pelanggan_id' => '',
    'tipe_paket' => 'harian',
    'durasi' => 1,
    'tanggal_check_in' => '',
    'tanggal_check_out' => '',
    'total_harga' => '',
    'status_reservasi' => 'pending',
    'kamars' => [],
    'pelanggans' => [],
    'harga_per_unit' => 0,
    'unit_label' => 'hari',
]);

// Watcher untuk memastikan perhitungan harga selalu ter-update
$updated = function($property) {
    if (in_array($property, ['kamar_id', 'tipe_paket', 'durasi'])) {
        $this->hitungTotalHarga();
    }
    if (in_array($property, ['tanggal_check_in', 'durasi', 'tipe_paket'])) {
        $this->hitungTanggalCheckout();
    }
};

mount(function() {
    $this->kamars = Kamar::with(['tipeKamar', 'hargas'])->orderBy('nomor_kamar')->get();
    $this->pelanggans = Pelanggan::with('user')->orderBy('id')->get();
    $this->kode_reservasi = ReservasiHelper::generateKodeReservasi();

    \Log::info('Mount completed', [
        'kamars_count' => $this->kamars->count(),
        'sample_kamar' => $this->kamars->first() ? [
            'id' => $this->kamars->first()->id,
            'hargas_count' => $this->kamars->first()->hargas->count(),
            'hargas' => $this->kamars->first()->hargas->toArray()
        ] : null
    ]);
});

// Fungsi untuk mengubah tipe paket dan menyesuaikan durasi
$ubahTipePaket = function() {
    // Reset durasi berdasarkan tipe paket
    switch ($this->tipe_paket) {
        case 'harian':
            $this->durasi = 1;
            $this->unit_label = 'hari';
            break;
        case 'mingguan':
            $this->durasi = 1;
            $this->unit_label = 'minggu';
            break;
        case 'bulanan':
            $this->durasi = 1;
            $this->unit_label = 'bulan';
            break;
    }

    // Recalculate tanggal checkout dan harga
    $this->hitungTanggalCheckout();
    $this->hitungTotalHarga();
};

// Fungsi untuk menghitung tanggal checkout berdasarkan durasi dan tipe paket
$hitungTanggalCheckout = function() {
    if ($this->tanggal_check_in && $this->durasi > 0) {
        $checkIn = \Carbon\Carbon::parse($this->tanggal_check_in);

        switch ($this->tipe_paket) {
            case 'harian':
                $this->tanggal_check_out = $checkIn->addDays((int)$this->durasi)->format('Y-m-d');
                break;
            case 'mingguan':
                $this->tanggal_check_out = $checkIn->addWeeks((int)$this->durasi)->format('Y-m-d');
                break;
            case 'bulanan':
                $this->tanggal_check_out = $checkIn->addMonths((int)$this->durasi)->format('Y-m-d');
                break;
        }

        $this->hitungTotalHarga();
    }
};

// Fungsi untuk menghitung total harga berdasarkan tipe paket
$hitungTotalHarga = function() {
    \Log::info('hitungTotalHarga called', [
        'kamar_id' => $this->kamar_id,
        'tipe_paket' => $this->tipe_paket,
        'durasi' => $this->durasi,
        'durasi_type' => gettype($this->durasi)
    ]);

    if ($this->kamar_id && $this->durasi > 0) {
        $kamar = $this->kamars->find($this->kamar_id);
        \Log::info('Kamar found', [
            'kamar' => $kamar ? $kamar->id : null,
            'tipeKamar' => $kamar && $kamar->tipeKamar ? $kamar->tipeKamar->id : null
        ]);

        if ($kamar) {
            // Ambil harga berdasarkan tipe paket yang dipilih
            // Gunakan collection yang sudah di-load, bukan query baru
            $hargaPaket = $kamar->hargas->where('tipe_paket', $this->tipe_paket)->first();

            \Log::info('Harga query result', [
                'kamar_id' => $kamar->id,
                'tipe_paket' => $this->tipe_paket,
                'hargaPaket' => $hargaPaket ? $hargaPaket->toArray() : null,
                'all_hargas' => $kamar->hargas->toArray()
            ]);

            if ($hargaPaket) {
                $this->harga_per_unit = (float)$hargaPaket->harga;
                $this->total_harga = $this->harga_per_unit * (int)$this->durasi;

                \Log::info('Harga calculated', [
                    'tipe_paket' => $this->tipe_paket,
                    'harga_per_unit' => $this->harga_per_unit,
                    'total_harga' => $this->total_harga,
                    'harga_record' => $hargaPaket->toArray()
                ]);
            } else {
                \Log::warning('Harga untuk tipe paket tidak ditemukan', [
                    'kamar_id' => $kamar->id,
                    'tipe_paket' => $this->tipe_paket,
                    'hargas_count' => $kamar->hargas->count()
                ]);
                $this->harga_per_unit = 0;
                $this->total_harga = 0;
            }
        }
    }
};

// Fungsi untuk validasi overlap reservasi
$validasiOverlap = function() {
    if ($this->kamar_id && $this->tanggal_check_in && $this->tanggal_check_out) {
        $overlap = \App\Models\Reservasi::where('kamar_id', $this->kamar_id)
            ->where('status_reservasi', '!=', 'cancelled')
            ->where(function($query) {
                $query->whereBetween('tanggal_check_in', [$this->tanggal_check_in, $this->tanggal_check_out])
                    ->orWhereBetween('tanggal_check_out', [$this->tanggal_check_in, $this->tanggal_check_out])
                    ->orWhere(function($q) {
                        $q->where('tanggal_check_in', '<=', $this->tanggal_check_in)
                            ->where('tanggal_check_out', '>=', $this->tanggal_check_out);
                    });
            })
            ->exists();

        if ($overlap) {
            $this->addError('tanggal_check_in', 'Kamar sudah dibooking untuk tanggal yang dipilih. Silakan pilih tanggal lain.');
            return false;
        }
    }
    return true;
};

$save = function() {
    // Validasi overlap sebelum save
    if (!$this->validasiOverlap()) {
        return;
    }

    $this->validate([
        'kode_reservasi' => 'required|string|unique:reservasis,kode_reservasi',
        'kamar_id' => 'required|exists:kamars,id',
        'pelanggan_id' => 'required|exists:pelanggans,id',
        'tipe_paket' => 'required|string|in:harian,mingguan,bulanan',
        'durasi' => 'required|integer|min:1',
        'tanggal_check_in' => 'required|date|after_or_equal:today',
        'tanggal_check_out' => 'required|date|after:tanggal_check_in',
        'total_harga' => 'required|numeric|min:0',
        'status_reservasi' => 'required|string',
    ]);

    try {
        \App\Models\Reservasi::create([
            'kode_reservasi' => $this->kode_reservasi,
            'kamar_id' => $this->kamar_id,
            'pelanggan_id' => $this->pelanggan_id,
            'tipe_paket' => $this->tipe_paket,
            'durasi' => $this->durasi,
            'tanggal_check_in' => $this->tanggal_check_in,
            'tanggal_check_out' => $this->tanggal_check_out,
            'total_harga' => $this->total_harga,
            'status_reservasi' => $this->status_reservasi,
        ]);

        session()->flash('message', 'Reservasi berhasil dibuat!');
        return $this->redirect(route('admin.reservasi.index'));
    } catch (\Exception $e) {
        \Log::error('Error creating reservasi:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        session()->flash('error', 'Gagal membuat reservasi: ' . $e->getMessage());
    }
};

?>

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-slate-900">
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header dengan design yang senada -->
            <div class="mb-10">
                <div class="relative">
                    <!-- Background decoration -->
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
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1
                                            class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 dark:from-white dark:via-blue-200 dark:to-purple-200 bg-clip-text text-transparent">
                                            Tambah Reservasi Baru
                                        </h1>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                                            Buat reservasi baru Cave Beach Bungalow
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.reservasi.index') }}"
                                class="group relative inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-600 via-gray-700 to-gray-800 hover:from-gray-700 hover:via-gray-800 hover:to-gray-900 text-white font-bold rounded-2xl shadow-2xl hover:shadow-gray-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden"
                                wire:navigate>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                <span class="relative z-10">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form dengan design yang modern -->
            <div class="relative">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-pink-600/5 rounded-3xl blur-3xl">
                </div>
                <div
                    class="relative bg-white/90 dark:bg-gray-800/90 shadow-2xl rounded-3xl overflow-hidden border border-white/30 dark:border-gray-700/50 backdrop-blur-xl">
                    <form wire:submit="save" class="p-8 space-y-8">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="kode_reservasi"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Kode Reservasi <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="kode_reservasi" type="text" id="kode_reservasi"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        placeholder="Kode akan di-generate otomatis" readonly>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Kode reservasi di-generate otomatis dengan format RSV + YYYY + MM + 4 digit
                                        nomor urut
                                    </p>
                                    @error('kode_reservasi')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status_reservasi"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Status Reservasi <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="status_reservasi" id="status_reservasi"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                        <option value="">Pilih status</option>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                    @error('status_reservasi')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Kamar dan Pelanggan Selection -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                Pilihan Kamar & Pelanggan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="kamar_id"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Kamar <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="kamar_id" id="kamar_id" wire:change="hitungTotalHarga"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                        <option value="">Pilih kamar</option>
                                        @foreach($this->kamars as $kamar)
                                        <option value="{{ $kamar->id }}">{{ $kamar->nomor_kamar }} ({{
                                            $kamar->tipeKamar->nama_tipe ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    @error('kamar_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="pelanggan_id"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Pelanggan <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="pelanggan_id" id="pelanggan_id"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                        <option value="">Pilih pelanggan</option>
                                        @foreach($this->pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->id }}">{{ $pelanggan->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                    @error('pelanggan_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Check In/Out -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                Tanggal Reservasi
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="tanggal_check_in"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Tanggal Check In <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model.live="tanggal_check_in" type="date" id="tanggal_check_in"
                                        wire:change="hitungTanggalCheckout"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                    @error('tanggal_check_in')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tipe_paket"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Tipe Paket <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="tipe_paket" id="tipe_paket" wire:change="ubahTipePaket"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                        <option value="harian">Harian</option>
                                        <option value="mingguan">Mingguan</option>
                                        <option value="bulanan">Bulanan</option>
                                    </select>
                                    @error('tipe_paket')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="durasi"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Durasi <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model.live="durasi" type="number" min="1" id="durasi"
                                        wire:change="hitungTanggalCheckout" wire:input="hitungTotalHarga"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Jumlah {{ $unit_label }}
                                    </p>
                                    @error('durasi')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div class="col-span-3">
                                    <label for="tanggal_check_out"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        Tanggal Check Out <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model.live="tanggal_check_out" type="date" id="tanggal_check_out"
                                        class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg"
                                        readonly>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Tanggal checkout dihitung otomatis berdasarkan tipe paket dan durasi
                                    </p>
                                    @error('tanggal_check_out')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Total Harga -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                </div>
                                Informasi Harga
                            </h3>
                            <div>
                                <label for="total_harga"
                                    class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                    Total Harga <span class="text-red-500">*</span>
                                </label>
                                <div
                                    class="mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Tipe Paket:</span>
                                        {{ ucfirst($tipe_paket) }}
                                    </p>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Harga per {{ $unit_label }}:</span>
                                        Rp {{ number_format($harga_per_unit, 0, ',', '.') }}
                                    </p>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <span class="font-semibold">Durasi:</span> {{ $durasi }} {{ $unit_label }}
                                    </p>
                                </div>
                                <div
                                    class="block w-full px-4 py-3 border-0 rounded-2xl bg-gray-50/80 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none backdrop-blur-sm text-sm font-medium shadow-lg">
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                        Rp {{ number_format((float)$total_harga, 0, ',', '.') }}
                                    </span>
                                    <div x-data="{ total: @entangle('total_harga') }"
                                        class="text-xs text-gray-500 mt-1"></div>
                                </div>
                                <input wire:model="total_harga" type="hidden" id="total_harga">
                                @error('total_harga')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200/50 dark:border-gray-700/50">
                            <a href="{{ route('admin.reservasi.index') }}"
                                class="group px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 text-gray-700 dark:text-gray-200 font-bold rounded-2xl shadow-lg hover:shadow-gray-500/25 transition-all duration-300 transform hover:-translate-y-1">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </span>
                            </a>
                            <button type="submit"
                                class="group relative inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold rounded-2xl shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <svg class="relative z-10 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="relative z-10">Simpan Reservasi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>