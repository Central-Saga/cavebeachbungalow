<?php

namespace Database\Factories;

use App\Models\Reservasi;
use App\Models\Kamar;
use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservasiFactory extends Factory
{
    protected $model = Reservasi::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $checkOut = (clone $checkIn)->modify('+' . rand(1, 5) . ' days');
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $tipePaket = $this->faker->randomElement(['harian', 'mingguan', 'bulanan']);

        return [
            'kode_reservasi' => strtoupper($this->faker->unique()->bothify('RSV-#####')),
            'kamar_id' => Kamar::factory(),
            'pelanggan_id' => Pelanggan::factory(),
            'tipe_paket' => $tipePaket,
            'durasi' => $this->faker->numberBetween(1, 30),
            'tanggal_check_in' => $checkIn->format('Y-m-d'),
            'tanggal_check_out' => $checkOut->format('Y-m-d'),
            'total_harga' => $this->faker->numberBetween(500000, 5000000),
            'status_reservasi' => $this->faker->randomElement($statuses),
        ];
    }
}
