<?php

use function Livewire\Volt\{ layout, title, state, mount };
use App\Models\TipeKamar;

layout('components.layouts.landing');
title('Cave Beach Bungalow');

state([
    'tipeKamars' => []
]);

mount(function () {
    $this->tipeKamars = TipeKamar::with(['galeriKamars', 'fasilitasKamars'])->get();
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

  /* fallback aksesibilitas bila kontras tinggi */
  @media (prefers-contrast: more) {
    .hero-head {
      color: #eaf2ff !important;
      -webkit-text-fill-color: currentColor;
      background: none !important;
      /* disable gradient text */
      text-shadow: 0 1px 2px rgba(0, 0, 0, .35);
    }
  }

  .testi-scrollbar-hidden {
    scrollbar-width: none;
    /* Firefox */
  }

  .testi-scrollbar-hidden::-webkit-scrollbar {
    /* Chrome/Safari */
    display: none;
  }

  .testi-mask {
    mask-image: linear-gradient(to right, transparent 0, black 48px, black calc(100% - 48px), transparent 100%);
    -webkit-mask-image: linear-gradient(to right, transparent 0, black 48px, black calc(100% - 48px), transparent 100%);
  }

  .testi-nav-btn {
    backdrop-filter: blur(6px);
  }
</style>

<div class="text-slate-800">
  <!-- Alert Messages -->
  <x-alert />

  <!-- ===== HERO ===== -->
  <section id="hero"
    class="relative min-h-[calc(100svh-var(--nav-h))] md:min-h-[calc(100dvh-var(--nav-h))] pt-[calc(var(--nav-h)+4rem)] pb-16 md:pb-24 overflow-hidden">

    <!-- BG Cover Image (LCP) -->
    <img src="{{ asset('img/image2.png') }}" alt="Cave Beach Bungalow" fetchpriority="high"
      class="absolute inset-0 w-full h-full object-cover object-center">

    <!-- Overlay gelap -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 via-slate-900/50 to-slate-900/30"></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-6 md:px-8 flex items-center justify-start h-full">
      <div class="max-w-4xl text-white py-12 md:py-20 lg:py-32">

        <!-- Headings -->
        <div class="mb-8 md:mb-10">
          <h1 id="hero-title-1"
            class="hero-head text-[clamp(2.5rem,6vw,5rem)] md:text-6xl lg:text-7xl font-black leading-tight text-white [text-shadow:0_2px_4px_rgba(0,0,0,.5)] opacity-0">
            Cave Beach Bungalow Nusa Penida
          </h1>
          <h2 id="hero-title-2"
           </h2>
        </div>

        <!-- Description (copy baru) -->
        <p class="mb-10 md:mb-12 text-lg md:text-xl lg:text-2xl text-white/95 leading-relaxed max-w-2xl"
          data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
          Villa nyaman dengan panorama pantai, hanya 14 menit dari pelabuhan boat. Dilengkapi kolam renang pribadi, Wi-Fi stabil, serta sistem keamanan terjaga. Lokasi strategis dekat destinasi wisata populer, ideal untuk liburan tenang maupun perjalanan singkat.
        </p>

        <!-- CTA -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6" data-aos="fade-up"
          data-aos-duration="800" data-aos-delay="500">
          <a href="{{ route('landingpage.tipe-kamar') }}"
            class="inline-flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-[#133E87] to-[#1e5bb8] hover:from-[#0f326e] hover:to-[#133E87] px-8 py-5 text-white font-semibold text-lg shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_20px_40px_rgba(19,62,135,0.3)] focus:outline-none focus:ring-4 focus:ring-blue-300/50"
            aria-label="Lihat tipe kamar yang tersedia di Cave Beach Bungalow">
            <i class="fas fa-calendar-check text-xl"></i>
            <span>Booking Sekarang</span>
          </a>
          <a href="#booking"
            class="inline-flex items-center justify-center gap-3 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-sm px-8 py-5 text-white font-semibold text-lg border border-white/30 transition-all duration-300 transform hover:scale-105 hover:shadow-[0_20px_40px_rgba(255,255,255,0.1)] focus:outline-none focus:ring-4 focus:ring-white/40"
            aria-label="Contact Cave Beach Bungalow">
            <i class="fas fa-phone-alt text-xl"></i>
            <span>Hubungi Kami</span>
          </a>
        </div>

        <!-- Reservasi Saya Link (untuk user yang sudah login) -->
        @if(auth()->check() && auth()->user()->pelanggan)
        <div class="mt-8" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">
          <a href="{{ route('landingpage.reservasi-saya') }}"
            class="inline-flex items-center gap-2 text-white/90 hover:text-white transition-colors duration-300 hover:scale-105">
            <i class="fas fa-calendar-check text-lg"></i>
            <span class="text-lg font-medium">Lihat Reservasi Saya</span>
            <i class="fas fa-arrow-right text-sm transition-transform duration-300 group-hover:translate-x-1"></i>
          </a>
        </div>
        @endif

        <!-- Microcopy kecil -->
        <div class="reveal mt-6 text-sm text-white/80">
          Affordable &nbsp;•&nbsp; Beach ambience &nbsp;•&nbsp; Rasakan Villa dengan Pantai Privat
        </div>

        <!-- Feature chips -->
        <div class="reveal mt-12 md:mt-16 flex flex-wrap items-center gap-6 text-white/80">
          <div class="flex items-center gap-2">
            <i class="fas fa-shield-alt text-blue-300"></i>
            <span class="text-sm">Keamanan 24/7</span>
          </div>
          <div class="flex items-center gap-2">
            <i class="fas fa-wifi text-blue-300"></i>
            <span class="text-sm">Wi-Fi Gratis</span>
          </div>
          <div class="flex items-center gap-2">
            <i class="fas fa-parking text-blue-300"></i>
            <span class="text-sm">Parkir Tersedia</span>
          </div>
        </div>

      </div>
    </div>

    <!-- Decorative blobs (target GSAP) -->
    <div id="blob-amber"
      class="absolute -right-32 top-32 w-96 h-96 rounded-full bg-gradient-to-br from-amber-300/20 to-transparent blur-3xl">
    </div>
    <div class="absolute -left-24 bottom-32 w-80 h-80 rounded-full bg-blue-500/20 blur-3xl"></div>
  </section>

  <!-- ===== ABOUT ===== -->
  <section id="about" class="scroll-mt-[var(--nav-h)] bg-white">
    <div class="container mx-auto px-4 py-16 grid lg:grid-cols-2 gap-10 items-center">
      <div class="order-2 lg:order-1" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
        <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Tentang <span class="text-[#133E87]">Cave Beach Bungalow</span></h2>
        <p class="mt-4 text-slate-600 leading-relaxed">
          Cave Beach Bungalow adalah hunian modern yang nyaman dan strategis, dirancang khusus untuk memberikan
          pengalaman tinggal terbaik. Dengan lokasi yang dekat pusat bisnis, kampus, dan rumah sakit, kami
          menawarkan kenyamanan maksimal dengan harga terjangkau.
        </p>
        <div class="mt-6 grid grid-cols-2 gap-4">
          <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100" data-aos="zoom-in" data-aos-duration="800"
            data-aos-delay="400">
            <div class="text-sm text-slate-500">Check-in</div>
            <div class="font-semibold">14:00</div>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100" data-aos="zoom-in" data-aos-duration="800"
            data-aos-delay="500">
            <div class="text-sm text-slate-500">Check-out</div>
            <div class="font-semibold">12:00</div>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100" data-aos="zoom-in" data-aos-duration="800"
            data-aos-delay="600">
            <div class="text-sm text-slate-500">Parkir</div>
            <div class="font-semibold">Tersedia</div>
          </div>
          <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100" data-aos="zoom-in" data-aos-duration="800"
            data-aos-delay="700">
            <div class="text-sm text-slate-500">Akses</div>
            <div class="font-semibold">14 Menit dari Pelabuhan</div>
          </div>
        </div>
      </div>
      <div class="order-1 lg:order-2" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
        <div class="relative">
          <img src="{{ asset('img/cover2.png') }}" alt="Cave Beach Bungalow Side"
            class="w-full rounded-3xl shadow-lg object-cover h-[420px]">
          <div class="absolute -bottom-8 -left-6 bg-white rounded-2xl p-4 shadow-lg border border-slate-100"
            data-aos="zoom-in" data-aos-duration="800" data-aos-delay="800">
            <div class="text-center">
              <div class="text-2xl font-bold text-[#133E87]">4.5</div>
              <div class="text-sm text-slate-600">Rating</div>
              <div class="flex text-yellow-400 text-sm mt-1">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== FASILITAS ===== -->
  <section id="fasilitas" class="scroll-mt-[var(--nav-h)] bg-slate-50">
    <div class="container mx-auto px-4 py-16">
      <div class="text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-slate-900" data-aos="fade-up" data-aos-duration="800">
          Fasilitas</h2>
        <p class="mt-3 text-slate-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-duration="800"
          data-aos-delay="200">Fungsi Unggulan Cave Beach Bungalow.</p>
      </div>
      <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Keamanan 24/7 -->
        <div
          class="facility-card rounded-2xl bg-white p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
          <div class="facility-icon mb-4">
            <img src="{{ asset('img/gif/policeman.gif') }}" alt="Keamanan 24/7" class="w-12 h-12 object-contain">
          </div>
          <h3 class="font-semibold text-slate-900">Keamanan 24/7</h3>
          <p class="mt-1 text-slate-600 text-sm">CCTV & access card untuk keamanan maksimal</p>
        </div>

        <!-- Wi-Fi Area -->
        <div
          class="facility-card rounded-2xl bg-white p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
          <div class="facility-icon mb-4">
            <img src="{{ asset('img/gif/smart-house.gif') }}" alt="Wi-Fi Area" class="w-12 h-12 object-contain">
          </div>
          <h3 class="font-semibold text-slate-900">Wi-Fi Area</h3>
          <p class="mt-1 text-slate-600 text-sm">Koneksi cepat di area komunal</p>
        </div>

        <!-- Parkir -->
        <div
          class="facility-card rounded-2xl bg-white p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
          <div class="facility-icon mb-4">
            <img src="{{ asset('img/gif/parking.gif') }}" alt="Parkir" class="w-12 h-12 object-contain">
          </div>
          <h3 class="font-semibold text-slate-900">Parkir</h3>
          <p class="mt-1 text-slate-600 text-sm">Area parkir luas dan terang</p>
        </div>

        <!-- Laundry -->
        <div
          class="facility-card rounded-2xl bg-white p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
          <div class="facility-icon mb-4">
            <img src="{{ asset('img/gif/laundry.gif') }}" alt="Laundry" class="w-12 h-12 object-contain">
          </div>
          <h3 class="font-semibold text-slate-900">Laundry</h3>
          <p class="mt-1 text-slate-600 text-sm">Self service & managed services</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== UNIT / ROOMS ===== -->
  <section id="rooms" class="scroll-mt-[var(--nav-h)] bg-white">
    <div class="container mx-auto px-4 py-16">
      <div class="text-center">
        <h2 class="reveal text-3xl md:text-4xl font-bold text-slate-900">Tipe Kamar</h2>
        <p class="reveal mt-3 text-slate-600 max-w-2xl mx-auto">Berbagai tipe kamar dengan ambience yang memikat.</p>
      </div>
      <div class="mt-10 grid md:grid-cols-3 gap-6">
        @forelse($tipeKamars as $tipe)
        <div
          class="room-card reveal group rounded-3xl bg-slate-50 overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 hover:scale-[1.02]">
          <div class="relative overflow-hidden">
            @if($tipe->galeriKamars->count() > 0)
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
              class="h-56 w-full object-cover group-hover:scale-110 transition-transform duration-700"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" loading="lazy">
            @endif

            {{-- Fallback jika gambar tidak ada atau error --}}
            <div
              class="room-fallback h-56 w-full flex items-center justify-center {{ $tipe->galeriKamars->count() > 0 ? 'hidden' : '' }}">
              <div class="text-center">
                <i class="fas fa-home text-4xl text-slate-500 mb-2"></i>
                <p class="text-sm text-slate-500">{{ $tipe->nama_tipe }}</p>
              </div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>
            <div class="absolute top-3 right-3">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#133E87] text-white">
                {{ $tipe->kode_tipe }}
              </span>
            </div>
            <div class="absolute bottom-3 left-3 text-white text-sm rounded-full bg-black/40 backdrop-blur px-3 py-1">
              Tersedia
            </div>
          </div>
          <div class="p-5">
            <h3 class="font-semibold text-lg text-slate-900">{{ $tipe->nama_tipe }}</h3>
            <p class="mt-2 text-slate-600 text-sm leading-relaxed">{{ $tipe->deskripsi }}</p>

            @if($tipe->fasilitasKamars->count() > 0)
            <div class="mt-3 flex flex-wrap gap-1">
              @foreach($tipe->fasilitasKamars->take(3) as $fasilitas)
              <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-blue-100 text-blue-800">
                <i class="fas fa-check text-xs mr-1"></i>
                {{ $fasilitas->nama_fasilitas }}
              </span>
              @endforeach
              @if($tipe->fasilitasKamars->count() > 3)
              <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-600">
                +{{ $tipe->fasilitasKamars->count() - 3 }} lagi
              </span>
              @endif
            </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
              <a href="{{ route('landingpage.tipe-kamar') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-[#133E87] hover:bg-[#0f326e] px-4 py-2 text-white transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-info-circle text-sm"></i>
                Detail Lengkap
              </a>
            </div>
          </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
          <div class="text-slate-400 text-6xl mb-4">
            <i class="fas fa-home"></i>
          </div>
          <h3 class="text-xl font-medium text-slate-600 mb-2">Belum ada unit tersedia</h3>
          <p class="text-slate-500">Kami sedang mempersiapkan unit-unit terbaik untuk Anda</p>
        </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONI ===== -->
  <section id="testimoni" class="scroll-mt-[var(--nav-h)] bg-slate-50">
    <div class="container mx-auto px-4 py-16">
      <div class="text-center">
        <h2 class="reveal text-3xl md:text-4xl font-bold text-slate-900">Apa Kata Mereka?</h2>
        <p class="reveal mt-3 text-slate-600 max-w-2xl mx-auto">Pengalaman menginap di Cave Beach Bungalow dari berbagai orang.
        </p>
      </div>

      @php
      $testi = [
      ['name'=>'Restu','text'=>'Lobby nyaman untuk kerja, Wi-Fi stabil.'],
      ['name'=>'Iben','text'=>'Unit 2BR lega & bersih, anak betah.'],
      ['name'=>'Rani','text'=>'Akses mudah, dekat transport & kuliner.'],
      ['name'=>'Rini','text'=>'Dekat kampus, hemat waktu & biaya.'],
      ['name'=>'Arap','text'=>'Interior modern, banyak spot foto kece.'],
      ['name'=>'Dwiki','text'=>'Koneksi internet cepat, meeting lancar.'],
      ['name'=>'Varina','text'=>'Dekat rumah sakit, shift malam aman.'],
      ['name'=>'Dede','text'=>'Parkir luas, staff sigap membantu.'],
      ['name'=>'Tu Agus','text'=>'Pencahayaan bagus, shooting jadi gampang.'],
      ['name'=>'Kelek','text'=>'Lingkungan tenang, tidur nyenyak tiap malam.'],
      ];
      @endphp

      <div class="relative mt-8 px-16">
        <!-- Tambah padding kiri-kanan untuk button -->
        <!-- Arrow buttons - posisi di luar area card -->
        <button type="button"
          class="testi-nav-btn group absolute left-0 top-1/2 -translate-y-1/2 z-10
              hidden sm:flex items-center justify-center w-12 h-12 rounded-full
              bg-white hover:bg-gray-50 border border-gray-200 shadow-lg
              transition-all duration-200 active:scale-95 focus:outline-none focus:ring-4 focus:ring-blue-200 hover:shadow-xl"
          aria-label="Gulir testimoni ke kiri" data-testi-prev>
          <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
            </path>
          </svg>
        </button>
        <button type="button"
          class="testi-nav-btn group absolute right-0 top-1/2 -translate-y-1/2 z-10
              hidden sm:flex items-center justify-center w-12 h-12 rounded-full
              bg-white hover:bg-gray-50 border border-gray-200 shadow-lg
              transition-all duration-200 active:scale-95 focus:outline-none focus:ring-4 focus:ring-blue-200 hover:shadow-xl"
          aria-label="Gulir testimoni ke kanan" data-testi-next>
          <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-800" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <!-- Track - hapus margin negatif karena sudah ada padding container -->
        <div id="testi-track" class="testi-mask testi-scrollbar-hidden relative overflow-x-auto overscroll-x-contain
                  snap-x snap-mandatory scroll-smooth">
          <ul class="flex gap-4 sm:gap-6 py-4" role="list" aria-label="Daftar testimoni">
            @foreach($testi as $t)
            <li class="snap-center shrink-0 w-[86%] xs:w-[78%] sm:w-[56%] md:w-[44%] lg:w-[32%] xl:w-[28%]">
              <figure class="reveal h-full rounded-2xl bg-white p-5 md:p-6 border border-slate-100 shadow-sm">
                <blockquote class="text-slate-700 italic leading-relaxed">"{{ $t['text'] }}"
                </blockquote>
                <figcaption class="mt-4 font-semibold text-slate-900">— {{ $t['name'] }}</figcaption>
              </figure>
            </li>
            @endforeach
          </ul>
        </div>
      </div>

      <!-- Dots (optional) -->
      <div class="mt-4 flex justify-center gap-2" id="testi-dots" aria-hidden="true"></div>
    </div>
  </section>

  <!-- ===== BOOKING / CTA ===== -->
  <section id="booking" class="scroll-mt-[var(--nav-h)] relative bg-slate-900">
    <div class="container relative z-10 mx-auto px-4 py-16 text-white">
      <div class="text-center max-w-3xl mx-auto">
        <h2 class="reveal text-3xl md:text-4xl font-bold">Tertarik dengan kami?</h2>
        <p class="reveal mt-3 text-white/80">Mari booking sekarang untuk mendapatkan villa yang strategis dengan pantai privat di depannya
        </p>
        <div class="reveal mt-8 flex flex-wrap items-center justify-center gap-3">
          <a href="{{ route('landingpage.tipe-kamar') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 px-6 py-3 font-medium transition">
            Lihat Tipe Kamar
          </a>
          <a href="https://wa.me/6281339163939"
            class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-6 py-3 text-white backdrop-blur transition">
            WhatsApp
          </a>
          <p class="basis-full text-sm text-white/60">Telepon: +62 813-3916-3939</p>
        </div>
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
    if (window.ScrollToPlugin) gsap.registerPlugin(ScrollToPlugin);

    // HERO entrance
    gsap.to('#hero .reveal', {
      opacity: 1, y: 0, duration: 0.9, ease: 'power2.out', stagger: 0.08, delay: 0.15
    });

    // Animasi khusus untuk heading hero
    const heroTitle1 = document.getElementById('hero-title-1');
    const heroTitle2 = document.getElementById('hero-title-2');

          if (heroTitle1 && heroTitle2) {
        // Timeline untuk animasi heading
        const heroTimeline = gsap.timeline({ delay: 0.3 });

        heroTimeline
          .to(heroTitle1, {
            opacity: 1,
            duration: 1.2,
            ease: 'power3.out'
          })
          .to(heroTitle2, {
            opacity: 1,
            duration: 1.2,
            ease: 'power3.out'
          }, '-=0.6'); // Mulai sebelum animasi pertama selesai
      }

    // Parallax halus pada blob (fixed selector)
    const blob = document.querySelector('#blob-amber');
    if (blob && window.ScrollTrigger) {
      gsap.to(blob, {
        y: 80, x: -40, ease: 'none',
        scrollTrigger: { trigger: '#hero', start: 'top top', end: 'bottom top', scrub: true }
      });
    }

    // Animasi section fasilitas
    const facilityCards = gsap.utils.toArray('.facility-card');
    if (facilityCards.length > 0 && window.ScrollTrigger) {
      facilityCards.forEach((card, index) => {
        // Animasi entrance
        gsap.fromTo(card,
          {
            opacity: 0,
            y: 50,
            scale: 0.8,
            rotationY: -15
          },
          {
            opacity: 1,
            y: 0,
            scale: 1,
            rotationY: 0,
            duration: 0.8,
            ease: 'power2.out',
            delay: index * 0.1,
            scrollTrigger: {
              trigger: card,
              start: 'top 85%',
              toggleActions: 'play none none reverse'
            }
          }
        );

        // Hover animation
        card.addEventListener('mouseenter', () => {
          gsap.to(card.querySelector('.facility-icon img'), {
            scale: 1.1,
            rotation: 5,
            duration: 0.3,
            ease: 'power2.out'
          });
        });

        card.addEventListener('mouseleave', () => {
          gsap.to(card.querySelector('.facility-icon img'), {
            scale: 1,
            rotation: 0,
            duration: 0.3,
            ease: 'power2.out'
          });
        });
      });
    }

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

    // Smooth anchor scroll dengan offset navbar
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', (e) => {
        const id = a.getAttribute('href');
        const target = document.querySelector(id);
        if (!target) return;
        const navH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-h')) || 80;
        const y = target.getBoundingClientRect().top + window.pageYOffset - navH;

        if (window.ScrollToPlugin) {
          e.preventDefault();
          gsap.to(window, { duration: 0.9, scrollTo: y, ease: 'power2.out' });
        } else {
          // fallback native smooth scroll
          e.preventDefault();
          window.scrollTo({ top: y, behavior: 'smooth' });
        }
      });
    });
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

    // Re-animasi heading hero setelah navigasi
    const heroTitle1 = document.getElementById('hero-title-1');
    const heroTitle2 = document.getElementById('hero-title-2');

    if (heroTitle1 && heroTitle2) {
      gsap.set([heroTitle1, heroTitle2], { opacity: 0 });

      const heroTimeline = gsap.timeline({ delay: 0.2 });
      heroTimeline
        .to(heroTitle1, {
          opacity: 1,
          duration: 1.2,
          ease: 'power3.out'
        })
        .to(heroTitle2, {
          opacity: 1,
          duration: 1.2,
          ease: 'power3.out'
        }, '-=0.6');
    }

    // Re-animasi section fasilitas setelah navigasi
    const facilityCards = gsap.utils.toArray('.facility-card');
    if (facilityCards.length > 0) {
      gsap.set(facilityCards, { opacity: 0, y: 50, scale: 0.8, rotationY: -15 });

      facilityCards.forEach((card, index) => {
        gsap.to(card, {
          opacity: 1,
          y: 0,
          scale: 1,
          rotationY: 0,
          duration: 0.6,
          delay: index * 0.08,
          ease: 'power2.out'
        });
      });
    }

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

    // Re-inisialisasi testimoni setelah navigasi
    initTesti();
  });

  // Fungsi untuk testimoni carousel
  const initTesti = () => {
    const track = document.getElementById('testi-track');
    if (!track) return;

    const prevBtn = document.querySelector('[data-testi-prev]');
    const nextBtn = document.querySelector('[data-testi-next]');
    const items   = Array.from(track.querySelectorAll('li'));
    const dotsWrap = document.getElementById('testi-dots');

    // Build dots (optional indikator per card)
    if (dotsWrap && items.length) {
      dotsWrap.innerHTML = '';
      items.forEach((_, i) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition';
        b.setAttribute('aria-label', `Ke testimoni ${i+1}`);
        b.addEventListener('click', () => {
          items[i].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        });
        dotsWrap.appendChild(b);
      });
    }

    // Helper: update dots active
    const updateDots = () => {
      if (!dotsWrap) return;
      const children = dotsWrap.children;
      if (!children.length) return;

      let closestIndex = 0;
      let minDelta = Infinity;
      const center = track.scrollLeft + track.clientWidth / 2;

      items.forEach((li, idx) => {
        const left = li.offsetLeft + li.clientWidth / 2;
        const delta = Math.abs(center - left);
        if (delta < minDelta) { minDelta = delta; closestIndex = idx; }
      });

      Array.from(children).forEach((el, i) =>
        el.className = 'w-2.5 h-2.5 rounded-full transition ' +
          (i === closestIndex ? 'bg-[#133E87]' : 'bg-slate-300 hover:bg-slate-400')
      );
    };

    // Scroll buttons
    const step = () => {
      // satu langkah = lebar 1 kartu + gap
      if (!items.length) return 320;
      const li = items[0];
      const gap = parseFloat(getComputedStyle(items[0].parentElement).gap || '16');
      return li.clientWidth + gap;
    };

    prevBtn?.addEventListener('click', () => {
      track.scrollBy({ left: -step(), behavior: 'smooth' });
    });
    nextBtn?.addEventListener('click', () => {
      track.scrollBy({ left: step(), behavior: 'smooth' });
    });

    // Auto-scroll (loop), pause on hover/focus/visibility
    let autoTimer = null;
    const startAuto = () => {
      stopAuto();
      autoTimer = setInterval(() => {
        const nearEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
        if (nearEnd) {
          track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: step(), behavior: 'smooth' });
        }
      }, 3500);
    };
    const stopAuto = () => autoTimer && (clearInterval(autoTimer), autoTimer = null);

    // Pause on user intent
    track.addEventListener('mouseenter', stopAuto);
    track.addEventListener('mouseleave', startAuto);
    track.addEventListener('touchstart', stopAuto, { passive: true });
    track.addEventListener('focusin', stopAuto);
    document.addEventListener('visibilitychange', () => {
      document.hidden ? stopAuto() : startAuto();
    });

    // Update dots on scroll end (throttled)
    let raf;
    track.addEventListener('scroll', () => {
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(updateDots);
    });

    // First run
    updateDots();
    startAuto();

    // If GSAP present, soften scroll snapping feel (optional)
    if (window.gsap && window.ScrollToPlugin) {
      // no special config required since we use native scroll; kept for future tweak
    }
  };

  // Panggil initTesti di dalam ready() yang sudah ada
  ready(() => {
    // ... existing code ...
    initTesti();
  });
})();
</script>