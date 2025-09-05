<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pembayaran extends Model
{
    protected $fillable = [
        'reservasi_id',
        'nominal',
        'bukti_path',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    // Scope untuk status
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeTerverifikasi($query)
    {
        return $query->where('status', 'terverifikasi');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    // Accessor untuk status badge
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'menunggu' => '<span class="badge badge-warning">Menunggu</span>',
            'terverifikasi' => '<span class="badge badge-success">Terverifikasi</span>',
            'ditolak' => '<span class="badge badge-danger">Ditolak</span>',
            default => '<span class="badge badge-secondary">Unknown</span>'
        };
    }

    // Hapus file bukti saat model dihapus
    protected static function booted()
    {
        static::deleting(function ($pembayaran) {
            if ($pembayaran->bukti_path && Storage::disk('public')->exists($pembayaran->bukti_path)) {
                Storage::disk('public')->delete($pembayaran->bukti_path);
            }
        });
    }
}
