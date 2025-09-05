<?php

namespace Database\Factories;

use App\Models\Harga;
use App\Models\Kamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class HargaFactory extends Factory
{
    protected $model = Harga::class;

    public function definition(): array
    {
        return [
            'kamar_id' => Kamar::factory(),
            'tipe_paket' => $this->faker->randomElement(['harian', 'mingguan', 'bulanan']),
            'harga' => $this->faker->randomFloat(2, 50000, 2000000),
        ];
    }
}
