@extends('components.layouts.admin.sidebar')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Pembayaran</h1>
        <div class="flex space-x-2">
            <button onclick="filterByStatus('all')"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                Semua
            </button>
            <button onclick="filterByStatus('menunggu')"
                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-200">
                Menunggu
            </button>
            <button onclick="filterByStatus('terverifikasi')"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200">
                Terverifikasi
            </button>
            <button onclick="filterByStatus('ditolak')"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200">
                Ditolak
            </button>
        </div>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kode Reservasi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pelanggan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nominal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Upload
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="pembayaran-table">
                    @forelse($pembayarans as $pembayaran)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $pembayaran->reservasi->kode_reservasi }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $pembayaran->reservasi->kamar->tipeKamar->nama_tipe }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $pembayaran->reservasi->pelanggan->nama }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $pembayaran->reservasi->pelanggan->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                            </div>
                            @if($pembayaran->keterangan)
                            <div class="text-sm text-gray-500">
                                {{ $pembayaran->keterangan }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {!! $pembayaran->status_badge !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pembayaran->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.pembayaran.show', $pembayaran) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    Detail
                                </a>
                                @if($pembayaran->status === 'menunggu')
                                <button onclick="verifikasiPembayaran({{ $pembayaran->id }}, 'terverifikasi')"
                                    class="text-green-600 hover:text-green-900">
                                    Verifikasi
                                </button>
                                <button onclick="verifikasiPembayaran({{ $pembayaran->id }}, 'ditolak')"
                                    class="text-red-600 hover:text-red-900">
                                    Tolak
                                </button>
                                @endif
                                <form action="{{ route('admin.pembayaran.destroy', $pembayaran) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pembayaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $pembayarans->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function filterByStatus(status) {
    if (status === 'all') {
        window.location.reload();
        return;
    }

    fetch(`/admin/pembayaran/status/${status}`)
        .then(response => response.json())
        .then(data => {
            updateTable(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateTable(pembayarans) {
    const tbody = document.getElementById('pembayaran-table');
    let html = '';

    if (pembayarans.length === 0) {
        html = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data pembayaran</td></tr>';
    } else {
        pembayarans.forEach(pembayaran => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${pembayaran.reservasi.kode_reservasi}</div>
                        <div class="text-sm text-gray-500">${pembayaran.reservasi.kamar.tipe_kamar.nama_tipe}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${pembayaran.reservasi.pelanggan.nama}</div>
                        <div class="text-sm text-gray-500">${pembayaran.reservasi.pelanggan.email}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Rp ${Number(pembayaran.nominal).toLocaleString('id-ID')}</div>
                        ${pembayaran.keterangan ? `<div class="text-sm text-gray-500">${pembayaran.keterangan}</div>` : ''}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${getStatusBadge(pembayaran.status)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(pembayaran.created_at).toLocaleDateString('id-ID')}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="/admin/pembayaran/${pembayaran.id}" class="text-blue-600 hover:text-blue-900">Detail</a>
                            ${pembayaran.status === 'menunggu' ? `
                                <button onclick="verifikasiPembayaran(${pembayaran.id}, 'terverifikasi')" class="text-green-600 hover:text-green-900">Verifikasi</button>
                                <button onclick="verifikasiPembayaran(${pembayaran.id}, 'ditolak')" class="text-red-600 hover:text-red-900">Tolak</button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    tbody.innerHTML = html;
}

function getStatusBadge(status) {
    const badges = {
        'menunggu': '<span class="badge badge-warning">Menunggu</span>',
        'terverifikasi': '<span class="badge badge-success">Terverifikasi</span>',
        'ditolak': '<span class="badge badge-danger">Ditolak</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
}

function verifikasiPembayaran(id, status) {
    if (confirm(`Yakin ingin ${status === 'terverifikasi' ? 'memverifikasi' : 'menolak'} pembayaran ini?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/pembayaran/${id}/verifikasi`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;

        form.appendChild(csrfToken);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection