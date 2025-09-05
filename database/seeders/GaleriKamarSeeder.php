<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GaleriKamar;
use App\Models\TipeKamar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class GaleriKamarSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar foto dari folder asset
        $assetPhotos = [
            '1.jpg',
            '2.jpg',
            '3.jpg',
            '4.jpg',
            '5.jpg',
            '6.jpg',
        ];

        // Pastikan folder storage ada
        $storagePath = 'public/galeri-kamar';
        if (!Storage::exists($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        // Ambil semua tipe kamar
        $tipeKamars = TipeKamar::all();

        foreach ($tipeKamars as $tipeKamar) {
            // Buat 2-3 foto untuk setiap tipe kamar
            $photoCount = rand(2, 3);

            for ($i = 0; $i < $photoCount; $i++) {
                // Pilih foto random dari asset
                $selectedPhoto = $assetPhotos[array_rand($assetPhotos)];
                $sourcePath = public_path('img/asset/' . $selectedPhoto);
                $fileName = $tipeKamar->kode_tipe . '_' . ($i + 1) . '_' . time() . '.jpg';
                $destinationPath = $storagePath . '/' . $fileName;

                // Salin foto dari asset ke storage
                if (File::exists($sourcePath)) {
                    $fileName = $tipeKamar->kode_tipe . '_' . ($i + 1) . '_' . time() . '.jpg';
                    $destinationPath = storage_path('app/public/galeri-kamar/' . $fileName);

                    // Salin foto dari asset ke storage menggunakan copy manual
                    if (copy($sourcePath, $destinationPath)) {
                        // Buat record di database dengan path storage yang benar
                        GaleriKamar::create([
                            'tipe_kamar_id' => $tipeKamar->id,
                            'url_foto' => 'galeri-kamar/' . $fileName,
                        ]);
                    }
                }
            }
        }
    }
}
