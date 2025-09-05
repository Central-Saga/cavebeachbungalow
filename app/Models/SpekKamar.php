<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FasilitasKamar;
use App\Models\TipeKamar;

class SpekKamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasilitas_kamar_id',
        'tipe_kamar_id',
    ];

    public function fasilitasKamar()
    {
        return $this->belongsTo(FasilitasKamar::class);
    }

    public function tipeKamar()
    {
        return $this->belongsTo(TipeKamar::class);
    }
}
