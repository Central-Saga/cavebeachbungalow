@extends('components.layouts.admin.sidebar')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.pembayaran.index') }}"
            class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Daftar Pembayaran
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Detail Pembayaran</h1>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informasi Reservasi -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Reservasi</h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Kode Reservasi:</span>
                            <span class="text-sm text-gray-900">{{ $pembayaran->reservasi->kode_reservasi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Pelanggan:</span>
                            <span class="text-sm text-gray-900">{{ $pembayaran->reservasi->pelanggan->nama }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Email:</span>
                            <span class="text-sm text-gray-900">{{ $pembayaran->reservasi->pelanggan->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Telepon:</span>
                            <span class="text-sm text-gray-900">{{ $pembayaran->reservasi->pelanggan->telepon }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Tipe Kamar:</span>
                            <span class="text-sm text-gray-900">{{ $pembayaran->reservasi->kamar->tipeKamar->nama_tipe
                                }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Check In:</span>
                            <span class="text-sm text-gray-900">{{
                                $pembayaran->reservasi->tanggal_check_in->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Check Out:</span>
                            <span class="text-sm text-gray-900">{{
                                $pembayaran->reservasi->tanggal_check_out->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pembayaran -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Total Harga:</span>
                            <span class="text-lg font-bold text-gray-900">Rp {{
                                number_format($pembayaran->reservasi->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Total Terbayar:</span>
                            <span class="text-lg font-bold text-green-600">Rp {{
                                number_format($pembayaran->reservasi->total_terbayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Sisa Bayar:</span>
                            <span class="text-lg font-bold text-orange-600">Rp {{
                                number_format($pembayaran->reservasi->sisa_bayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-600">Status Reservasi:</span>
                            <span class="text-sm">
                                @if($pembayaran->reservasi->status_reservasi === 'terkonfirmasi')
                                <span class="badge badge-success">Terkonfirmasi</span>
                                @else
                                <span class="badge badge-warning">{{ ucfirst($pembayaran->reservasi->status_reservasi)
                                    }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Pembayaran -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Pembayaran Ini</h3>
                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Nominal:</span>
                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($pembayaran->nominal, 0, ',',
                            '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Status:</span>
                        <span class="text-sm">{!! $pembayaran->status_badge !!}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Tanggal Upload:</span>
                        <span class="text-sm text-gray-900">{{ $pembayaran->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($pembayaran->keterangan)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Keterangan:</span>
                        <span class="text-sm text-gray-900">{{ $pembayaran->keterangan }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bukti Transfer -->
            @if($pembayaran->bukti_path)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bukti Transfer</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <img src="{{ Storage::disk('public')->url($pembayaran->bukti_path) }}" alt="Bukti Transfer"
                        class="w-full h-auto max-w-md rounded-lg shadow-sm">
                    <div class="mt-3">
                        <a href="{{ Storage::disk('public')->url($pembayaran->bukti_path) }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                </path>
                            </svg>
                            Lihat Full Size
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Riwayat Pembayaran -->
            @if($pembayaran->reservasi->pembayarans->count() > 1)
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Pembayaran Lainnya</h3>
                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                    @foreach($pembayaran->reservasi->pembayarans->where('id', '!=',
                    $pembayaran->id)->sortByDesc('created_at') as $pembayaranLain)
                    <div class="flex justify-between items-center p-3 bg-white rounded border">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ $pembayaranLain->created_at->format('d/m/Y H:i')
                                }}</span>
                            <span class="text-sm font-medium">Rp {{ number_format($pembayaranLain->nominal, 0, ',', '.')
                                }}</span>
                            <span class="text-sm">{!! $pembayaranLain->status_badge !!}</span>
                        </div>
                        @if($pembayaranLain->keterangan)
                        <span class="text-sm text-gray-500">{{ $pembayaranLain->keterangan }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Aksi -->
            @if($pembayaran->status === 'menunggu')
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Verifikasi Pembayaran</h3>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.pembayaran.verifikasi', $pembayaran) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="terverifikasi">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition duration-200"
                            onclick="return confirm('Yakin ingin memverifikasi pembayaran ini?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Verifikasi
                        </button>
                    </form>

                    <form action="{{ route('admin.pembayaran.verifikasi', $pembayaran) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="ditolak">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition duration-200"
                            onclick="return confirm('Yakin ingin menolak pembayaran ini?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tolak
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection