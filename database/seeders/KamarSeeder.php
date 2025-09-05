<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kamar;
use App\Models\TipeKamar;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        $tipeKamars = TipeKamar::all();
        if ($tipeKamars->count() === 0) {
            $tipeKamars = TipeKamar::factory()->count(3)->create();
        }
        foreach ($tipeKamars as $tipeKamar) {
            Kamar::factory()->count(5)->create([
                'tipe_kamar_id' => $tipeKamar->id,
                // harga sudah di-generate oleh factory
            ]);
        }
    }
}
