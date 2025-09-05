<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Reservasi extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'kode_reservasi',
        'kamar_id',
        'pelanggan_id',
        'tipe_paket',
        'durasi',
        'tanggal_check_in',
        'tanggal_check_out',
        'total_harga',
        'status_reservasi',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Total terbayar (hanya yang terverifikasi)
    public function getTotalTerbayarAttribute()
    {
        return $this->pembayarans()->where('status', 'terverifikasi')->sum('nominal');
    }

    // Apakah sudah lunas
    public function getLunasAttribute()
    {
        return (float)$this->total_terbayar >= (float)$this->total_harga;
    }

    // Sisa yang harus dibayar
    public function getSisaBayarAttribute()
    {
        $sisa = (float)$this->total_harga - (float)$this->total_terbayar;
        return max(0, $sisa);
    }

    // Status pembayaran
    public function getStatusPembayaranAttribute()
    {
        if ($this->lunas) {
            return 'Lunas';
        } elseif ($this->total_terbayar > 0) {
            return 'DP';
        } else {
            return 'Belum Bayar';
        }
    }

    /**
     * Activity log configuration
     */
    protected $loggableAttributes = [
        'kode_reservasi',
        'kamar_id',
        'pelanggan_id',
        'tanggal_check_in',
        'tanggal_check_out',
        'total_harga',
        'status_reservasi',
    ];

    protected $logName = 'Reservasi';
}
