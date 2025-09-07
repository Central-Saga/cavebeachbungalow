<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Membuat user admin, owner, dan pengunjung menggunakan factory
         */
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin',
        ]);

        User::factory()->pengunjung()->create([
            'email' => 'pengunjung@example.com',
            'name' => 'Pengunjung',
        ]);

        // Create additional pengunjung users with pelanggan data
        User::factory()->pengunjung()->count(10)->create();
    }
}
