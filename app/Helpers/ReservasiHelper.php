<?php

namespace App\Helpers;

use App\Models\Reservasi;

class ReservasiHelper
{
    /**
     * Generate kode reservasi otomatis
     * Format: RSV + YYYY + MM + 4 digit nomor urut
     * Contoh: RSV2025010001
     */
    public static function generateKodeReservasi(): string
    {
        $prefix = 'RSV';
        $year = date('Y');
        $month = date('m');

        // Ambil nomor urut terakhir untuk bulan ini
        $lastReservasi = Reservasi::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReservasi) {
            // Extract nomor urut dari kode terakhir
            $lastNumber = (int) substr($lastReservasi->kode_reservasi, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate kode reservasi dengan prefix custom
     */
    public static function generateKodeReservasiWithPrefix(string $prefix = 'RSV'): string
    {
        $year = date('Y');
        $month = date('m');

        // Ambil nomor urut terakhir untuk bulan ini
        $lastReservasi = Reservasi::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReservasi) {
            // Extract nomor urut dari kode terakhir
            $lastNumber = (int) substr($lastReservasi->kode_reservasi, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validasi format kode reservasi
     */
    public static function validateKodeReservasi(string $kode): bool
    {
        // Format: RSV + 4 digit tahun + 2 digit bulan + 4 digit nomor urut
        $pattern = '/^RSV\d{4}\d{2}\d{4}$/';
        return preg_match($pattern, $kode) === 1;
    }

    /**
     * Extract informasi dari kode reservasi
     */
    public static function parseKodeReservasi(string $kode): array
    {
        if (!self::validateKodeReservasi($kode)) {
            return [];
        }

        $year = substr($kode, 3, 4);
        $month = substr($kode, 7, 2);
        $number = (int) substr($kode, 9, 4);

        return [
            'year' => $year,
            'month' => $month,
            'number' => $number,
            'formatted' => [
                'year' => $year,
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'number' => $number
            ]
        ];
    }
}
