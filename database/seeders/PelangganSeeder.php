<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with Pengunjung role that don't have pelanggan data yet
        $usersWithoutPelanggan = User::role('Pengunjung')
            ->whereDoesntHave('pelanggan')
            ->get();

        // Create pelanggan data for users that don't have it
        foreach ($usersWithoutPelanggan as $user) {
            Pelanggan::factory()->create([
                'user_id' => $user->id
            ]);
        }

        // Alternatively, you can use this approach to create pelanggan for specific users
        // User::role('Pengunjung')->get()->each(function ($user) {
        //     if (!$user->pelanggan) {
        //         Pelanggan::factory()->create(['user_id' => $user->id]);
        //     }
        // });
    }
}
