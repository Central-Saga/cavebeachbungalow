<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\TipeKamar;
use App\Models\Kamar;

new #[Layout('components.layouts.landing')] class extends Component {
    public $tipeKamar;
    public $kamars;

    public function mount($id) {
        $this->tipeKamar = TipeKamar::with(['fasilitasKamars', 'kamars.galeriKamars', 'kamars.hargas'])
            ->findOrFail($id);
        $this->kamars = $this->tipeKamar->kamars;
    }
}; ?>

<div class="py-6">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col items-center">
            @php
                $foto = $tipeKamar->galeriKamars->count() ? $tipeKamar->galeriKamars->first()->url_foto : null;
            @endphp
            @if($foto)
                <img src="{{ asset($foto) }}" alt="Foto Kamar" class="w-full h-48 object-cover object-center">
            @else
                <img src="{{ asset('vendor/log-viewer/img/gunung.jpg') }}" alt="Foto Kamar" class="w-full h-48 object-cover object-center">
            @endif
            <div class="p-6 w-full">
                <h1 class="text-2xl font-bold text-[#133E87] mb-2">{{ $tipeKamar->nama_tipe }}</h1>
                <div class="mb-4 text-gray-700">{{ $tipeKamar->deskripsi }}</div>
                <h2 class="text-lg font-semibold mb-2 text-[#133E87]">Fasilitas Kamar</h2>
                <ul class="text-gray-700 mb-4 list-disc list-inside">
                    @foreach($tipeKamar->fasilitasKamars as $fasilitas)
                        <li>{{ $fasilitas->nama_fasilitas }}</li>
                    @endforeach
                </ul>
                <h2 class="text-lg font-semibold mb-2 text-[#133E87]">Harga Kamar</h2>
                <ul class="text-gray-700 mb-4 list-disc list-inside">
                    @php
                        $paketList = ['harian', 'mingguan', 'bulanan'];
                        $hargaKamar = [];
                        foreach ($paketList as $paket) {
                            $hargaKamar[$paket] = null;
                        }
                        foreach ($kamars as $kamar) {
                            foreach ($kamar->hargas as $harga) {
                                if (in_array($harga->tipe_paket, $paketList)) {
                                    if (is_null($hargaKamar[$harga->tipe_paket]) || $harga->harga < $hargaKamar[$harga->tipe_paket]) {
                                        $hargaKamar[$harga->tipe_paket] = $harga->harga;
                                    }
                                }
                            }
                        }
                    @endphp
                    @foreach($paketList as $paket)
                        <li>
                            {{ ucfirst($paket) }}:
                            @if($hargaKamar[$paket])
                                <span class="font-bold text-[#608BC1]">Rp {{ number_format($hargaKamar[$paket], 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
