<?php

use function Livewire\Volt\{
    layout, title, state, mount
};
use App\Models\FasilitasKamar;

layout('components.layouts.landing');
title('Fasilitas - Pondok Putri Apartment');

state([
    'fasilitas' => []
]);

mount(function() {
    $this->fasilitas = FasilitasKamar::all();
});

?>

<style>
    :root {
        --nav-h: 84px;
    }

    .facility-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #608BC1;
    }

    .facility-icon {
        background: linear-gradient(135deg, #608BC1 0%, #4a6a99 100%);
    }
</style>

<div class="text-slate-800">
    <!-- ===== HERO SECTION ===== -->
    <section
        class="relative min-h-[50vh] pt-[calc(var(--nav-h)+2rem)] overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-100 to-purple-100">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/20 via-slate-900/10 to-transparent"></div>

        <div class="relative z-10 container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Breadcrumb -->
                <nav class="flex items-center justify-center gap-2 text-sm text-slate-600 mb-6" data-aos="fade-down"
                    data-aos-duration="600">
                    <a href="{{ route('landingpage.home') }}" class="hover:text-[#133E87] transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                    </a>
                </nav>

                <!-- Headings -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight text-slate-900 mb-6"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    Fasilitas Unggulan
                </h1>

                <p class="text-lg md:text-xl text-slate-600 leading-relaxed max-w-2xl mx-auto" data-aos="fade-up"
                    data-aos-duration="800" data-aos-delay="300">
                    Nikmati berbagai fasilitas modern dan nyaman yang kami sediakan untuk kenyamanan Anda.
                </p>
            </div>
        </div>
    </section>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="bg-white">
        <div class="container mx-auto px-4 py-16">
            <!-- Facilities Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-duration="800">
                @forelse($this->fasilitas as $fasilitas)
                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-star text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                {{ $fasilitas->nama_fasilitas }}
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                {{ $fasilitas->deskripsi ?: 'Fasilitas unggulan untuk kenyamanan Anda.' }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <!-- Default Facilities -->
                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-wifi text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                WiFi Gratis
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Koneksi internet cepat dan stabil untuk kebutuhan online Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-parking text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                Parkir Luas
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Area parkir yang aman dan luas untuk kendaraan Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                24/7 Security
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Keamanan 24 jam untuk kenyamanan dan keamanan Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-tshirt text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                Laundry Service
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Layanan laundry untuk memudahkan kebutuhan pakaian Anda.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-utensils text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                Dapur Bersama
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Dapur lengkap untuk memasak dan menyiapkan makanan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="facility-card rounded-2xl p-6 group">
                    <div class="flex items-start gap-4">
                        <div
                            class="facility-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-couch text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <h3
                                class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#608BC1] transition-colors duration-300">
                                Ruang Tamu
                            </h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Ruang tamu yang nyaman untuk bersantai dan berkumpul.
                            </p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- CTA Section -->
            <div class="mt-16 text-center" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="bg-gradient-to-r from-[#608BC1] to-[#4a6a99] rounded-3xl p-8 text-white">
                    <h2 class="text-3xl font-bold mb-4">Siap untuk Menginap?</h2>
                    <p class="text-lg mb-6 text-white/90">
                        Nikmati semua fasilitas unggulan kami dengan melakukan reservasi sekarang.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('landingpage.contact') }}"
                            class="bg-white text-[#133E87] px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105">
                            Hubungi Kami
                        </a>
                        <a href="{{ route('landingpage.kamar') }}"
                            class="border-2 border-white text-white px-8 py-3 rounded-full font-bold hover:bg-white hover:text-[#133E87] transition-all duration-300 transform hover:scale-105">
                            Lihat Kamar
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