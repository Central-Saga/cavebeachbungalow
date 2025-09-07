<x-layouts.landing>
    <div class="min-h-screen bg-gray-50 pt-32">
        <!-- Hero Section with Parallax Effect -->
        <div
            class="relative flex content-center items-center justify-center py-16 bg-cover bg-center h-[60vh]"
            x-data="{}"
            x-init="$el.style.backgroundImage = 'url(' + '{{ asset('img/galeri_landing/641c0f46.jpeg') }}' + ')'"
        >
            <div class="absolute inset-0 bg-[#133E87]/60"></div>
            <div class="container relative mx-auto px-4 z-10">
                <div class="flex flex-wrap justify-center">
                    <div class="w-full lg:w-6/12 px-4 text-center">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white">Gallery</h1>
                        <p class="text-lg text-white/90">
                            Explore the beauty of our Villas through stunning imagery
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Grid Section -->
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Gallery Items -->
                @php
                    $galleryImages = [
                        '08618c09.jpeg',
                        '1c812388.jpeg',
                        '2348512d.jpeg',
                        '37660291.jpeg',
                        '3adb2424.jpeg',
                        '5b08a85c.jpeg',
                        '5d1afa35.jpeg',
                        '5d55e276.jpeg',
                        '638a4bfa.jpeg',
                        '641c0f46.jpeg',
                        '649730255.jpeg',
                        '6a9096c1.jpeg',
                        '6b9bb2ae.jpeg',
                        '73d31243.jpeg',
                        '8cbc5921.jpeg',
                        '907a1356.jpeg',
                        '991d107e.jpeg',
                        'a3179cb1.jpeg',
                        'b7c08b60.jpeg',
                        'cave-bugalow-.jpeg',
                        'd27fb1ac.jpeg',
                        'e379a61b.jpeg',
                        'e57b75d5 2.jpeg',
                        'e5ed10b4.jpeg',
                        'eb4647a1.jpeg',
                    ];
                @endphp

                @foreach($galleryImages as $image)
                    <div class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition-all duration-500">
                        <img
                            src="{{ asset('img/galeri_landing/' . $image) }}"
                            alt="Cave Beach Bungalow Villa Image"
                            class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#133E87]/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col items-center justify-end p-4">
                            <span class="text-white text-lg font-semibold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                Cave Beach Bungalow
                            </span>
                            <span class="text-white/80 text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700 delay-100">
                                Luxury Villa Experience
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="bg-[#608BC1]/10 py-16">
            <div class="container mx-auto px-4">
                <div class="flex flex-col items-center justify-center text-center">
                    <h2 class="text-3xl font-bold text-[#133E87] mb-4">Experience Luxury Firsthand</h2>
                    <p class="text-lg text-gray-700 mb-8 max-w-3xl">
                        Book your stay at Cave Beach Bungalow and create memories that will last a lifetime in our stunning villa accommodations.
                    </p>
                    <a
                        href="{{ route('landingpage.tipe-kamar') }}"
                        class="px-8 py-3 bg-[#133E87] hover:bg-[#0f326e] text-white font-medium rounded-full transition-all duration-300 transform hover:scale-105 flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <span>View Our Villas</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.landing>
