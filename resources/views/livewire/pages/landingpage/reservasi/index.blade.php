<?php

use function Livewire\Volt\{
    layout, title, state, mount, uses
};
use App\Models\Kamar;
use App\Models\Pelanggan;
use App\Helpers\ReservasiHelper;

layout('components.layouts.landing');
title('Reservasi - Pondok Putri Apartment');

state([
    'kode_reservasi' => '',
    'kamar_id' => '',
    'pelanggan_id' => '',
    'tipe_paket' => 'harian',
    'durasi' => 1,
    'tanggal_check_in' => '',
    'tanggal_check_out' => '',
    'total_harga' => '',
    'kamars' => [],
    'pelanggans' => [],
    'harga_per_unit' => 0,
    'unit_label' => 'hari',
    'isLoading' => false,
    'isLoggedIn' => false,
    'currentUserPelangganId' => null,
    'selectedKamar' => null
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

    // Check if user is logged in
    if (auth()->check()) {
        $this->isLoggedIn = true;
        $user = auth()->user();

        if ($user->pelanggan) {
            $this->currentUserPelangganId = $user->pelanggan->id;
            $this->pelanggan_id = $user->pelanggan->id;
        }
    }

    // Set default dates
    $this->tanggal_check_in = now()->format('Y-m-d');
    $this->tanggal_check_out = now()->addDay()->format('Y-m-d');

    // Check URL parameters
    if (request()->has('kamar')) {
        $kamarId = request()->get('kamar');
        $this->pilihKamar($kamarId);
    } elseif (request()->has('tipe')) {
        $tipeId = request()->get('tipe');
        $kamarDariTipe = $this->kamars->where('tipe_kamar_id', $tipeId)->first();
        if ($kamarDariTipe) {
            $this->pilihKamar($kamarDariTipe->id);
        }
    }

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

// Method untuk memilih kamar
$pilihKamar = function($kamarId) {
    $this->kamar_id = $kamarId;
    $this->selectedKamar = $this->kamars->find($kamarId);
    $this->hitungTotalHarga();
    $this->hitungTanggalCheckout();
};

// Method untuk ganti kamar
$gantiKamar = function() {
    $this->kamar_id = '';
    $this->selectedKamar = null;
    $this->harga_per_unit = 0;
    $this->total_harga = 0;
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

$ensureKodeReservasi = function() {
    if (empty($this->kode_reservasi)) {
        $this->kode_reservasi = ReservasiHelper::generateKodeReservasi();
    }
};

$refreshKodeReservasi = function() {
    $this->kode_reservasi = ReservasiHelper::generateKodeReservasi();
};

$save = function() {
    // Check login status
    if (!auth()->check()) {
        session()->flash('error', 'Anda harus login terlebih dahulu untuk melakukan reservasi.');
        return;
    }

    // Check pelanggan status
    if (!$this->currentUserPelangganId) {
        session()->flash('error', 'Akun Anda belum terdaftar sebagai pelanggan.');
        return;
    }

    // Ensure reservation code exists
    $this->ensureKodeReservasi();

    // Validasi overlap sebelum save
    if (!$this->validasiOverlap()) {
        return;
    }

    $this->validate([
        'kode_reservasi' => 'required|string|unique:reservasis,kode_reservasi',
        'kamar_id' => 'required|exists:kamars,id',
        'tipe_paket' => 'required|string|in:harian,mingguan,bulanan',
        'durasi' => 'required|integer|min:1',
        'tanggal_check_in' => 'required|date|after_or_equal:today',
        'tanggal_check_out' => 'required|date|after:tanggal_check_in',
        'total_harga' => 'required|numeric|min:0',
    ]);

    try {
        \App\Models\Reservasi::create([
            'kode_reservasi' => $this->kode_reservasi,
            'kamar_id' => $this->kamar_id,
            'pelanggan_id' => $this->currentUserPelangganId,
            'tipe_paket' => $this->tipe_paket,
            'durasi' => $this->durasi,
            'tanggal_check_in' => $this->tanggal_check_in,
            'tanggal_check_out' => $this->tanggal_check_out,
            'total_harga' => $this->total_harga,
            'status_reservasi' => 'pending',
        ]);

        session()->flash('message', 'Reservasi berhasil dibuat!');

        // Redirect ke halaman reservasi saya
        return redirect()->route('landingpage.reservasi-saya');

    } catch (\Exception $e) {
        \Log::error('Error creating reservasi:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        session()->flash('error', 'Gagal membuat reservasi: ' . $e->getMessage());
    }
};

?>

<div>
    <div class="text-slate-800">
        <!-- ===== HERO SECTION ===== -->
        <section
            class="relative min-h-[50vh] pt-[84px] overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/20 via-slate-900/10 to-transparent"></div>

            <div class="relative z-10 container mx-auto px-4 py-16">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center justify-center gap-2 text-sm text-slate-600 mb-6">
                        <a href="{{ route('landingpage.tipe-kamar') }}" class="hover:text-[#133E87] transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Tipe Kamar
                        </a>
                        @if(auth()->check() && auth()->user()->pelanggan)
                        <span class="text-slate-400">|</span>
                        <a href="{{ route('landingpage.reservasi-saya') }}"
                            class="hover:text-[#133E87] transition-colors">
                            <i class="fas fa-calendar-check mr-2"></i>Reservasi Saya
                        </a>
                        @endif
                    </nav>

                    <!-- Headings -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight text-slate-900 mb-6">
                        Reservasi Kamar
                    </h1>

                    <p class="text-lg md:text-xl text-slate-600 leading-relaxed max-w-2xl mx-auto">
                        Lengkapi form di bawah untuk melakukan reservasi kamar impian Anda.
                    </p>

                    @if(auth()->check() && auth()->user()->pelanggan)
                    <div class="mt-6">
                        <a href="{{ route('landingpage.reservasi-saya') }}"
                            class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm hover:bg-white text-[#133E87] px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg">
                            <i class="fas fa-calendar-check"></i>
                            Lihat Reservasi Saya
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="bg-white">
            <div class="container mx-auto px-4 py-16">
                <div class="grid lg:grid-cols-3 gap-12">
                    <!-- Left Column - Form -->
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-slate-200 p-8 relative">

                            <div class="relative">
                                <!-- Overlay untuk user yang tidak punya akses -->
                                @if($isLoggedIn && !$currentUserPelangganId)
                                <div class="access-overlay">
                                    <div class="access-overlay-content text-center p-8 max-w-md">
                                        <div
                                            class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <i class="fas fa-user-lock text-4xl text-yellow-600"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Akses Terbatas</h3>
                                        <p class="text-slate-600 mb-6">
                                            Akun Anda sudah login, tetapi belum terdaftar sebagai pelanggan.
                                            Hanya pelanggan yang dapat melakukan reservasi kamar.
                                        </p>
                                        <div class="space-y-3">
                                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                                <p class="text-sm text-blue-800">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    <strong>Untuk melanjutkan:</strong>
                                                </p>
                                                <ul class="text-sm text-blue-700 mt-2 space-y-1">
                                                    <li>• Hubungi admin untuk mendaftarkan akun Anda sebagai pelanggan
                                                    </li>
                                                    <li>• Atau gunakan akun yang sudah terdaftar sebagai pelanggan</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @elseif(!$isLoggedIn)
                                <div class="access-overlay">
                                    <div class="access-overlay-content text-center p-8 max-w-md">
                                        <div
                                            class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <i class="fas fa-sign-in-alt text-4xl text-blue-600"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Login Diperlukan</h3>
                                        <p class="text-slate-600 mb-6">
                                            Silakan login terlebih dahulu untuk melakukan reservasi kamar.
                                        </p>
                                        <a href="{{ route('login') }}"
                                            class="inline-flex items-center gap-2 bg-[#133E87] hover:bg-[#0f326e] text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                            <i class="fas fa-sign-in-alt"></i>
                                            Login Sekarang
                                        </a>
                                    </div>
                                </div>
                                @endif

                                <h2 class="text-2xl font-bold text-slate-900 mb-6">Form Reservasi</h2>

                                @if(session('message'))
                                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    {{ session('message') }}
                                </div>
                                @endif

                                @if(session('error'))
                                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ session('error') }}
                                </div>
                                @endif

                                <form wire:submit="save" class="space-y-8">
                                    <!-- Basic Information -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Dasar</h3>
                                        <div class="grid md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Kode
                                                    Reservasi
                                                    *</label>
                                                <div class="relative">
                                                    <input type="text" wire:model="kode_reservasi"
                                                        class="w-full px-4 py-3 pr-12 border border-slate-300 rounded-lg form-input focus:outline-none transition-all duration-300"
                                                        placeholder="Kode akan di-generate otomatis" readonly>
                                                    <button type="button" wire:click="refreshKodeReservasi"
                                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 p-2 text-slate-500 hover:text-blue-600 transition-colors"
                                                        title="Refresh kode reservasi">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </div>
                                                <p class="mt-2 text-xs text-slate-500">
                                                    Kode reservasi di-generate otomatis dengan format RSV + YYYY + MM +
                                                    4
                                                    digit nomor urut
                                                </p>
                                                @error('kode_reservasi') <span class="text-red-500 text-sm">{{ $message
                                                    }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Status
                                                    Reservasi</label>
                                                <div
                                                    class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    Pending (Default)
                                                </div>
                                                <p class="mt-2 text-xs text-slate-500">
                                                    Status reservasi akan otomatis diset sebagai "Pending"
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kamar dan Pelanggan Selection -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Pilihan Kamar & Pelanggan
                                        </h3>

                                        <!-- Daftar Kamar Tersedia -->
                                        @if($kamar_id)
                                        <!-- Jika kamar sudah dipilih, tampilkan hanya kamar yang dipilih -->
                                        <div class="mb-6">
                                            <p class="text-sm text-slate-600 mb-4">Kamar yang dipilih:</p>
                                            <div
                                                class="bg-slate-50 rounded-xl p-4 border-2 border-[#133E87] bg-blue-50">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-4">
                                                        <div
                                                            class="w-12 h-12 bg-[#133E87] rounded-xl flex items-center justify-center">
                                                            <i class="fas fa-door-open text-white text-lg"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-semibold text-slate-900 text-lg">{{
                                                                $selectedKamar->nomor_kamar }}</h4>
                                                            <p class="text-slate-600">{{
                                                                $selectedKamar->tipeKamar->nama_tipe ?? 'Tipe Kamar' }}
                                                            </p>
                                                            <p class="text-sm text-slate-500">Lantai {{
                                                                $selectedKamar->lantai ?? '1' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#133E87] text-white">
                                                                <i class="fas fa-check-circle mr-1"></i>Dipilih
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('kamar_id') <span class="text-red-500 text-sm mt-2 block">{{ $message
                                                }}</span> @enderror
                                        </div>
                                        @else
                                        <!-- Jika belum ada kamar yang dipilih, tampilkan semua opsi -->
                                        <div class="mb-6">
                                            <p class="text-sm text-slate-600 mb-4">Pilih kamar yang ingin Anda booking:
                                            </p>
                                            <div class="grid gap-4">
                                                @foreach($this->kamars as $kamar)
                                                <div class="relative">
                                                    <div class="bg-slate-50 rounded-xl p-4 border-2 transition-all duration-300 cursor-pointer hover:shadow-lg border-slate-200 hover:border-slate-300"
                                                        wire:click="pilihKamar({{ $kamar->id }})">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-4">
                                                                <div
                                                                    class="w-12 h-12 bg-slate-200 rounded-xl flex items-center justify-center transition-colors duration-300">
                                                                    <i
                                                                        class="fas fa-door-open text-slate-600 text-lg"></i>
                                                                </div>
                                                                <div>
                                                                    <h4 class="font-semibold text-slate-900 text-lg">{{
                                                                        $kamar->nomor_kamar }}</h4>
                                                                    <p class="text-slate-600">{{
                                                                        $kamar->tipeKamar->nama_tipe ?? 'Tipe Kamar' }}
                                                                    </p>
                                                                    <p class="text-sm text-slate-500">Lantai {{
                                                                        $kamar->lantai ?? '1' }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <span
                                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        <i class="fas fa-check-circle mr-1"></i>Tersedia
                                                                    </span>
                                                                </div>
                                                                <div class="text-sm text-slate-600">
                                                                    Klik untuk memilih
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @error('kamar_id') <span class="text-red-500 text-sm mt-2 block">{{ $message
                                                }}</span> @enderror
                                        </div>
                                        @endif

                                        <!-- Informasi Pelanggan -->
                                        <div class="grid md:grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-slate-700 mb-2">Pelanggan</label>
                                                <div
                                                    class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                                    @if($currentUserPelangganId)
                                                    <i class="fas fa-user mr-2"></i>
                                                    {{ auth()->user()->name ?? 'User' }} (Otomatis)
                                                    @else
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    Belum terdaftar sebagai pelanggan
                                                    @endif
                                                </div>
                                                <p class="mt-2 text-xs text-slate-500">
                                                    @if($currentUserPelangganId)
                                                    Menggunakan data pelanggan dari akun yang login
                                                    @else
                                                    Silakan hubungi admin untuk mendaftarkan akun Anda sebagai pelanggan
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tanggal Check In/Out -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Tanggal Reservasi</h3>
                                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p class="text-sm text-blue-800">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Fitur Otomatis:</strong> Tanggal checkout dan harga akan
                                                dihitung
                                                otomatis berdasarkan tipe paket dan durasi yang Anda pilih.
                                            </p>
                                        </div>
                                        <div class="grid md:grid-cols-3 gap-4">
                                            <!-- Tanggal Check In -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal
                                                    Check
                                                    In *</label>
                                                <input type="date" wire:model.live="tanggal_check_in"
                                                    wire:change="hitungTanggalCheckout"
                                                    min="{{ now()->format('Y-m-d') }}"
                                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg form-input focus:outline-none transition-all duration-300 date-input">
                                                @error('tanggal_check_in')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Tipe Paket -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Paket
                                                    *</label>
                                                <select wire:model.live="tipe_paket" wire:change="ubahTipePaket"
                                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg form-input focus:outline-none transition-all duration-300">
                                                    <option value="harian">Harian</option>
                                                    <option value="mingguan">Mingguan</option>
                                                    <option value="bulanan">Bulanan</option>
                                                </select>
                                                <p class="mt-2 text-xs text-slate-500">Pilih tipe paket untuk melihat
                                                    harga
                                                    yang sesuai</p>
                                                @error('tipe_paket')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Durasi -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Durasi
                                                    *</label>
                                                <input type="number" min="1" wire:model.live="durasi"
                                                    wire:change="hitungTanggalCheckout" wire:input="hitungTotalHarga"
                                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg form-input focus:outline-none transition-all duration-300">
                                                <p class="mt-2 text-xs text-slate-500">Jumlah {{ $unit_label }} - Harga
                                                    akan
                                                    dihitung otomatis</p>
                                                @error('durasi')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Check
                                                Out
                                                *</label>
                                            <input type="date" wire:model="tanggal_check_out"
                                                class="w-full px-4 py-3 border border-slate-300 rounded-lg form-input focus:outline-none transition-all duration-300 date-input"
                                                readonly>
                                            <p class="mt-2 text-xs text-slate-500">Tanggal checkout dihitung otomatis
                                                berdasarkan tipe paket dan durasi</p>
                                            @error('tanggal_check_out') <span class="text-red-500 text-sm">{{ $message
                                                }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-6">
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-3 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-8 py-4 text-white font-medium text-lg transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                                            wire:loading.attr="disabled" wire:loading.class="opacity-50" {{ !$isLoggedIn
                                            || !$currentUserPelangganId ? 'disabled' : '' }}>

                                            <span wire:loading.remove>
                                                <i class="fas fa-calendar-check"></i>
                                                Buat Reservasi
                                            </span>

                                            <span wire:loading>
                                                <i class="fas fa-spinner fa-spin"></i>
                                                Memproses...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Summary -->
                    <div class="space-y-8">
                        <!-- Price Summary Card -->
                        @if($kamar_id && $harga_per_unit > 0)
                        <div class="bg-gradient-to-br from-blue-600 to-purple-700 rounded-2xl p-6 text-white">
                            <h3 class="text-xl font-bold mb-4">Ringkasan Harga</h3>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span>Harga per {{ $unit_label }}</span>
                                    <span>Rp {{ number_format($harga_per_unit, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Jumlah {{ $unit_label }}</span>
                                    <span>{{ $durasi }} {{ $unit_label }}</span>
                                </div>
                                <div class="border-t border-white/20 pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span>Rp {{ number_format((float)$total_harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-sm text-white/80">
                                <p><i class="fas fa-info-circle mr-2"></i>Harga sudah termasuk semua fasilitas</p>
                                <p class="mt-2"><i class="fas fa-calculator mr-2"></i>Update otomatis saat mengubah tipe
                                    paket atau durasi</p>
                                <p class="mt-2"><i class="fas fa-sync-alt mr-2"></i>Perubahan langsung terlihat di sini
                                </p>
                            </div>
                        </div>
                        @elseif($kamar_id)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                            <h3 class="text-xl font-bold mb-4 text-yellow-800">Harga Belum Tersedia</h3>
                            <div class="text-yellow-700">
                                <p class="mb-3">Pilih tipe paket dan durasi untuk melihat harga yang sesuai.</p>
                                <div class="text-sm space-y-1">
                                    <p><strong>Tipe Paket:</strong> {{ $tipe_paket }}</p>
                                    <p><strong>Durasi:</strong> {{ $durasi }} {{ $unit_label }}</p>
                                    <p><strong>Kamar ID:</strong> {{ $kamar_id }}</p>
                                    @if($selectedKamar)
                                    <p><strong>Nomor Kamar:</strong> {{ $selectedKamar->nomor_kamar }}</p>
                                    <p><strong>Tipe Kamar:</strong> {{ $selectedKamar->tipeKamar->nama_tipe ?? 'N/A' }}
                                    </p>
                                    <p><strong>Harga Tersedia:</strong>
                                        @if($selectedKamar->hargas->count() > 0)
                                        @foreach($selectedKamar->hargas as $harga)
                                        {{ $harga->tipe_paket }}: Rp {{ number_format($harga->harga, 0, ',', '.') }}{{
                                        !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        @else
                                        Tidak ada harga
                                        @endif
                                    </p>
                                    @endif
                                </div>
                                <div class="mt-4 p-3 bg-yellow-100 rounded-lg">
                                    <p class="text-xs text-yellow-800">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        <strong>Tips:</strong> Harga akan muncul otomatis setelah Anda memilih tipe
                                        paket
                                        dan durasi yang sesuai dengan data harga yang tersedia.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Contact Info Card -->
                        <div class="bg-slate-50/80 backdrop-blur-sm rounded-2xl p-6 border border-slate-200">
                            <h3 class="text-xl font-bold text-slate-900 mb-4">Butuh Bantuan?</h3>
                            <p class="text-slate-600 mb-4">Tim kami siap membantu Anda dengan reservasi.</p>
                            <a href="https://wa.me/6281234567890?text=Saya ingin bertanya tentang reservasi"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 hover:bg-green-700 px-6 py-3 text-white font-medium transition-all duration-300">
                                <i class="fab fa-whatsapp"></i>
                                Chat WhatsApp
                            </a>
                        </div>

                        <!-- Terms Card -->
                        <div class="bg-slate-50/80 backdrop-blur-sm rounded-2xl p-6 border border-slate-200">
                            <h3 class="text-xl font-bold text-slate-900 mb-4">Syarat & Ketentuan</h3>
                            <div class="text-sm text-slate-600 space-y-2">
                                <p><i class="fas fa-check text-green-500 mr-2"></i>Check-in: 14:00 WIB</p>
                                <p><i class="fas fa-check text-green-500 mr-2"></i>Check-out: 12:00 WIB</p>
                                <p><i class="fas fa-check text-green-500 mr-2"></i>Pembayaran di muka</p>
                                <p><i class="fas fa-check text-green-500 mr-2"></i>Pembatalan 24 jam sebelumnya</p>
                            </div>
                        </div>

                        <!-- Access Info Card - Tampilkan hanya jika user tidak punya akses -->
                        @if(!$isLoggedIn || !$currentUserPelangganId)
                        <div
                            class="bg-gradient-to-br from-amber-50/90 to-orange-50/90 backdrop-blur-sm rounded-2xl p-6 border border-amber-200">
                            <h3 class="text-xl font-bold text-amber-800 mb-4">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Informasi Akses
                            </h3>

                            @if(!$isLoggedIn)
                            <div class="text-amber-700 mb-4">
                                <p class="mb-3"><strong>Status:</strong> Belum Login</p>
                                <p class="text-sm">Anda harus login terlebih dahulu untuk melakukan reservasi.</p>
                            </div>
                            <a href="{{ route('login') }}"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-700 px-6 py-3 text-white font-medium transition-all duration-300">
                                <i class="fas fa-sign-in-alt"></i>
                                Login Sekarang
                            </a>
                            @else
                            <div class="text-amber-700 mb-4">
                                <p class="mb-3"><strong>Status:</strong> Sudah Login</p>
                                <p class="text-sm">Akun Anda belum terdaftar sebagai pelanggan.</p>
                                <div class="mt-3 p-3 bg-amber-100 rounded-lg">
                                    <p class="text-xs text-amber-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Solusi:</strong> Hubungi admin untuk mendaftarkan akun Anda sebagai
                                        pelanggan.
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-input:focus {
            border-color: #133E87;
            box-shadow: 0 0 0 3px rgba(19, 62, 135, 0.1);
        }

        .date-input::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
        }

        /* Access Overlay dengan efek blur */
        .access-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 1rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .access-overlay-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* Efek blur tambahan untuk elemen lain */
        .blur-effect {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        .blur-effect-strong {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</div>