<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Harga;
use App\Models\Kamar;

class HargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua kamar yang ada
        $kamars = Kamar::all();

        foreach ($kamars as $kamar) {
            // Untuk setiap kamar, buat harga untuk setiap tipe paket
            foreach (['harian', 'mingguan', 'bulanan'] as $tipe) {
                Harga::factory()->create([
                    'kamar_id' => $kamar->id,
                    'tipe_paket' => $tipe,
                ]);
            }
        }
    }
}
