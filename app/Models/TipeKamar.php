<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FasilitasKamar;
use App\Models\SpekKamar;
use App\Models\Kamar;
use App\Models\GaleriKamar;
use Illuminate\Support\Facades\Storage;

class TipeKamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tipe',
        'kode_tipe',
        'deskripsi',
    ];

    // Relasi one-to-many ke galeri kamar
    public function galeriKamars()
    {
        return $this->hasMany(GaleriKamar::class, 'tipe_kamar_id');
    }

    public function spekKamars()
    {
        return $this->hasMany(SpekKamar::class);
    }

    // Relasi many-to-many ke fasilitas kamar
    public function fasilitasKamars()
    {
        return $this->belongsToMany(FasilitasKamar::class, 'spek_kamars', 'tipe_kamar_id', 'fasilitas_kamar_id');
    }

    /**
     * Tambahkan fasilitas ke tipe kamar.
     */
    public function tambahFasilitas($fasilitasKamarId)
    {
        return $this->fasilitasKamars()->attach($fasilitasKamarId);
    }

    /**
     * Hapus fasilitas dari tipe kamar.
     */
    public function hapusFasilitas($fasilitasKamarId)
    {
        return $this->fasilitasKamars()->detach($fasilitasKamarId);
    }

    // Relasi one-to-many ke kamar
    public function kamars()
    {
        return $this->hasMany(Kamar::class, 'tipe_kamar_id');
    }

    // Relasi one-to-many ke harga melalui kamars
    public function hargas()
    {
        return $this->hasManyThrough(Harga::class, Kamar::class, 'tipe_kamar_id', 'kamar_id');
    }

    /**
     * Generate nomor kamar otomatis berdasarkan tipe kamar
     */
    public function generateNomorKamar()
    {
        $lastKamar = $this->kamars()->orderBy('nomor_kamar', 'desc')->first();

        if (!$lastKamar) {
            // Jika belum ada kamar dengan tipe ini, mulai dari 1
            return $this->kode_tipe . '-001';
        }

        // Cek apakah nomor kamar sudah menggunakan format baru (kode_tipe-xxx)
        if (strpos($lastKamar->nomor_kamar, $this->kode_tipe . '-') === 0) {
            // Extract nomor dari nomor kamar terakhir dengan format baru
            $lastNumber = (int) substr($lastKamar->nomor_kamar, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            // Jika masih menggunakan format lama, mulai dari 1
            $nextNumber = 1;
        }

        // Format dengan leading zeros (001, 002, dst)
        return $this->kode_tipe . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method untuk handle cascade delete
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika tipe kamar dibuat, generate kode_tipe otomatis
        static::creating(function ($tipeKamar) {
            if (empty($tipeKamar->kode_tipe)) {
                $tipeKamar->kode_tipe = $tipeKamar->generateKodeTipe();
            }
        });

        // Ketika tipe kamar dihapus, hapus semua foto dari storage
        static::deleting(function ($tipeKamar) {
            foreach ($tipeKamar->galeriKamars as $foto) {
                $path = str_replace('/storage/', '', $foto->url_foto);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    \Log::info('Deleted photo from storage during cascade delete:', ['path' => $path]);
                }
            }
        });
    }

    /**
     * Generate kode_tipe otomatis dari nama_tipe
     */
    public function generateKodeTipe()
    {
        $kode_tipe = strtoupper(substr($this->nama_tipe, 0, 3));

        // Pastikan kode_tipe unik
        $counter = 1;
        $original_kode = $kode_tipe;
        while (static::where('kode_tipe', $kode_tipe)->exists()) {
            $kode_tipe = $original_kode . $counter;
            $counter++;
        }

        return $kode_tipe;
    }
}
