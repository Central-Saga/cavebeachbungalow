<?php

namespace App\Http\Controllers;

use App\Models\TipeKamar;
use App\Models\GaleriKamar;
use App\Models\FasilitasKamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TipeKamarController extends Controller
{
    public function index()
    {
        $types = TipeKamar::withCount(['fasilitasKamars', 'kamars', 'galeriKamars'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'totalTypes' => TipeKamar::count(),
            'withFacilities' => TipeKamar::has('fasilitasKamars')->count(),
            'withoutFacilities' => TipeKamar::doesntHave('fasilitasKamars')->count(),
            'totalFacilities' => FasilitasKamar::count(),
            'totalRooms' => \App\Models\Kamar::count(),
            'totalGaleri' => GaleriKamar::count(),
            'withGaleri' => TipeKamar::has('galeriKamars')->count(),
        ];

        return view('livewire.pages.tipe-kamar.index', compact('types', 'stats'));
    }

    public function create()
    {
        $fasilitas = FasilitasKamar::orderBy('nama_fasilitas')->get();
        return view('livewire.pages.tipe-kamar.create', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'selectedFasilitas' => 'array',
            'galeriPhotos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        try {
            $tipeKamar = TipeKamar::create([
                'nama_tipe' => $request->nama_tipe,
                'deskripsi' => $request->deskripsi,
            ]);

            // Sync fasilitas
            if (!empty($request->selectedFasilitas)) {
                $tipeKamar->fasilitasKamars()->attach($request->selectedFasilitas);
            }

            // Upload galeri photos
            if ($request->hasFile('galeriPhotos')) {
                foreach ($request->file('galeriPhotos') as $photo) {
                    $path = $photo->store('galeri-kamar', 'public');

                    GaleriKamar::create([
                        'tipe_kamar_id' => $tipeKamar->id,
                        'url_foto' => Storage::url($path),
                    ]);
                }
            }

            return redirect()->route('admin.tipe-kamar.index')
                ->with('message', 'Tipe kamar berhasil dibuat!');
        } catch (\Exception $e) {
            Log::error('Gagal membuat tipe kamar', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal membuat tipe kamar: ' . $e->getMessage());
        }
    }

    public function edit(TipeKamar $tipeKamar)
    {
        $tipeKamar->load(['fasilitasKamars', 'galeriKamars']);
        $fasilitas = FasilitasKamar::orderBy('nama_fasilitas')->get();

        return view('livewire.pages.tipe-kamar.edit', compact('tipeKamar', 'fasilitas'));
    }

    public function update(Request $request, TipeKamar $tipeKamar)
    {
        $request->validate([
            'nama_tipe' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'selectedFasilitas' => 'array',
            'galeriPhotos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $tipeKamar->update([
                'nama_tipe' => $request->nama_tipe,
                'deskripsi' => $request->deskripsi,
            ]);

            // Sync fasilitas
            $tipeKamar->fasilitasKamars()->sync($request->selectedFasilitas ?? []);

            // Upload new galeri photos
            if ($request->hasFile('galeriPhotos')) {
                foreach ($request->file('galeriPhotos') as $photo) {
                    $path = $photo->store('galeri-kamar', 'public');

                    GaleriKamar::create([
                        'tipe_kamar_id' => $tipeKamar->id,
                        'url_foto' => Storage::url($path),
                    ]);
                }
            }

            return redirect()->route('admin.tipe-kamar.index')
                ->with('message', 'Tipe kamar berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Gagal update tipe kamar', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal memperbarui tipe kamar: ' . $e->getMessage());
        }
    }

    public function destroy(TipeKamar $tipeKamar)
    {
        try {
            // Check if has rooms
            if ($tipeKamar->kamars()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus: masih ada kamar dengan tipe ini.');
            }

            // Delete galeri photos from storage
            foreach ($tipeKamar->galeriKamars as $foto) {
                $path = str_replace('/storage/', '', $foto->url_foto);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Detach fasilitas
            $tipeKamar->fasilitasKamars()->detach();

            // Delete tipe kamar (galeri akan cascade delete)
            $tipeKamar->delete();

            return back()->with('message', 'Tipe kamar berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal hapus tipe kamar', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus tipe kamar: ' . $e->getMessage());
        }
    }

    public function deleteGaleriPhoto($photoId)
    {
        try {
            $foto = GaleriKamar::findOrFail($photoId);

            // Delete from storage
            $path = str_replace('/storage/', '', $foto->url_foto);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $foto->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Gagal hapus foto galeri', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
