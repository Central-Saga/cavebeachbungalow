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
                'nama_tipe' => 'Studio',
                'kode_tipe' => 'STD',
                'deskripsi' => 'Kamar studio yang nyaman dan efisien, cocok untuk solo traveler atau pasangan muda. Dilengkapi dengan fasilitas lengkap untuk kenyamanan maksimal.',
            ],
            [
                'nama_tipe' => 'Deluxe',
                'kode_tipe' => 'DLX',
                'deskripsi' => 'Kamar deluxe dengan ruang yang lebih luas dan fasilitas premium. Desain modern dengan sentuhan elegan untuk pengalaman tinggal yang memuaskan.',
            ],
            [
                'nama_tipe' => 'Suite',
                'kode_tipe' => 'SUT',
                'deskripsi' => 'Kamar suite mewah dengan ruang yang sangat luas dan fasilitas lengkap. Cocok untuk keluarga atau tamu VIP yang menginginkan kenyamanan maksimal.',
            ],
            [
                'nama_tipe' => 'Premium',
                'kode_tipe' => 'PRM',
                'deskripsi' => 'Unit premium dengan standar kualitas tinggi dan fasilitas terbaik. Desain kontemporer dengan sentuhan mewah untuk pengalaman tinggal eksklusif.',
            ],
            [
                'nama_tipe' => 'Executive',
                'kode_tipe' => 'EXC',
                'deskripsi' => 'Kamar executive yang dirancang khusus untuk pebisnis dan profesional. Dilengkapi dengan ruang kerja dan fasilitas bisnis yang lengkap.',
            ],
        ];

        foreach ($tipeKamars as $tipeKamar) {
            TipeKamar::create($tipeKamar);
        }
    }
}
