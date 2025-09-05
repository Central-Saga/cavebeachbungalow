<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            PelangganSeeder::class,
            TipeKamarSeeder::class,
            FasilitasKamarSeeder::class,
            SpekKamarSeeder::class,
            GaleriKamarSeeder::class,
            KamarSeeder::class,
            // ReservasiSeeder::class,
            HargaSeeder::class,
            // PembayaranSeeder::class,
        ]);
    }
}
