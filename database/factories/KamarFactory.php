<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class KamarFactory extends Factory
{
    protected $model = Kamar::class;

    public function definition(): array
    {
        $statuses = ['tersedia', 'terisi', 'perbaikan'];
        return [
            'tipe_kamar_id' => TipeKamar::factory(),
            'nomor_kamar' => $this->faker->unique()->bothify('KMR-###'),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
