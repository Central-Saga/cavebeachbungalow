<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipeKamar;


class Kamar extends Model
{
    // Relasi one-to-many ke harga
    public function hargas()
    {
        return $this->hasMany(\App\Models\Harga::class, 'kamar_id');
    }
    use HasFactory;

    protected $fillable = [
        'tipe_kamar_id',
        'nomor_kamar',
        'status',
    ];

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class);
    }

    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}
