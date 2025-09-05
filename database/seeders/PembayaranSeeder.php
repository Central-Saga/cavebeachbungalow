<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Reservasi;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua reservasi yang ada
        $reservasis = Reservasi::all();

        foreach ($reservasis as $reservasi) {
            // Buat 1-2 pembayaran untuk setiap reservasi
            $jumlahPembayaran = rand(1, 2);

            for ($i = 0; $i < $jumlahPembayaran; $i++) {
                // Status selalu menunggu untuk testing verifikasi
                $status = 'menunggu';
                $nominal = $i === 0 ? $reservasi->total_harga * 0.5 : $reservasi->total_harga * 0.5;

                Pembayaran::create([
                    'reservasi_id' => $reservasi->id,
                    'nominal' => $nominal,
                    'bukti_path' => null, // Untuk demo, tidak ada file
                    'status' => $status,
                    'keterangan' => $i === 0 ? 'Pembayaran DP' : 'Pelunasan',
                ]);
            }
        }
    }
}
