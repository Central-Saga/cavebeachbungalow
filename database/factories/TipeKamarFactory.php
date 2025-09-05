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
            'Studio',
            'Deluxe',
            'Suite',
            'Premium',
            'Executive',
            'Family',
            'Business',
            'Luxury',
            'Standard',
            'Superior'
        ];

        $descriptions = [
            'Kamar nyaman dengan fasilitas lengkap untuk kenyamanan maksimal',
            'Unit premium dengan desain modern dan fasilitas terbaik',
            'Kamar mewah dengan ruang yang luas dan pelayanan eksklusif',
            'Hunian berkualitas tinggi dengan standar internasional',
            'Unit executive yang cocok untuk pebisnis dan profesional',
            'Kamar keluarga yang nyaman dengan ruang yang lega',
            'Unit bisnis dengan fasilitas kerja yang lengkap',
            'Kamar mewah dengan sentuhan elegan dan mewah',
            'Kamar standar dengan kualitas terjamin dan harga terjangkau',
            'Unit superior dengan kualitas di atas standar'
        ];

        return [
            'nama_tipe' => $this->faker->unique()->randomElement($tipeNames),
            'kode_tipe' => strtoupper($this->faker->unique()->lexify('???')),
            'deskripsi' => $this->faker->randomElement($descriptions),
        ];
    }
}
