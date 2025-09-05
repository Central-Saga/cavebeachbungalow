<?php

namespace Database\Factories;

use App\Models\GaleriKamar;
use App\Models\TipeKamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class GaleriKamarFactory extends Factory
{
    protected $model = GaleriKamar::class;

    public function definition(): array
    {
        // Daftar foto dari folder asset
        $assetPhotos = [
            '/img/asset/1.jpg',
            '/img/asset/2.jpg',
            '/img/asset/3.jpg',
            '/img/asset/4.jpg',
            '/img/asset/5.jpg',
            '/img/asset/6.jpg',
        ];

        return [
            'tipe_kamar_id' => TipeKamar::factory(),
            'url_foto' => $this->faker->randomElement($assetPhotos),
            'judul_foto' => $this->faker->sentence(3),
            'deskripsi_foto' => $this->faker->sentence(6),
        ];
    }
}
