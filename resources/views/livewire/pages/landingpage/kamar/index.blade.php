<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\TipeKamar;

new #[Layout('components.layouts.landing')] class extends Component {
    public $tipeKamars;

    public function mount() {
        $this->tipeKamars = TipeKamar::with(['fasilitasKamars', 'kamars'])->get();
    }
}; ?>

<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Daftar Kamar Cave Beach Bungalow</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pilih tipe kamar sesuai kebutuhan Anda.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($tipeKamars as $tipe)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col items-center">
                <img src="{{ asset('vendor/log-viewer/img/gunung.jpg') }}" alt="{{ $tipe->nama_tipe }}" class="w-full h-40 object-cover object-center">
                <div class="p-6 w-full">
                    <h3 class="text-xl font-semibold mb-2 text-[#133E87]">{{ $tipe->nama_tipe }}</h3>
                    <ul class="text-gray-700 text-left mb-4 list-disc list-inside">
                        @foreach($tipe->fasilitasKamars as $fasilitas)
                            <li>{{ $fasilitas->nama_fasilitas }}</li>
                        @endforeach
                    </ul>
                    <div class="text-lg font-bold text-[#608BC1] mb-2">
                        @php
                            $harga = $tipe->kamars->min('harga');
                        @endphp
                        {{ $harga ? 'Rp ' . number_format($harga, 0, ',', '.') . ' / bulan' : '-' }}
                    </div>
                    <a href="{{ route('kamar.detail', $tipe->id) }}"
                       class="inline-block mt-2 px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg shadow hover:from-pink-600 hover:to-rose-600 transition-all duration-200">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

