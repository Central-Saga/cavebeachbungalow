<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold mb-4">Upload Bukti Pembayaran</h3>

    @if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('message') }}
    </div>
    @endif

    <div class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Harga Reservasi</label>
                <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($reservasi->total_harga, 0, ',', '.')
                    }}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Terbayar</label>
                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($reservasi->total_terbayar, 0, ',',
                    '.') }}</div>
            </div>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Sisa yang harus dibayar</h3>
                    <div class="text-2xl font-bold text-blue-900">Rp {{ number_format($reservasi->sisa_bayar, 0, ',',
                        '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="uploadBukti" class="space-y-4">
        <div>
            <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">
                Nominal Pembayaran <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                    Rp
                </span>
                <input type="number" id="nominal" wire:model="nominal"
                    class="pl-12 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Masukkan nominal pembayaran" min="1000" step="1000">
            </div>
            @error('nominal')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-2">
                Bukti Transfer <span class="text-red-500">*</span>
            </label>
            <input type="file" id="bukti_transfer" wire:model="bukti_transfer" accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            @error('bukti_transfer')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 10MB.</p>
        </div>

        <div>
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan (Opsional)
            </label>
            <textarea id="keterangan" wire:model="keterangan" rows="3"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Catatan tambahan tentang pembayaran ini..."></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-200"
                wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed">
                <span wire:loading.remove>Upload Bukti Pembayaran</span>
                <span wire:loading>Uploading...</span>
            </button>
        </div>
    </form>

    @if($reservasi->pembayarans->count() > 0)
    <div class="mt-8">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Riwayat Pembayaran</h4>
        <div class="space-y-3">
            @foreach($reservasi->pembayarans->sortByDesc('created_at') as $pembayaran)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        {{ $pembayaran->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="font-medium text-gray-900">
                        Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                    </div>
                    <div class="text-sm">
                        {!! $pembayaran->status_badge !!}
                    </div>
                </div>
                @if($pembayaran->keterangan)
                <div class="text-sm text-gray-600">
                    {{ $pembayaran->keterangan }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>