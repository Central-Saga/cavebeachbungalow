<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    public function definition(): array
    {
        $reservasi = Reservasi::factory()->create();

        return [
            'reservasi_id' => $reservasi->id,
            'nominal' => $this->faker->randomFloat(2, 100000, 500000),
            'bukti_path' => $this->faker->optional(0.8)->imageUrl(640, 480, 'business'),
            // 'status' => $this->faker->randomElement(['menunggu', 'terverifikasi', 'ditolak']),
            'status' => 'menunggu',
            'keterangan' => $this->faker->optional()->sentence(),
        ];
    }

    public function menunggu()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'menunggu',
            ];
        });
    }

    public function terverifikasi()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'terverifikasi',
            ];
        });
    }

    public function ditolak()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ditolak',
            ];
        });
    }
}
