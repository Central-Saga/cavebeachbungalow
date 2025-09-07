<nav id="main-navbar"
    class="bg-white/90 backdrop-blur-md shadow-lg fixed z-50 hover:bg-white/95 hover:shadow-xl hover:scale-105"
    style="top: 1rem; left: 50%; transform: translateX(-50%); width: calc(100% - 2rem); max-width: 1200px; border-radius: 9999px; margin: 0 1rem;">
    <div class="px-6 py-4">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="#" class="flex items-center space-x-2 group">
                    <div class="w-10 h-10 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                    <img src="{{ asset('img/logo_cavebeach.PNG') }}" 
                        alt="Cave Beach Bungalow Logo" 
                        class="w-full h-full object-contain">
                    </div>
                    <span
                        class="text-lg font-bold text-[#133E87] transition-all duration-300 group-hover:text-[#608BC1] group-hover:scale-105 whitespace-nowrap"> Cave Beach Bungalow
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:block flex-grow">
                <div class="flex items-center justify-center space-x-8">
                    <a href="{{ route('landingpage.home') }}"
                        class="text-[#133E87] hover:text-[#608BC1] px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">Beranda</a>
                    <a href="{{ route('landingpage.gallery') }}"
                        class="text-[#133E87] hover:text-[#608BC1] px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">Gallery</a>
                    <a href="{{ route('landingpage.tipe-kamar') }}"
                        class="text-[#133E87] hover:text-[#608BC1] px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">Villas</a>
                    <a href="{{ route('landingpage.aboutme') }}"
                        class="text-[#133E87] hover:text-[#608BC1] px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">About Us</a>
                    <a href="{{ route('landingpage.contact') }}"
                        class="text-[#133E87] hover:text-[#608BC1] px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">Contact
                        Us</a>
                </div>
            </div>

            <!-- Auth Buttons / User Menu -->
            <div class="hidden lg:block flex-shrink-0 flex items-center space-x-3">
                @auth
                <!-- User is logged in -->
                <div class="relative group">
                    <button type="button"
                        class="flex items-center space-x-2 text-[#133E87] hover:text-[#608BC1] px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">
                        <i class="fas fa-user-circle text-lg"></i>
                        <span>{{ auth()->user()->name }}</span>
                        <i
                            class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100 origin-top-right">
                        <div class="py-2">
                            @if(auth()->user()->hasRole('Admin'))
                            <a href="{{ route('admin.dashboard') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#608BC1]/10 hover:text-[#133E87] transition-colors duration-200">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin
                            </a>
                            @endif
                            <a href="{{ route('landingpage.home') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#608BC1]/10 hover:text-[#133E87] transition-colors duration-200">
                                <i class="fas fa-home mr-2"></i>Beranda
                            </a>
                            @if(auth()->user()->pelanggan)
                            <a href="{{ route('landingpage.reservasi-saya') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#608BC1]/10 hover:text-[#133E87] transition-colors duration-200">
                                <i class="fas fa-calendar-check mr-2"></i>Reservasi Saya
                            </a>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <!-- User is not logged in -->
                <a href="{{ route('login') }}"
                    class="text-[#133E87] hover:text-[#608BC1] px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">
                    Login
                </a>
                <a href="{{ route('register') }}"
                    class="bg-[#608BC1] hover:bg-[#4a6a99] text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 shadow-md hover:scale-110 hover:shadow-lg hover:-translate-y-1 whitespace-nowrap">
                    Register
                </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="lg:hidden flex-shrink-0">
                <button type="button"
                    class="text-[#133E87] hover:text-[#608BC1] focus:outline-none focus:text-[#608BC1] transition-all duration-300 hover:scale-110"
                    onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-lg transition-all duration-300 hover:rotate-90"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="lg:hidden hidden absolute top-full left-0 right-0 mt-2" id="mobile-menu">
            <div
                class="mx-4 px-4 pt-3 pb-4 space-y-2 bg-white/95 backdrop-blur-md rounded-2xl shadow-lg border border-gray-100">
                <a href="{{ route('landingpage.home') }}"
                    class="text-[#133E87] hover:text-[#608BC1] block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Beranda</a>
                <a href="{{ route('landingpage.gallery') }}"
                    class="text-[#133E87] hover:text-[#608BC1] px-3 py-2 rounded-md text-sm font-medium transition-all duration-300 hover:scale-110 hover:bg-[#608BC1]/10 whitespace-nowrap">Gallery</a>
                <a href="{{ route('landingpage.tipe-kamar') }}"
                    class="text-[#133E87] hover:text-[#608BC1] block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Villas</a>
                <a href="{{ route('landingpage.aboutme') }}"
                    class="text-[#133E87] hover:text-[#608BC1] block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Tentang</a>
                <a href="{{ route('landingpage.contact') }}"
                    class="text-[#133E87] hover:text-[#608BC1] block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Contact
                    Us</a>
                @auth
                <!-- User is logged in (Mobile) -->
                <div class="pt-3 space-y-2">
                    <div class="px-6 py-2 text-center">
                        <div class="flex items-center justify-center space-x-2 text-[#133E87]">
                            <i class="fas fa-user-circle text-lg"></i>
                            <span class="font-medium">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                    @if(auth()->user()->hasRole('Admin'))
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-[#133E87] hover:text-[#608BC1] block px-6 py-2 rounded-full text-sm font-medium transition-colors duration-200 text-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard Admin
                    </a>
                    @endif

                    @if(auth()->user()->pelanggan)
                    <a href="{{ route('landingpage.reservasi-saya') }}"
                        class="text-[#133E87] hover:text-[#608BC1] block px-6 py-2 rounded-full text-sm font-medium transition-colors duration-200 text-center">
                        <i class="fas fa-calendar-check mr-2"></i>Reservasi Saya
                    </a>
                    @endif
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit"
                            class="w-full text-center px-6 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200 rounded-full">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
                @else
                <!-- User is not logged in (Mobile) -->
                <div class="pt-3 space-y-2">
                    <a href="{{ route('login') }}"
                        class="text-[#133E87] hover:text-[#608BC1] block px-6 py-2 rounded-full text-sm font-medium transition-colors duration-200 text-center">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="bg-[#608BC1] hover:bg-[#4a6a99] text-white px-6 py-2 rounded-full text-sm font-medium transition-colors duration-200 shadow-md block text-center">
                        Register
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }

    // Initialize navbar functionality
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('main-navbar');
        let isScrolled = false;

        // Check if GSAP is available
        if (!window.gsap) {
            console.error('GSAP not available');
            return;
        }

        // Close mobile menu when clicking on links
        const navLinks = document.querySelectorAll('nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobile-menu');
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Close mobile menu when clicking on logout button
        const logoutButtons = document.querySelectorAll('button[type="submit"]');
        logoutButtons.forEach(button => {
            button.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuButton = document.querySelector('button[onclick="toggleMobileMenu()"]');

            if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });

        // Navbar scroll animation
        function updateNavbar() {
            const scrollY = window.scrollY;

            if (scrollY > 50 && !isScrolled) {
                isScrolled = true;

                // Use GSAP if available, otherwise use CSS transitions
                if (window.gsap) {
                    gsap.to(navbar, {
                        duration: 0.8,
                        ease: "power3.out",
                        css: {
                            top: "0px",
                            left: "0px",
                            transform: "translateX(0%)",
                            width: "100%",
                            maxWidth: "none",
                            borderRadius: "0px",
                            margin: "0px",
                            backgroundColor: "rgba(255, 255, 255, 0.95)"
                        }
                    });
                } else {
                    // Fallback CSS animation
                    navbar.style.transition = 'all 0.8s ease';
                    navbar.style.top = "0px";
                    navbar.style.left = "0px";
                    navbar.style.transform = "translateX(0%)";
                    navbar.style.width = "100%";
                    navbar.style.maxWidth = "none";
                    navbar.style.borderRadius = "0px";
                    navbar.style.margin = "0px";
                    navbar.style.backgroundColor = "rgba(255, 255, 255, 0.95)";
                }

            } else if (scrollY <= 50 && isScrolled) {
                isScrolled = false;

                // Use GSAP if available, otherwise use CSS transitions
                if (window.gsap) {
                    gsap.to(navbar, {
                        duration: 0.8,
                        ease: "power3.out",
                        css: {
                            top: "1rem",
                            left: "50%",
                            transform: "translateX(-50%)",
                            width: "calc(100% - 2rem)",
                            maxWidth: "1200px",
                            borderRadius: "9999px",
                            margin: "0 1rem",
                            backgroundColor: "rgba(255, 255, 255, 0.9)"
                        }
                    });
                } else {
                    // Fallback CSS animation
                    navbar.style.transition = 'all 0.8s ease';
                    navbar.style.top = "1rem";
                    navbar.style.left = "50%";
                    navbar.style.transform = "translateX(-50%)";
                    navbar.style.width = "calc(100% - 2rem)";
                    navbar.style.maxWidth = "1200px";
                    navbar.style.borderRadius = "9999px";
                    navbar.style.margin = "0 1rem";
                    navbar.style.backgroundColor = "rgba(255, 255, 255, 0.9)";
                }
            }
        }

        // Throttled scroll listener
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(() => {
                    updateNavbar();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Test GSAP immediately
        setTimeout(() => {
            if (window.gsap && navbar) {
                gsap.to(navbar, {duration: 0.1, scale: 1.01, yoyo: true, repeat: 1});
            }
        }, 1000);
    });
</script>
