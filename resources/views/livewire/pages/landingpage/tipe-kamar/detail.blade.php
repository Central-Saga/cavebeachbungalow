<?php

use function Livewire\Volt\{ layout, title, state, mount };
use App\Models\TipeKamar;

layout('components.layouts.landing');
title('Detail Tipe Kamar - Cave Beach Bungalow');

state(['tipeKamar' => null, 'galeriKamar' => [], 'fasilitasKamar' => [], 'hargaKamar' => null]);

mount(function($id) {
    $this->tipeKamar = TipeKamar::with([
        'galeriKamars',
        'fasilitasKamars',
        'hargas'
    ])->findOrFail($id);

    if ($this->tipeKamar) {
        $this->galeriKamar = $this->tipeKamar->galeriKamars;
        $this->fasilitasKamar = $this->tipeKamar->fasilitasKamars;
        $this->hargaKamar = $this->tipeKamar->hargas->first();
    }
});

?>

<style>
    :root {
        --nav-h: 84px;
    }

    .gallery-image {
        transition: all 0.3s ease;
    }

    .gallery-image:hover {
        transform: scale(1.05);
    }

    .spec-item {
        transition: all 0.3s ease;
    }

    .spec-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="text-slate-800">
    <!-- ===== HERO SECTION ===== -->
    <section
        class="relative min-h-[60vh] pt-[calc(var(--nav-h)+2rem)] overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/20 via-slate-900/10 to-transparent"></div>

        <div class="relative z-10 container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-slate-600 mb-6" data-aos="fade-down"
                    data-aos-duration="600">
                    <a href="{{ route('landingpage.tipe-kamar') }}" class="hover:text-[#133E87] transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Tipe Kamar
                    </a>
                </nav>

                <!-- Headings -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight text-slate-900 mb-6"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    {{ $tipeKamar->nama_tipe }}
                </h1>

                <p class="text-lg md:text-xl text-slate-600 leading-relaxed max-w-3xl mb-8" data-aos="fade-up"
                    data-aos-duration="800" data-aos-delay="300">
                    {{ $tipeKamar->deskripsi }}
                </p>

                <!-- Quick Actions -->
                <div class="flex flex-wrap items-center gap-4" data-aos="fade-up" data-aos-duration="800"
                    data-aos-delay="400">
                    <a href="{{ route('landingpage.reservasi') }}?tipe={{ $tipeKamar->id }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-6 py-3 text-white font-medium transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-calendar-check"></i>
                        Booking Sekarang
                    </a>
                    <a href="https://wa.me/6283114380118?text=Saya tertarik dengan {{ $tipeKamar->nama_tipe }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-green-600 hover:bg-green-700 px-6 py-3 text-white font-medium transition-all duration-300 transform hover:scale-105">
                        <i class="fab fa-whatsapp"></i>
                        Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="bg-white">
        <div class="container mx-auto px-4 py-16">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- Gallery Section -->
                    @if($galeriKamar && count($galeriKamar) > 0)
                    <section data-aos="fade-up" data-aos-duration="800">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Galeri Kamar</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($galeriKamar as $index => $gambar)
                            <div class="aspect-square rounded-xl overflow-hidden shadow-lg">
                                @php
                                $imageUrl = $gambar->url_foto;
                                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                $imgSrc = $imageUrl;
                                } else {
                                $cleanPath = ltrim($imageUrl, '/');
                                if (strpos($cleanPath, 'storage/') === 0) {
                                $cleanPath = substr($cleanPath, 8);
                                }
                                $imgSrc = asset('storage/' . $cleanPath);
                                }
                                @endphp
                                <img src="{{ $imgSrc }}" alt="{{ $tipeKamar->nama_tipe }} - {{ $index + 1 }}"
                                    class="w-full h-full object-cover gallery-image">
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    <!-- Description Section -->
                    <section data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Deskripsi Lengkap</h2>
                        <div class="prose prose-slate max-w-none">
                            <p class="text-slate-600 leading-relaxed text-lg">
                                {{ $tipeKamar->deskripsi }}
                            </p>
                        </div>
                    </section>

                    <!-- Available Rooms Section -->
                    <section data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Daftar Kamar Tersedia</h2>
                        <div class="grid gap-4">
                            @php
                            // Ambil kamar yang memiliki tipe ini
                            $availableRooms = \App\Models\Kamar::where('tipe_kamar_id', $tipeKamar->id)->get();
                            @endphp

                                                        @if($availableRooms && count($availableRooms) > 0)
                            @foreach($availableRooms as $kamar)
                            <div
                                class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-[#133E87] rounded-xl flex items-center justify-center">
                                            <i class="fas fa-door-open text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-slate-900 text-lg">{{ $kamar->nomor_kamar }}
                                            </h4>
                                            @php
                                                // Check room availability status
                                                $isAvailable = true; // You can implement your availability logic here
                                                $statusText = $isAvailable ? 'Tersedia' : 'Tidak Tersedia';
                                                $statusClass = $isAvailable ? 'text-green-600' : 'text-red-600';
                                            @endphp
                                            <p class="{{ $statusClass }}">{{ $statusText }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            // Get room price - prioritize harian first, then other types
                                            $hargaKamar = $kamar->hargas()->where('tipe_paket', 'harian')->first() 
                                                        ?? $kamar->hargas()->first();
                                        @endphp
                                        @if($hargaKamar)
                                        <div class="mb-2">
                                            <div class="text-xl font-bold text-[#133E87]">
                                                Rp {{ number_format($hargaKamar->harga, 0, ',', '.') }}
                                            </div>
                                            <p class="text-sm text-slate-600">per {{ $hargaKamar->tipe_paket === 'harian' ? 'hari' : ($hargaKamar->tipe_paket === 'mingguan' ? 'minggu' : 'bulan') }}</p>
                                        </div>
                                        @else
                                        <div class="mb-2">
                                            <div class="text-lg font-medium text-slate-500">
                                                Harga belum diset
                                            </div>
                                        </div>
                                        @endif
                                        @if($isAvailable)
                                        <a href="{{ route('landingpage.reservasi') }}?kamar={{ $kamar->id }}"
                                            class="inline-flex items-center gap-2 rounded-lg bg-[#133E87] hover:bg-[#0f326e] px-4 py-2 text-white text-sm font-medium transition-all duration-300 transform hover:scale-105">
                                            <i class="fas fa-calendar-check"></i>
                                            Booking
                                        </a>
                                        @else
                                        <span class="inline-flex items-center gap-2 rounded-lg bg-slate-400 px-4 py-2 text-white text-sm font-medium cursor-not-allowed">
                                            <i class="fas fa-lock"></i>
                                            Tidak Tersedia
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <!-- Fallback jika tidak ada kamar spesifik -->
                            <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 text-center">
                                <div
                                    class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-home text-blue-600 text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-slate-900 text-lg mb-2">Unit Tersedia</h4>
                                <p class="text-slate-600 mb-2">Tipe kamar ini tersedia untuk booking</p>
                                @if($hargaKamar)
                                <div class="mb-4">
                                    <div class="text-2xl font-bold text-[#133E87]">
                                        Rp {{ number_format($hargaKamar->harga, 0, ',', '.') }}
                                    </div>
                                    <p class="text-sm text-slate-600">per {{ $hargaKamar->tipe_paket === 'harian' ? 'hari' : ($hargaKamar->tipe_paket === 'mingguan' ? 'minggu' : 'bulan') }}</p>
                                </div>
                                @endif
                                <a href="{{ route('landingpage.reservasi') }}?tipe={{ $tipeKamar->id }}"
                                    class="inline-flex items-center gap-2 rounded-lg bg-[#133E87] hover:bg-[#0f326e] px-6 py-3 text-white font-medium transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-calendar-check"></i>
                                    Booking Sekarang
                                </a>
                            </div>
                            @endif
                        </div>
                    </section>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-8">
                    <!-- Price Card -->
                    @if($hargaKamar)
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200" data-aos="fade-left"
                        data-aos-duration="800">
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Harga</h3>
                        <div class="text-3xl font-black text-[#133E87] mb-2">
                            Rp {{ number_format($hargaKamar->harga, 0, ',', '.') }}
                        </div>
                        <p class="text-slate-600 mb-6">per malam</p>
                        <a href="{{ route('landingpage.reservasi') }}?tipe={{ $tipeKamar->id }}"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-6 py-3 text-white font-medium transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-calendar-check"></i>
                            Booking Sekarang
                        </a>
                    </div>
                    @endif

                    <!-- Facilities Card -->
                    @if($fasilitasKamar && count($fasilitasKamar) > 0)
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200" data-aos="fade-left"
                        data-aos-duration="800" data-aos-delay="100">
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Fasilitas</h3>
                        <div class="space-y-3">
                            @foreach($fasilitasKamar as $fasilitas)
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                                <span class="text-slate-700">{{ $fasilitas->nama_fasilitas }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Contact Card -->
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200" data-aos="fade-left"
                        data-aos-duration="800" data-aos-delay="200">
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Butuh Bantuan?</h3>
                        <p class="text-slate-600 mb-4">Tim kami siap kapanpun anda membutuhkan kami</p>
                        <a href="https://wa.me/6283114380118?text=Halo%20Cave%20Beach%20Bungalow%2C%20saya%20mau%20bertanya%20tentang%20{{ urlencode($tipeKamar->nama_tipe) }}" 
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 hover:bg-green-700 px-6 py-3 text-white font-medium transition-all duration-300">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
        const ready = (cb) => (document.readyState === 'loading')
            ? document.addEventListener('DOMContentLoaded', cb)
            : cb();

        // sinkronkan --nav-h dengan tinggi navbar aktual
        const syncNavH = () => {
            const n = document.getElementById('main-navbar');
            if (!n) return;
            document.documentElement.style.setProperty('--nav-h', n.offsetHeight + 'px');
        };
        addEventListener('load', syncNavH);
        addEventListener('resize', syncNavH);

        ready(() => {
            // Inisialisasi AOS
            if (window.AOS) {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100
                });
            }

            // Gallery image click to enlarge
            const galleryImages = document.querySelectorAll('.gallery-image');
            galleryImages.forEach(img => {
                img.addEventListener('click', function() {
                    // Create modal for enlarged image
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 bg-black/80 flex items-center justify-center z-50 cursor-pointer';
                    modal.innerHTML = `
                        <img src="${this.src}" alt="${this.alt}" class="max-w-[90vw] max-h-[90vh] object-contain">
                    `;

                    modal.addEventListener('click', () => modal.remove());
                    document.body.appendChild(modal);
                });
            });
        });

        // Re-init setelah Livewire navigate
        document.addEventListener('livewire:navigated', () => {
            syncNavH();
            if (window.AOS) {
                AOS.refresh();
            }
        });
    })();
</script>