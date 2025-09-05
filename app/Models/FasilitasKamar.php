<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasKamar extends Model
{
    use HasFactory;

    protected $table = 'fasilitas_kamars';

    protected $fillable = [
        'nama_fasilitas',
    ];

    public function spekKamars()
    {
        return $this->hasMany(SpekKamar::class);
    }

    // Relasi many-to-many ke tipe kamar
    public function tipeKamars()
    {
        return $this->belongsToMany(TipeKamar::class, 'spek_kamars', 'fasilitas_kamar_id', 'tipe_kamar_id');
    }
}
