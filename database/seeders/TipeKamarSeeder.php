<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipeKamar;

class TipeKamarSeeder extends Seeder
{
    public function run(): void
    {
        $tipeKamars = [
            [
                'nama_tipe' => 'Standard Twin 2 Twin Beds',
                'kode_tipe' => 'STW',
                'deskripsi' => 'Kamar nyaman dengan dua tempat tidur twin, ideal untuk teman atau keluarga yang menginap bersama. Dilengkapi dengan fasilitas lengkap untuk kenyamanan maksimal.',
            ],
            [
                'nama_tipe' => 'Deluxe Garden View',
                'kode_tipe' => 'DGV',
                'deskripsi' => 'Kamar deluxe dengan pemandangan taman yang asri. Nikmati suasana tenang dan menyegarkan dengan akses ke pemandangan taman resort.',
            ],
            [
                'nama_tipe' => 'Deluxe 1 Twin Bed Garden View',
                'kode_tipe' => 'DTG',
                'deskripsi' => 'Kamar deluxe dengan satu tempat tidur twin dan pemandangan taman. Cocok untuk solo traveler yang menginginkan kenyamanan dengan view taman yang menenangkan.',
            ],
            [
                'nama_tipe' => 'Deluxe Ocean View',
                'kode_tipe' => 'DOV',
                'deskripsi' => 'Kamar deluxe premium dengan pemandangan laut yang memukau. Nikmati panorama laut yang indah langsung dari kamar Anda.',
            ],
        ];

        foreach ($tipeKamars as $tipeKamar) {
            TipeKamar::create($tipeKamar);
        }
    }
}
