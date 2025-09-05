<?php

use function Livewire\Volt\{ layout, title, state, mount };
use App\Models\TipeKamar;

layout('components.layouts.landing');
title('Tipe Kamar - Pondok Putri Apartment');

state(['tipeKamar' => []]);

mount(function() {
    $this->tipeKamar = TipeKamar::with([
        'galeriKamars',
        'spekKamars',
        'fasilitasKamars',
        'hargas'
    ])->get();

    // Debug: log data yang di-load
    \Log::info('TipeKamar loaded in landing page:', [
        'count' => $this->tipeKamar->count(),
        'data' => $this->tipeKamar->toArray()
    ]);
});

?>

<style>
    :root {
        --nav-h: 84px;
    }

    .reveal {
        opacity: 0;
        transform: translateY(24px);
        will-change: transform, opacity;
    }

    /* Room card image styling */
    .room-card img {
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .room-card img:hover {
        transform: scale(1.1);
    }

    /* Fallback styling */
    .room-fallback {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border: 2px dashed #cbd5e1;
    }

    /* Debug info styling */
    .debug-info {
        font-family: monospace;
        font-size: 10px;
        max-width: 200px;
        word-break: break-all;
    }
</style>

<div class="text-slate-800">
    <!-- ===== HERO SECTION ===== -->
    <section
        class="relative min-h-[calc(100svh-var(--nav-h))] md:min-h-[calc(100dvh-var(--nav-h))] pt-[calc(var(--nav-h)+2rem)] overflow-hidden bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('img/asset/5.jpg') }}');">
        <!-- Background overlay untuk readability -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 via-slate-900/60 to-slate-900/40"></div>
        <!-- Subtle pattern overlay -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=" 60" height="60" viewBox="0 0 60 60"
            xmlns="http://www.w3.org/2000/svg" %3E%3Cg fill="none" fill-rule="evenodd" %3E%3Cg fill="%23ffffff"
            fill-opacity="0.03" %3E%3Ccircle cx="30" cy="30" r="2" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>

        <div class="relative z-10 container mx-auto px-4 pb-16 md:pb-24">
            <div class="max-w-4xl text-center mx-auto">
                <!-- Badge -->
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-sm px-4 py-2 text-sm font-medium border border-white/30 mb-6"
                    data-aos="fade-down" data-aos-duration="800" data-aos-delay="100">
                    <i class="fas fa-building text-blue-300"></i>
                    Tipe Kamar
                </span>

                <!-- Headings -->
                <h1 class="text-[clamp(2rem,6vw,4.5rem)] md:text-6xl lg:text-7xl font-black leading-tight text-white mb-6"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    Pilihan Unit
                </h1>

                <p class="text-lg md:text-xl lg:text-2xl text-white/90 leading-relaxed max-w-2xl mx-auto"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                    Berbagai tipe unit dengan desain modern dan fasilitas lengkap untuk kenyamanan maksimal.
                </p>

                <!-- Debug Info -->
                <div class="mt-8 p-4 bg-white/20 backdrop-blur-sm rounded-lg max-w-md mx-auto border border-white/30"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    <p class="text-sm text-white">
                        <strong>Total tipe kamar:</strong> {{ count($tipeKamar) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ROOMS SECTION ===== -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-4">
            @if(count($tipeKamar) > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($tipeKamar as $tipe)
                <div class="room-card reveal group rounded-3xl bg-slate-50 overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 hover:scale-[1.02]"
                    data-aos="fade-up" data-aos-offset="80" data-aos-duration="700" data-aos-easing="ease-out-quart">

                    <!-- Image Section -->
                    <div class="relative overflow-hidden">
                        @if($tipe->galeriKamars && $tipe->galeriKamars->count() > 0)
                        @php
                        $firstImage = $tipe->galeriKamars->first();
                        $imageUrl = $firstImage->url_foto;

                        // Cek apakah URL external atau local
                        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        // URL external (placeholder)
                        $imgSrc = $imageUrl;
                        } else {
                        // URL local storage - pastikan path benar
                        $cleanPath = ltrim($imageUrl, '/');
                        // Hapus 'storage/' dari awal jika sudah ada
                        if (strpos($cleanPath, 'storage/') === 0) {
                        $cleanPath = substr($cleanPath, 8); // Hapus 'storage/' dari awal
                        }
                        // Gunakan asset() untuk generate URL yang benar
                        $imgSrc = asset('storage/' . $cleanPath);
                        }
                        @endphp

                        <img src="{{ $imgSrc }}" alt="{{ $tipe->nama_tipe }}"
                            class="h-64 w-full object-cover group-hover:scale-110 transition-transform duration-700"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            loading="lazy">
                        @endif

                        {{-- Fallback jika gambar tidak ada atau error --}}
                        <div
                            class="room-fallback h-64 w-full flex items-center justify-center {{ $tipe->galeriKamars && $tipe->galeriKamars->count() > 0 ? 'hidden' : '' }}">
                            <div class="text-center">
                                <i class="fas fa-home text-4xl text-slate-500 mb-2"></i>
                                <p class="text-sm text-slate-500">{{ $tipe->nama_tipe }}</p>
                            </div>
                        </div>

                        <!-- Overlay gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>

                        <!-- Badge kode tipe -->
                        <div class="absolute top-3 right-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#133E87] text-white shadow">
                                {{ $tipe->kode_tipe }}
                            </span>
                        </div>

                        <!-- Status tersedia -->
                        <div
                            class="absolute bottom-3 left-3 text-white text-sm rounded-full bg-black/40 backdrop-blur px-3 py-1">
                            Tersedia
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-6 space-y-4">
                        <h3 class="font-semibold text-xl text-slate-900">{{ $tipe->nama_tipe }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ $tipe->deskripsi }}</p>

                        @if($tipe->fasilitasKamars && $tipe->fasilitasKamars->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($tipe->fasilitasKamars->take(3) as $fasilitas)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-blue-100 text-blue-800">
                                <i class="fas fa-check text-xs mr-1"></i>
                                {{ $fasilitas->nama_fasilitas }}
                            </span>
                            @endforeach
                            @if($tipe->fasilitasKamars->count() > 3)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-600">
                                +{{ $tipe->fasilitasKamars->count() - 3 }} lagi
                            </span>
                            @endif
                        </div>
                        @endif

                        @if($tipe->hargas && $tipe->hargas->count() > 0)
                        <div class="pt-2">
                            <div class="text-2xl font-bold text-[#133E87]">
                                Rp {{ number_format($tipe->hargas->first()->harga, 0, ',', '.') }}
                            </div>
                            <p class="text-sm text-slate-400">per malam</p>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-2">
                            <a href="{{ route('landingpage.tipe-kamar.detail', $tipe->id) }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-4 py-2 text-white transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-info-circle text-sm"></i>
                                Detail Lengkap
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-16">
                <div class="text-slate-400 text-6xl mb-4">
                    <i class="fas fa-home"></i>
                </div>
                <h3 class="text-xl font-medium text-slate-600 mb-2">Belum ada tipe kamar tersedia</h3>
                <p class="text-slate-500">Kami sedang mempersiapkan unit-unit terbaik untuk Anda</p>
            </div>
            @endif
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="bg-slate-50 py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Tertarik dengan Unit Kami?</h2>
            <p class="text-slate-600 max-w-2xl mx-auto mb-8">
                Jangan ragu untuk menghubungi kami atau melakukan booking langsung untuk unit yang Anda inginkan.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="https://wa.me/6281234567890"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 hover:bg-green-700 px-6 py-3 text-white font-medium transition-all duration-300 transform hover:scale-105">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </section>
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
            } else {
                console.warn('AOS belum ter-load');
            }

            if (!window.gsap) { console.warn('GSAP belum ter-load'); return; }
            if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

            // Animasi section rooms dengan efek yang lebih menarik
            const roomCards = gsap.utils.toArray('.room-card');
            if (roomCards.length > 0 && window.ScrollTrigger) {
                roomCards.forEach((card, index) => {
                    // Animasi entrance dengan stagger effect
                    gsap.fromTo(card,
                        {
                            opacity: 0,
                            y: 80,
                            scale: 0.85,
                            rotationX: 8,
                            rotationY: -5
                        },
                        {
                            opacity: 1,
                            y: 0,
                            scale: 1,
                            rotationX: 0,
                            rotationY: 0,
                            duration: 1,
                            ease: 'power3.out',
                            delay: index * 0.15,
                            scrollTrigger: {
                                trigger: card,
                                start: 'top 80%',
                                toggleActions: 'play none none reverse'
                            }
                        }
                    );

                    // Hover animation untuk gambar
                    const cardImage = card.querySelector('img');
                    if (cardImage) {
                        // Image error handling
                        cardImage.addEventListener('error', function() {
                            this.style.display = 'none';
                            const fallback = this.nextElementSibling;
                            if (fallback) {
                                fallback.style.display = 'flex';
                            }
                        });

                        card.addEventListener('mouseenter', () => {
                            gsap.to(cardImage, {
                                scale: 1.15,
                                duration: 0.6,
                                ease: 'power2.out'
                            });
                        });

                        card.addEventListener('mouseleave', () => {
                            gsap.to(cardImage, {
                                scale: 1,
                                duration: 0.6,
                                ease: 'power2.out'
                            });
                        });
                    }

                    // Hover animation untuk card secara keseluruhan
                    card.addEventListener('mouseenter', () => {
                        gsap.to(card, {
                            y: -8,
                            scale: 1.03,
                            duration: 0.4,
                            ease: 'power2.out'
                        });
                    });

                    card.addEventListener('mouseleave', () => {
                        gsap.to(card, {
                            y: 0,
                            scale: 1,
                            duration: 0.4,
                            ease: 'power2.out'
                        });
                    });
                });
            }

            // Scroll reveal
            const items = gsap.utils.toArray('.reveal');
            if (window.ScrollTrigger) {
                items.forEach((el) => {
                    gsap.to(el, {
                        opacity: 1, y: 0, duration: 0.7, ease: 'power2.out',
                        scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none none' }
                    });
                });
            } else {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((e) => {
                        if (e.isIntersecting) {
                            gsap.to(e.target, {opacity:1,y:0,duration:0.7,ease:'power2.out'});
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.15 });
                items.forEach((el) => io.observe(el));
            }
        });

        // Re-init setelah Livewire navigate
        document.addEventListener('livewire:navigated', () => {
            syncNavH();

            // Re-inisialisasi AOS
            if (window.AOS) {
                AOS.refresh();
            }

            if (!window.gsap) return;
            gsap.utils.toArray('.reveal').forEach((el,i)=>
                gsap.fromTo(el,{opacity:0,y:24},{opacity:1,y:0,duration:0.6,delay:0.04*i,ease:'power2.out'})
            );

            // Re-animasi section rooms setelah navigasi
            const roomCards = gsap.utils.toArray('.room-card');
            if (roomCards.length > 0) {
                gsap.set(roomCards, { opacity: 0, y: 60, scale: 0.9, rotationX: 5 });

                roomCards.forEach((card, index) => {
                    gsap.to(card, {
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        rotationX: 0,
                        duration: 0.8,
                        delay: index * 0.1,
                        ease: 'power3.out'
                    });

                    // Re-setup image error handling
                    const cardImage = card.querySelector('img');
                    if (cardImage) {
                        cardImage.addEventListener('error', function() {
                            this.style.display = 'none';
                            const fallback = this.nextElementSibling;
                            if (fallback) {
                                fallback.style.display = 'flex';
                            }
                        });
                    }
                });
            }
        });
    })();
</script>