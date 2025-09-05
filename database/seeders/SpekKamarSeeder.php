<?php


namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SpekKamar;
use App\Models\FasilitasKamar;
use App\Models\TipeKamar;

class SpekKamarSeeder extends Seeder
{
    public function run(): void
    {
        $fasilitas = FasilitasKamar::all();
        $tipeKamars = TipeKamar::all();

        foreach ($tipeKamars as $tipe) {
            $randomFasilitas = $fasilitas->random(rand(1, $fasilitas->count()));
            foreach ($randomFasilitas as $fasilitasKamar) {
                SpekKamar::create([
                    'fasilitas_kamar_id' => $fasilitasKamar->id,
                    'tipe_kamar_id' => $tipe->id,
                ]);
            }
        }
    }
}
