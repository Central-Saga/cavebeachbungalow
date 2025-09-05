<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservasi;
use App\Models\Kamar;
use App\Models\Pelanggan;

class ReservasiSeeder extends Seeder
{
    public function run(): void
    {
        $kamars = Kamar::all();
        $pelanggans = Pelanggan::all();
        if ($kamars->count() === 0) {
            $kamars = Kamar::factory()->count(5)->create();
        }
        if ($pelanggans->count() === 0) {
            $pelanggans = Pelanggan::factory()->count(5)->create();
        }
        foreach ($kamars as $kamar) {
            Reservasi::factory()->count(2)->create([
                'kamar_id' => $kamar->id,
                'pelanggan_id' => $pelanggans->random()->id,
            ]);
        }
    }
}
