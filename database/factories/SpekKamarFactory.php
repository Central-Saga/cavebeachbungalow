<?php


namespace Database\Factories;
use App\Models\SpekKamar;
use App\Models\FasilitasKamar;
use App\Models\TipeKamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpekKamarFactory extends Factory
{
    protected $model = SpekKamar::class;

    public function definition(): array
    {
        return [
            'fasilitas_kamar_id' => FasilitasKamar::factory(),
            'tipe_kamar_id' => TipeKamar::factory(),
        ];
    }
}
