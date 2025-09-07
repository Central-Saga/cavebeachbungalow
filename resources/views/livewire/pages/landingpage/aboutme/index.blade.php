<?php

use function Livewire\Volt\{ layout, title, state, mount };

layout('components.layouts.landing');
title('Tentang Kami - Pondok Putri Apartment');

state([
    'teamMembers' => [],
    'achievements' => [],
    'values' => []
]);

mount(function () {
    $this->teamMembers = [
        [
            'name' => 'Sarah Putri',
            'position' => 'Founder & CEO',
            'image' => 'img/asset/team-1.jpg',
            'description' => 'Pengalaman 15+ tahun di industri properti dan hospitality.',
            'social' => ['linkedin', 'instagram', 'twitter']
        ],
        [
            'name' => 'Ahmad Rahman',
            'position' => 'Property Manager',
            'image' => 'img/asset/team-2.jpg',
            'description' => 'Spesialis manajemen properti dengan fokus pada customer satisfaction.',
            'social' => ['linkedin', 'instagram']
        ],
        [
            'name' => 'Maya Sari',
            'position' => 'Customer Relations',
            'image' => 'img/asset/team-3.jpg',
            'description' => 'Memastikan setiap penghuni merasa seperti di rumah sendiri.',
            'social' => ['instagram', 'whatsapp']
        ]
    ];

    $this->achievements = [
        ['number' => '1000+', 'label' => 'Penghuni Puas', 'icon' => 'fas fa-smile'],
        ['number' => '6+', 'label' => 'Tahun Pengalaman', 'icon' => 'fas fa-award'],
        ['number' => '97%', 'label' => 'Kepuasan Pelanggan', 'icon' => 'fas fa-heart'],
        ['number' => '24/7', 'label' => 'Layanan Support', 'icon' => 'fas fa-clock']
    ];

    $this->values = [
        [
            'title' => 'Integritas',
            'description' => 'Kami selalu jujur dan transparan dalam setiap transaksi.',
            'icon' => 'fas fa-handshake',
            'color' => 'from-blue-500 to-blue-600'
        ],
        [
            'title' => 'Kualitas',
            'description' => 'Mempertahankan standar tinggi dalam setiap aspek layanan.',
            'icon' => 'fas fa-star',
            'color' => 'from-amber-500 to-amber-600'
        ],
        [
            'title' => 'Inovasi',
            'description' => 'Terus berinovasi untuk memberikan pengalaman terbaik.',
            'icon' => 'fas fa-lightbulb',
            'color' => 'from-purple-500 to-purple-600'
        ],
        [
            'title' => 'Kepedulian',
            'description' => 'Peduli terhadap kenyamanan dan keamanan penghuni.',
            'icon' => 'fas fa-heart',
            'color' => 'from-pink-500 to-pink-600'
        ]
    ];
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

    .hero-head {
        background: linear-gradient(135deg, #133E87 0%, #608BC1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    .card-hover {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .team-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .team-card:hover {
        transform: translateY(-12px);
    }

    .achievement-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .achievement-card:hover {
        transform: scale(1.05);
    }

    .value-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .value-card:hover {
        transform: translateY(-6px);
    }

    /* fallback aksesibilitas bila kontras tinggi */
    @media (prefers-contrast: more) {
        .hero-head {
            color: #133E87 !important;
            -webkit-text-fill-color: currentColor;
            background: none !important;
            text-shadow: none;
        }
    }
</style>

<div class="text-slate-800">
    <!-- ===== HERO SECTION ===== -->
    <section id="hero"
        class="relative min-h-[calc(100svh-var(--nav-h))] md:min-h-[calc(100dvh-var(--nav-h))] pt-[calc(var(--nav-h)+2rem)] overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 gradient-bg"></div>

        <!-- Decorative Elements -->
        <div class="absolute top-20 right-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl"></div>

        <!-- Content -->
        <div class="relative z-10 container mx-auto px-4 pb-16 md:pb-24">
            <div class="max-w-4xl mx-auto text-center">

                <!-- Main Heading -->
                <h1 class="hero-head text-[clamp(2.5rem,8vw,5rem)] md:text-6xl lg:text-7xl font-black leading-tight mt-8 opacity-0"
                    id="hero-title">
                    About Us
                    <span class="block">Cave Beach Bungalow Nusa Penida</span>
                </h1>

                <!-- Description -->
                <p class="mt-8 text-lg md:text-xl lg:text-2xl text-slate-600 leading-relaxed max-w-3xl mx-auto"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    Kami berkomitmen memberikan kenyamanan terbaik bagi setiap penghuni dengan layanan yang memanjakan.
                </p>

                <!-- CTA Buttons -->
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4" data-aos="fade-up"
                    data-aos-duration="800" data-aos-delay="500">
                    <a href="#story"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-[#133E87] to-[#1e5bb8] hover:from-[#0f326e] hover:to-[#133E87] px-8 py-4 text-white font-semibold text-lg shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_20px_40px_rgba(19,62,135,0.3)] focus:outline-none focus:ring-4 focus:ring-blue-300/50">
                        <i class="fas fa-book-open text-xl"></i>
                        <span>Mari Bersama Kami</span>
                    </a>
                    <a href="{{ route('landingpage.home') }}#rooms"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl bg-white/80 hover:bg-white backdrop-blur-sm px-8 py-4 text-[#133E87] font-semibold text-lg border border-slate-200 transition-all duration-300 transform hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300/50">
                        <i class="fas fa-home text-xl"></i>
                        <span>Lihat Kamar</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STORY SECTION ===== -->
    <section id="story" class="scroll-mt-[var(--nav-h)] bg-white py-20">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Story Content -->
                <div class="order-2 lg:order-1" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">
                        6 Tahun <span class="text-[#133E87]">Perjalanan Kami</span>
                        <span class="block">Membangun Kepercayaan</span>
                    </h2>

                    <div class="space-y-6 text-slate-600 leading-relaxed">
                        <p>
                           Cave Beach Bungalow adalah usaha yang bergerak di bidang akomodasi yang lokasinya berada di bagian timur pulau Bali, tepatnya di pulau Nusa Penida. Villa ini terletak di Jalan Batununggul,Suana, Desa Karangsari, Desa Suana, Kecamatan Nusa Penida, Klungkung, Bali. Pemilik dari usaha villa ini adalah Bapak I Putu Rai Sudarta, SE. Awal mula Cave Beach Bungalow dibangun karena pemilik villa ini melihat potensi pariwisata yang berada di daerah Nusa Penida, Bali terus berkembang. Wisatawan yang datang ke pulau Bali untuk berliburan dan mencari kamar untuk menginap yang menjadi alasan Bapak Ketut Suyasa membangun villa ini di lahan miliknya. Cave Beach Bungalow telah beroperasi selama 5 (lima) tahun sejak awal berdirinya villa ini pada tahun 2019. Penginapan ini memiliki kolam renang outdoor dengan tema infinity berukuran 4×8 meter, delapan kamar yang dilengkapi dengan layanan free WiFi, kitchen set yang terletak dilobby, resepsionis 15 jam dan area parkir yang cukup luas. Adapun fasilitas yang disediakan di setiap kamar, yaitu tempat tidur single suite atau double suite, AC (Air Conditioner), kamar mandi, peralatan mandi, lemari pakaian, meja, kursi dan teras depan yang menghadap langsung ke laut.
                        </p>
                    </div>

                    <!-- Key Points -->
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-10 h-10 bg-[#133E87] rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900">Keamanan 24/7</div>
                                <div class="text-sm text-slate-500">CCTV & Access Control</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-10 h-10 bg-[#133E87] rounded-lg flex items-center justify-center">
                                <i class="fas fa-wifi text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900">Wi-Fi Stabil</div>
                                <div class="text-sm text-slate-500">High-Speed Internet</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Story Image -->
                <div class="order-1 lg:order-2" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
                    <div class="relative">
                        <img src="{{ asset('img/image2.png') }}" alt="Pondok Putri Story"
                            class="w-full rounded-3xl shadow-2xl object-cover h-[500px]">

                        <!-- Floating Stats Card -->
                        <div class="absolute -bottom-8 -right-8 bg-white rounded-2xl p-6 shadow-xl border border-slate-100"
                            data-aos="zoom-in" data-aos-duration="800" data-aos-delay="800">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#133E87]">4.5</div>
                                <div class="text-sm text-slate-600 mb-2">Rating Google</div>
                                <div class="flex justify-center text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">500+ ulasan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== VISION & MISSION SECTION ===== -->
    <section class="bg-gradient-to-br from-slate-50 to-blue-50/30 py-20 overflow-hidden">
        <div class="container mx-auto px-4">
            <!-- Vision -->
            <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="800">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                    Visi & <span class="text-[#133E87]">Misi</span>
                </h2>
                <p class="text-slate-600 max-w-3xl mx-auto">
                    Komitmen kami untuk memberikan pengalaman menginap terbaik di tepi pantai Nusa Penida
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Vision Card -->
                <div class="relative" data-aos="fade-right" data-aos-duration="1000">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 rounded-3xl blur-2xl"></div>
                    <div class="relative bg-white/80 backdrop-blur-xl p-8 rounded-3xl border border-white/30 shadow-xl hover:shadow-2xl transition-all duration-300 group">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#133E87] to-[#608BC1] rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-mountain text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Visi</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Menjadi ikon kedamaian tepi pantai Nusa Penida, tempat setiap tamu menemukan ketenangan dan keindahan
                        </p>
                    </div>
                </div>

                <!-- Mission Card -->
                <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 rounded-3xl blur-2xl"></div>
                    <div class="relative bg-white/80 backdrop-blur-xl p-8 rounded-3xl border border-white/30 shadow-xl hover:shadow-2xl transition-all duration-300">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#133E87] to-[#608BC1] rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-star text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Misi</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-home text-[#133E87]"></i>
                                </div>
                                <p class="text-slate-600">Menyediakan akomodasi nyaman dengan sentuhan alam yang memukau.</p>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-heart text-[#133E87]"></i>
                                </div>
                                <p class="text-slate-600">Menginspirasi relaksasi melalui harmoni pantai dan pelayanan tulus.</p>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-camera text-[#133E87]"></i>
                                </div>
                                <p class="text-slate-600">Menciptakan kenangan liburan yang berkesan di setiap kunjungan.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ACHIEVEMENTS SECTION ===== -->
    <section class="bg-slate-50 py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="800">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                    Pencapaian <span class="text-[#133E87]">Kami</span>
                </h2>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    Angka-angka yang membuktikan komitmen kami dalam memberikan layanan terbaik
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($achievements as $index => $achievement)
                <div class="achievement-card text-center p-8 bg-white rounded-3xl shadow-lg border border-slate-100"
                    data-aos="zoom-in" data-aos-duration="800" data-aos-delay="{{ 200 + ($index * 100) }}">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-[#133E87] to-[#608BC1] rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="{{ $achievement['icon'] }} text-3xl text-white"></i>
                    </div>
                    <div class="text-4xl font-bold text-[#133E87] mb-2">{{ $achievement['number'] }}</div>
                    <div class="text-slate-600 font-medium">{{ $achievement['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== VALUES SECTION ===== -->
    <section class="bg-white py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="800">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">
                    Nilai-Nilai <span class="text-[#133E87]">Kami</span>
                </h2>
                <p class="text-slate-600 max-w-2xl mx-auto">
                    Prinsip yang menjadi fondasi dalam setiap keputusan dan layanan kami
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($values as $index => $value)
                <div class="value-card text-center p-8 bg-slate-50 rounded-3xl border border-slate-100 hover:bg-white hover:shadow-xl transition-all duration-300"
                    data-aos="fade-up" data-aos-duration="800" data-aos-delay="{{ 200 + ($index * 100) }}">
                    <div
                        class="w-16 h-16 bg-gradient-to-br {{ $value['color'] }} rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="{{ $value['icon'] }} text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">{{ $value['title'] }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $value['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="bg-gradient-to-br from-[#133E87] to-[#1e5bb8] py-20">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto" data-aos="fade-up" data-aos-duration="800">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Siap Bergabung dengan <span class="text-amber-300">Keluarga Kami?</span>
                </h2>
                <p class="text-white/90 text-lg mb-8 leading-relaxed">
                    Mari nikmati villa dengan kenyaman yang terjamin, layanan terbaik, suasana tepi pantai dengan akses cepat ke pelabuhan dan tempat wisata populer di Nusa Penida.
                </p>
                    <a href="https://wa.me/6283114380118"
                        class="inline-flex items-center justify-center gap-3 rounded-2xl bg-amber-400 hover:bg-amber-500 text-slate-900 px-8 py-4 font-semibold text-lg shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_20px_40px_rgba(251,191,36,0.3)] focus:outline-none focus:ring-4 focus:ring-amber-300/50">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>Hubungi Kami</span>
                    </a>
                </div>

                <div class="mt-6 text-white/70 text-sm">
                    <p>Telepon: +62 813-3916-3939 • Email: admin@cavebeachbungalow.com</p>
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
            const heroTitle = document.getElementById('hero-title');
            if (heroTitle) {
                gsap.to(heroTitle, {
                    opacity: 1,
                    duration: 1.2,
                    ease: 'power3.out',
                    delay: 0.3
                });
            }

            // Parallax halus pada decorative elements
            const decorativeElements = document.querySelectorAll('#hero .absolute');
            if (decorativeElements.length > 0 && window.ScrollTrigger) {
                decorativeElements.forEach((el, index) => {
                    gsap.to(el, {
                        y: (index + 1) * 20,
                        x: (index % 2 === 0 ? 1 : -1) * 15,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: '#hero',
                            start: 'top top',
                            end: 'bottom top',
                            scrub: true
                        }
                    });
                });
            }

            // Animasi achievement cards dengan stagger effect
            const achievementCards = gsap.utils.toArray('.achievement-card');
            if (achievementCards.length > 0 && window.ScrollTrigger) {
                achievementCards.forEach((card, index) => {
                    gsap.fromTo(card,
                        {
                            opacity: 0,
                            y: 60,
                            scale: 0.8
                        },
                        {
                            opacity: 1,
                            y: 0,
                            scale: 1,
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
                });
            }

            // Animasi value cards dengan rotation effect
            const valueCards = gsap.utils.toArray('.value-card');
            if (valueCards.length > 0 && window.ScrollTrigger) {
                valueCards.forEach((card, index) => {
                    gsap.fromTo(card,
                        {
                            opacity: 0,
                            y: 80,
                            rotationY: -15
                        },
                        {
                            opacity: 1,
                            y: 0,
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

            // Re-animasi hero title setelah navigasi
            const heroTitle = document.getElementById('hero-title');
            if (heroTitle) {
                gsap.set(heroTitle, { opacity: 0 });
                gsap.to(heroTitle, {
                    opacity: 1,
                    duration: 1.2,
                    ease: 'power3.out',
                    delay: 0.2
                });
            }

            // Re-animasi achievement cards setelah navigasi
            const achievementCards = gsap.utils.toArray('.achievement-card');
            if (achievementCards.length > 0) {
                gsap.set(achievementCards, { opacity: 0, y: 60, scale: 0.8 });
                achievementCards.forEach((card, index) => {
                    gsap.to(card, {
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        duration: 0.6,
                        delay: index * 0.08,
                        ease: 'power2.out'
                    });
                });
            }

            // Re-animasi value cards setelah navigasi
            const valueCards = gsap.utils.toArray('.value-card');
            if (valueCards.length > 0) {
                gsap.set(valueCards, { opacity: 0, y: 80, rotationY: -15 });
                valueCards.forEach((card, index) => {
                    gsap.to(card, {
                        opacity: 1,
                        y: 0,
                        rotationY: 0,
                        duration: 0.8,
                        delay: index * 0.1,
                        ease: 'power3.out'
                    });
                });
            }

            // Re-animasi team cards setelah navigasi
            const teamCards = gsap.utils.toArray('.team-card');
            if (teamCards.length > 0) {
                gsap.set(teamCards, { opacity: 0, y: 100, scale: 0.9 });
                teamCards.forEach((card, index) => {
                    gsap.to(card, {
                        opacity: 1,
                        y: 0,
                        scale: 1,
                        duration: 0.8,
                        delay: index * 0.15,
                        ease: 'power3.out'
                    });
                });
            }
        });
    })();
</script>