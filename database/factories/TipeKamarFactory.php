<?php


namespace Database\Factories;

use App\Models\TipeKamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipeKamarFactory extends Factory
{
    protected $model = TipeKamar::class;

    public function definition(): array
    {
        $tipeNames = [
            'Standard Twin 2 Twin Beds',
            'Deluxe Garden View',
            'Deluxe 1 Twin Bed Garden View',
            'Deluxe Ocean View'
        ];

        $descriptions = [
            'Kamar nyaman dengan dua tempat tidur twin, ideal untuk teman atau keluarga yang menginap bersama.',
            'Kamar deluxe dengan pemandangan taman yang asri.',
            'Kamar deluxe dengan satu tempat tidur twin dan pemandangan taman.',
            'Kamar deluxe premium dengan pemandangan laut yang memukau.'
        ];

        return [
            'nama_tipe' => $this->faker->unique()->randomElement($tipeNames),
            'kode_tipe' => strtoupper($this->faker->unique()->lexify('???')),
            'deskripsi' => $this->faker->randomElement($descriptions),
        ];
    }
}
