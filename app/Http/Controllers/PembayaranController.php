<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::with(['reservasi.pelanggan', 'reservasi.kamar'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['reservasi.pelanggan', 'reservasi.kamar']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function verifikasi(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'status' => 'required|in:terverifikasi,ditolak',
            'keterangan_admin' => 'nullable|string|max:500',
        ]);

        $pembayaran->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan_admin,
        ]);

        // Jika pembayaran terverifikasi, cek apakah reservasi sudah lunas
        if ($request->status === 'terverifikasi') {
            $reservasi = $pembayaran->reservasi;

            // Jika sudah lunas, update status reservasi
            if ($reservasi->lunas) {
                $reservasi->update(['status_reservasi' => 'terkonfirmasi']);
                session()->flash('message', 'Pembayaran diverifikasi dan reservasi telah dikonfirmasi (LUNAS).');
            } else {
                session()->flash('message', 'Pembayaran diverifikasi. Reservasi masih menunggu pelunasan.');
            }
        } else {
            session()->flash('message', 'Pembayaran ditolak.');
        }

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Status pembayaran berhasil diupdate.');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        // Hapus file bukti jika ada
        if ($pembayaran->bukti_path && Storage::disk('public')->exists($pembayaran->bukti_path)) {
            Storage::disk('public')->delete($pembayaran->bukti_path);
        }

        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    // API untuk mendapatkan data pembayaran berdasarkan status
    public function getByStatus($status)
    {
        $pembayarans = Pembayaran::with(['reservasi.pelanggan', 'reservasi.kamar'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pembayarans);
    }

    // Dashboard stats
    public function getStats()
    {
        $stats = [
            'total_pembayaran' => Pembayaran::count(),
            'menunggu_verifikasi' => Pembayaran::where('status', 'menunggu')->count(),
            'terverifikasi' => Pembayaran::where('status', 'terverifikasi')->count(),
            'ditolak' => Pembayaran::where('status', 'ditolak')->count(),
            'total_nominal_terverifikasi' => Pembayaran::where('status', 'terverifikasi')->sum('nominal'),
        ];

        return response()->json($stats);
    }
}
