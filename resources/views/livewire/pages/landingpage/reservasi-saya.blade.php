<?php

use function Livewire\Volt\{
    layout, title, state, mount, uses
};
use Livewire\WithFileUploads;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;

layout('components.layouts.landing');
title('Reservasi Saya - Cave Beach Bungalow');

uses([WithFileUploads::class]);

state([
    'reservasis' => [],
    'selectedReservasi' => null,
    'showPaymentModal' => false,
    'nominal' => '',
    'bukti_transfer' => null,
    'keterangan' => '',
    'isLoading' => false
]);

mount(function() {
    if (auth()->check() && auth()->user()->pelanggan) {
        $this->reservasis = Reservasi::with(['kamar.tipeKamar', 'pembayarans'])
            ->where('pelanggan_id', auth()->user()->pelanggan->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
});

$selectReservasi = function($reservasiId) {
    $this->selectedReservasi = $this->reservasis->find($reservasiId);
    $this->nominal = $this->selectedReservasi->sisa_bayar;
    $this->showPaymentModal = true;
};

$closePaymentModal = function() {
    $this->showPaymentModal = false;
    $this->selectedReservasi = null;
    $this->reset(['nominal', 'bukti_transfer', 'keterangan']);
};

$uploadBuktiPembayaran = function() {
    $this->validate([
        'nominal' => 'required|numeric|min:1000',
        'bukti_transfer' => 'required|image|max:2048',
        'keterangan' => 'nullable|string|max:500',
    ]);

    $this->isLoading = true;

    try {
        // Upload file
        $path = $this->bukti_transfer->store('bukti-pembayaran', 'public');

        // Buat record pembayaran
        Pembayaran::create([
            'reservasi_id' => $this->selectedReservasi->id,
            'nominal' => $this->nominal,
            'bukti_path' => $path,
            'status' => 'menunggu',
            'keterangan' => $this->keterangan,
        ]);

        // Refresh data reservasi
        $this->reservasis = Reservasi::with(['kamar.tipeKamar', 'pembayarans'])
            ->where('pelanggan_id', auth()->user()->pelanggan->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->closePaymentModal();
        session()->flash('message', 'Bukti pembayaran berhasil diupload dan menunggu verifikasi admin.');

    } catch (\Exception $e) {
        session()->flash('error', 'Gagal upload bukti pembayaran: ' . $e->getMessage());
    } finally {
        $this->isLoading = false;
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
                        <a href="{{ route('landingpage.home') }}" class="hover:text-[#133E87] transition-colors">
                            <i class="fas fa-home mr-2"></i>Beranda
                        </a>
                        <span class="text-slate-400">/</span>
                        <span class="text-slate-600">Reservasi Saya</span>
                    </nav>

                    <!-- Headings -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight text-[#133E87] mb-6">
                        Reservasi Saya
                    </h1>

                    <p class="text-lg md:text-xl text-slate-600 leading-relaxed max-w-2xl mx-auto">
                        Kelola dan pantau semua reservasi kamar Anda di sini.
                    </p>
                </div>
            </div>
        </section>

        <!-- ===== MAIN CONTENT ===== -->
        <div class="bg-white">
            <div class="container mx-auto px-4 py-16">
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

                @if(!auth()->check())
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-lock text-4xl text-slate-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-[#133E87] mb-4">Login Diperlukan</h3>
                    <p class="text-slate-600 mb-6">Silakan login terlebih dahulu untuk melihat reservasi Anda.</p>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 bg-[#133E87] hover:bg-[#0f326e] text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                </div>
                @elseif(!auth()->user()->pelanggan)
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-plus text-4xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-[#133E87] mb-4">Akun Belum Terdaftar</h3>
                    <p class="text-slate-600 mb-6">Akun Anda belum terdaftar sebagai pelanggan. Silakan hubungi admin.
                    </p>
                    <a href="{{ route('landingpage.home') }}"
                        class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                </div>
                @elseif($reservasis->isEmpty())
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-calendar-times text-4xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-[#133E87] mb-4">Belum Ada Reservasi</h3>
                    <p class="text-slate-600 mb-6">Anda belum memiliki reservasi kamar. Mulai buat reservasi pertama
                        Anda!</p>
                    <a href="{{ route('landingpage.reservasi') }}"
                        class="inline-flex items-center gap-2 bg-[#133E87] hover:bg-[#0f326e] text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-calendar-plus"></i>
                        Buat Reservasi
                    </a>
                </div>
                @else
                <!-- Daftar Reservasi -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-[#133E87]">Daftar Reservasi</h2>
                        <a href="{{ route('landingpage.reservasi') }}"
                            class="inline-flex items-center gap-2 bg-[#133E87] hover:bg-[#0f326e] text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus"></i>
                            Reservasi Baru
                        </a>
                    </div>

                    @foreach($reservasis as $reservasi)
                    <div
                        class="bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <!-- Header Reservasi -->
                        <div class="px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $reservasi->kode_reservasi
                                        }}</h3>
                                    <p class="text-slate-600">{{ $reservasi->kamar->nomor_kamar }} - {{
                                        $reservasi->kamar->tipeKamar->nama_tipe }}</p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $reservasi->status_reservasi === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($reservasi->status_reservasi === 'terkonfirmasi' ? 'bg-green-100 text-green-800' : ($reservasi->status_reservasi === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        <i
                                            class="{{ $reservasi->status_reservasi === 'pending' ? 'fas fa-clock' : ($reservasi->status_reservasi === 'terkonfirmasi' ? 'fas fa-check-circle' : ($reservasi->status_reservasi === 'cancelled' ? 'fas fa-times-circle' : 'fas fa-question-circle')) }} mr-2"></i>
                                        {{ ucfirst($reservasi->status_reservasi) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Reservasi -->
                        <div class="px-6 py-4">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-3">Informasi Reservasi</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">Check-in:</span>
                                            <span class="font-medium">{{
                                                \Carbon\Carbon::parse($reservasi->tanggal_check_in)->format('d M Y')
                                                }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">Check-out:</span>
                                            <span class="font-medium">{{
                                                \Carbon\Carbon::parse($reservasi->tanggal_check_out)->format('d M Y')
                                                }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">Durasi:</span>
                                            <span class="font-medium">{{ $reservasi->durasi }} {{ $reservasi->tipe_paket
                                                }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">Total Harga:</span>
                                            <span class="font-medium text-lg text-[#133E87]">Rp {{
                                                number_format($reservasi->total_harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-3">Status Pembayaran</h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-600">Total Bayar:</span>
                                            <span class="font-medium text-green-600">Rp {{
                                                number_format($reservasi->total_bayar, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-600">Sisa Bayar:</span>
                                            <span
                                                class="font-medium {{ $reservasi->sisa_bayar > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                Rp {{ number_format($reservasi->sisa_bayar, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-slate-600">Status:</span>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $reservasi->lunas ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                <i
                                                    class="fas {{ $reservasi->lunas ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                                {{ $reservasi->lunas ? 'Lunas' : 'Belum Lunas' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Pembayaran -->
                            @if($reservasi->pembayarans->count() > 0)
                            <div class="mt-6 pt-4 border-t border-slate-200">
                                <h4 class="font-semibold text-slate-900 mb-3">Riwayat Pembayaran</h4>
                                <div class="space-y-2">
                                    @foreach($reservasi->pembayarans as $pembayaran)
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-medium">Rp {{ number_format($pembayaran->nominal,
                                                0, ',', '.') }}</span>
                                            <span class="text-xs text-slate-500">{{
                                                \Carbon\Carbon::parse($pembayaran->created_at)->format('d M Y H:i')
                                                }}</span>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pembayaran->status === 'menunggu' ? 'bg-blue-100 text-blue-800' : ($pembayaran->status === 'terverifikasi' ? 'bg-green-100 text-green-800' : ($pembayaran->status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            <i
                                                class="{{ $pembayaran->status === 'menunggu' ? 'fas fa-clock' : ($pembayaran->status === 'terverifikasi' ? 'fas fa-check-circle' : ($pembayaran->status === 'ditolak' ? 'fas fa-times-circle' : 'fas fa-question-circle')) }} mr-1"></i>
                                            {{ ucfirst($pembayaran->status) }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="mt-6 pt-4 border-t border-slate-200">
                                <div class="flex items-center gap-3">
                                    @if($reservasi->sisa_bayar > 0)
                                    <button wire:click="selectReservasi({{ $reservasi->id }})"
                                        class="inline-flex items-center gap-2 bg-[#133E87] hover:bg-[#0f326e] text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-credit-card"></i>
                                        Bayar Sekarang
                                    </button>
                                    @endif
                                    <a href="{{ route('landingpage.reservasi.detail', $reservasi->id) }}"
                                        class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg font-medium transition-colors">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @if($showPaymentModal)
    <div class="fixed inset-0 backdrop-blur-md bg-white/20 flex items-center justify-center z-50">
        <div class="bg-white/95 backdrop-blur-sm rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl border border-white/20">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Upload Bukti Pembayaran</h3>
                <button wire:click="closePaymentModal" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form wire:submit.prevent="uploadBuktiPembayaran">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nominal Pembayaran</label>
                        <input type="text" wire:model="nominal" inputmode="numeric"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#133E87] focus:border-transparent"
                            placeholder="Contoh: 500000"
                            oninput="this.value = Math.round(this.value.replace(/[^0-9.]/g, '')); if(this.value < 1000) this.value = 1000;">
                        @error('nominal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Informasi Rekening -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-3">Informasi Rekening</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Bank:</span>
                                <span class="font-medium text-blue-900">Bank Central Asia (BCA)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">No. Rekening:</span>
                                <span class="font-medium text-blue-900">1234567890</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Atas Nama:</span>
                                <span class="font-medium text-blue-900">PT. CAVE BEACH BUNGALOW</span>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-blue-100 rounded border border-blue-200">
                            <p class="text-xs text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Silakan transfer sesuai nominal yang diinput di atas
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Bukti Transfer</label>
                        <input type="file" wire:model="bukti_transfer" accept="image/*"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#133E87] focus:border-transparent">
                        @error('bukti_transfer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Keterangan (Opsional)</label>
                        <textarea wire:model="keterangan" rows="3"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#133E87] focus:border-transparent"
                            placeholder="Tambahkan keterangan pembayaran..."></textarea>
                        @error('keterangan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button type="button" wire:click="closePaymentModal"
                            class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2 bg-[#133E87] hover:bg-[#0f326e] text-white rounded-lg font-medium transition-colors disabled:opacity-50">
                            <span wire:loading.remove>Upload Bukti</span>
                            <span wire:loading>Uploading...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>