<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GaleriKamar extends Model
{
    use HasFactory;

    protected $table = 'galeri_kamars';
    protected $fillable = [
        'tipe_kamar_id',
        'url_foto',
    ];

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class);
    }

    /**
     * Boot method untuk handle cascade delete
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika galeri kamar dihapus, hapus file dari storage
        static::deleting(function ($galeriKamar) {
            $path = str_replace('/storage/', '', $galeriKamar->url_foto);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                \Log::info('Deleted photo from storage during GaleriKamar delete:', ['path' => $path]);
            }
        });
    }
}
