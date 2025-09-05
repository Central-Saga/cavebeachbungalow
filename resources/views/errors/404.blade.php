<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Pondok Putri</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-indigo-600 mb-4">404</h1>
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Maaf, halaman yang Anda cari tidak dapat ditemukan.
                Mungkin halaman tersebut telah dipindahkan atau dihapus.
            </p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('landingpage.home') }}"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="text-sm text-gray-500">
                Atau gunakan menu navigasi di atas untuk menjelajahi situs kami
            </div>
        </div>
    </div>
</body>

</html>