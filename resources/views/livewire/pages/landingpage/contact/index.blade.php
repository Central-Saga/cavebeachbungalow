<?php

use function Livewire\Volt\{ layout, title, state, mount };

layout('components.layouts.landing');
title('Contact Us - Pondok Putri Apartment');

?>

<div class="text-slate-800">
    <!-- ===== HERO ===== -->
    <section id="hero" class="relative min-h-[calc(100svh-84px)] pt-32 overflow-hidden">
        <!-- BG Cover Image -->
        <img src="{{ asset('img/cover.png') }}" alt="Pondok Putri Apartment" fetchpriority="high"
            class="absolute inset-0 w-full h-full object-cover object-center">

        <!-- Overlay gelap -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/80 via-slate-900/50 to-slate-900/30"></div>

        <!-- Content -->
        <div class="relative z-10 container mx-auto px-4 pb-16 md:pb-24">
            <div class="max-w-4xl text-white">
                <!-- Badge -->
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-sm px-4 py-2 text-sm font-medium border border-white/30"
                    data-aos="fade-down" data-aos-duration="800" data-aos-delay="100">
                    <i class="fas fa-phone text-blue-300"></i>
                    Hubungi Kami
                </span>

                <!-- Headings -->
                <div class="mt-8">
                    <h1
                        class="hero-head text-[clamp(2rem,6vw,4.5rem)] md:text-6xl lg:text-7xl font-black leading-tight text-white [text-shadow:0_2px_4px_rgba(0,0,0,.5)] opacity-0">
                        Hubungi Kami
                    </h1>
                    <h2
                        class="hero-head text-[clamp(2rem,6vw,4.5rem)] md:text-6xl lg:text-7xl font-black leading-tight text-white mt-2 [text-shadow:0_2px_4px_rgba(0,0,0,.5)] opacity-0">
                        Siap Melayani Anda
                    </h2>
                </div>

                <!-- Description -->
                <p class="mt-6 md:mt-8 text-lg md:text-xl lg:text-2xl text-white/95 leading-relaxed max-w-2xl"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    Ada pertanyaan atau ingin melakukan reservasi? Jangan ragu untuk menghubungi tim kami yang siap
                    membantu Anda.
                </p>

                <!-- CTA -->
                <div class="mt-10 md:mt-12 flex flex-col sm:flex-row items-stretch sm:items-center gap-4"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                    <a href="https://wa.me/6281234567890"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 px-8 py-4 text-white font-semibold text-lg shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_20px_40px_rgba(34,197,94,0.3)] focus:outline-none focus:ring-4 focus:ring-green-300/50"
                        aria-label="Hubungi kami via WhatsApp">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>WhatsApp</span>
                    </a>
                    <a href="tel:+6281234567890"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl bg-white/20 hover:bg-white/30 backdrop-blur-sm px-8 py-4 text-white font-semibold text-lg border border-white/30 transition-all duration-300 transform hover:scale-105 hover:shadow-[0_20px_40px_rgba(255,255,255,0.1)] focus:outline-none focus:ring-4 focus:ring-white/40"
                        aria-label="Telepon langsung ke kami">
                        <i class="fas fa-phone text-xl"></i>
                        <span>Telepon</span>
                    </a>
                </div>

                <!-- Feature chips -->
                <div class="reveal mt-12 md:mt-16 flex flex-wrap items-center gap-6 text-white/80">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-blue-300"></i>
                        <span class="text-sm">Layanan 24/7</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-comments text-blue-300"></i>
                        <span class="text-sm">Respon Cepat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-heart text-blue-300"></i>
                        <span class="text-sm">Pelayanan Ramah</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative blobs -->
        <div id="blob-amber"
            class="absolute -right-32 top-32 w-96 h-96 rounded-full bg-gradient-to-br from-amber-300/20 to-transparent blur-3xl">
        </div>
        <div class="absolute -left-24 bottom-32 w-80 h-80 rounded-full bg-blue-500/20 blur-3xl"></div>
    </section>

    <!-- ===== CONTACT INFO ===== -->
    <section id="contact-info" class="scroll-mt-[84px] bg-white">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900" data-aos="fade-up" data-aos-duration="800">
                    Informasi Kontak
                </h2>
                <p class="mt-3 text-slate-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-duration="800"
                    data-aos-delay="200">
                    Berbagai cara untuk menghubungi kami
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <!-- Phone -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 border border-slate-100"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                    <div class="w-16 h-16 bg-[#608BC1] rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-[#133E87] mb-3 text-center">Telepon</h3>
                    <div class="space-y-2 text-center">
                        <p class="text-gray-600">+62 812-3456-7890</p>
                        <p class="text-gray-600">+62 812-3456-7891</p>
                        <a href="tel:+6281234567890"
                            class="inline-block mt-3 px-4 py-2 bg-[#608BC1] text-white rounded-lg hover:bg-[#4a6a99] transition-colors duration-300">
                            <i class="fas fa-phone mr-2"></i>Telepon Sekarang
                        </a>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 border border-slate-100"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    <div class="w-16 h-16 bg-[#608BC1] rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-[#133E87] mb-3 text-center">Email</h3>
                    <div class="space-y-2 text-center">
                        <p class="text-gray-600">info@pondokputri.com</p>
                        <p class="text-gray-600">reservasi@pondokputri.com</p>
                        <a href="mailto:info@pondokputri.com"
                            class="inline-block mt-3 px-4 py-2 bg-[#608BC1] text-white rounded-lg hover:bg-[#4a6a99] transition-colors duration-300">
                            <i class="fas fa-envelope mr-2"></i>Kirim Email
                        </a>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 border border-slate-100"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                    <div class="w-16 h-16 bg-[#608BC1] rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-[#133E87] mb-3 text-center">Alamat</h3>
                    <div class="space-y-2 text-center">
                        <p class="text-gray-600">Jl. Beji Ayu IV No.3, Seminyak</p>
                        <p class="text-gray-600">Kec. Kuta, Kabupaten Badung, Bali 80361</p>
                        <a href="#maps"
                            class="inline-block mt-3 px-4 py-2 bg-[#608BC1] text-white rounded-lg hover:bg-[#4a6a99] transition-colors duration-300">
                            <i class="fas fa-map mr-2"></i>Lihat Peta
                        </a>
                    </div>
                </div>
            </div>

            <!-- Business Hours -->
            <div class="max-w-2xl mx-auto text-center" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">
                <h3 class="text-2xl font-bold text-[#133E87] mb-6">Jam Operasional</h3>
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-slate-100">
                    <div class="grid grid-cols-2 gap-4 text-lg mb-4">
                        <div class="text-left">
                            <p class="font-semibold text-[#133E87]">Senin - Jumat</p>
                            <p class="text-gray-600">08:00 - 22:00</p>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-[#133E87]">Sabtu - Minggu</p>
                            <p class="text-gray-600">07:00 - 23:00</p>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm">*Reservasi online tersedia 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== MAPS ===== -->
    <section id="maps" class="scroll-mt-[84px] bg-slate-50">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900" data-aos="fade-up" data-aos-duration="800">
                    Lokasi Kami
                </h2>
                <p class="mt-3 text-slate-600 max-w-2xl mx-auto" data-aos="fade-up" data-aos-duration="800"
                    data-aos-delay="200">
                    Temukan lokasi Pondok Putri dengan mudah
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <!-- Map -->
                <div class="order-2 lg:order-1" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
                    <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.5!2d115.2!3d-8.7!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOMKwNDInMDAuMCJTIDExNcKwMTInMDAuMCJF!5e0!3m2!1sen!2sid!4v1234567890"
                            width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" class="w-full h-96">
                        </iframe>
                    </div>
                </div>

                <!-- Location Info -->
                <div class="order-1 lg:order-2" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-2xl font-bold text-[#133E87] mb-3">Lokasi Strategis</h3>
                            <p class="text-slate-600 leading-relaxed">
                                Pondok Putri berlokasi di area strategis yang mudah dijangkau dari berbagai tempat
                                penting di Bali.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-plane text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">Bandara</div>
                                        <div class="text-sm text-slate-600">15 menit</div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-bag text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">Pusat Kota</div>
                                        <div class="text-sm text-slate-600">10 menit</div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-umbrella-beach text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">Pantai</div>
                                        <div class="text-sm text-slate-600">5 menit</div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-utensils text-orange-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">Restoran</div>
                                        <div class="text-sm text-slate-600">2 menit</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-[#133E87] to-[#608BC1] rounded-2xl p-6 text-white">
                            <h4 class="text-lg font-semibold mb-2">Butuh Bantuan?</h4>
                            <p class="text-white/90 mb-4">Tim kami siap membantu Anda menemukan lokasi dengan mudah</p>
                            <a href="https://wa.me/6281234567890"
                                class="inline-flex items-center gap-2 bg-white text-[#133E87] px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                <i class="fab fa-whatsapp"></i>
                                Tanya Lokasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section id="cta" class="scroll-mt-[84px] relative bg-slate-900">
        <div
            class="absolute inset-0 opacity-[0.06] bg-[url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1200&auto=format&fit=crop')] bg-cover">
        </div>
        <div class="container relative z-10 mx-auto px-4 py-16 text-white">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold">Siap Memulai Perjalanan?</h2>
                <p class="mt-3 text-white/80">Hubungi kami sekarang untuk informasi lebih lanjut dan reservasi.</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="https://wa.me/6281234567890"
                        class="inline-flex items-center gap-2 rounded-xl bg-green-500 hover:bg-green-600 text-white px-6 py-3 font-medium transition">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                    <a href="tel:+6281234567890"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-6 py-3 text-white backdrop-blur transition">
                        <i class="fas fa-phone"></i>
                        Telepon
                    </a>
                    <p class="basis-full text-sm text-white/60">Telepon: +62 812-3456-7890</p>
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

    // HERO entrance
    gsap.to('#hero .reveal', {
      opacity: 1, y: 0, duration: 0.9, ease: 'power2.out', stagger: 0.08, delay: 0.15
    });

    // Animasi khusus untuk heading hero
    const heroTitle1 = document.querySelector('#hero h1');
    const heroTitle2 = document.querySelector('#hero h2');

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
        }, '-=0.6');
    }

    // Parallax halus pada blob
    const blob = document.querySelector('#blob-amber');
    if (blob && window.ScrollTrigger) {
      gsap.to(blob, {
        y: 80, x: -40, ease: 'none',
        scrollTrigger: { trigger: '#hero', start: 'top top', end: 'bottom top', scrub: true }
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
        const navH = 84; // Fixed navbar height
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
    // Re-inisialisasi AOS
    if (window.AOS) {
      AOS.refresh();
    }

    if (!window.gsap) return;
    gsap.utils.toArray('.reveal').forEach((el,i)=>
      gsap.fromTo(el,{opacity:0,y:24},{opacity:1,y:0,duration:0.6,delay:0.04*i,ease:'power2.out'})
    );

    // Re-animasi heading hero setelah navigasi
    const heroTitle1 = document.querySelector('#hero h1');
    const heroTitle2 = document.querySelector('#hero h2');

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
  });
})();
</script>