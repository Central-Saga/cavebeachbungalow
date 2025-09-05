<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if they don't exist
        $permissions = [
            'mengelola user',
            'mengelola role',
            'mengelola pelanggan',
            'mengelola reservasi',
            'mengelola pembayaran',
            'mengelola tipe dan fasilitas kamar',
            'mengelola kamar',
            'mengelola ketersediaan kamar',
            'mengelola galeri kamar',
            'mengelola harga sewa',
            'melihat tipe dan fasilitas kamar',
            'melihat ketersediaan kamar',
            'melihat galeri kamar',
            'melihat harga sewa',
            'melakukan reservasi',
            'mencetak laporan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'mengelola user',
            'mengelola role',
            'mengelola pelanggan',
            'mengelola reservasi',
            'mengelola pembayaran',
            'mengelola kamar',
            'mengelola tipe dan fasilitas kamar',
            'mengelola ketersediaan kamar',
            'mengelola galeri kamar',
            'mengelola harga sewa',
            'mencetak laporan',
        ]);

        // Create role for Owner (same permissions as Admin)
        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $owner->syncPermissions([
            'mengelola user',
            'mengelola role',
            'mengelola pelanggan',
            'mengelola kamar',
            'mengelola reservasi',
            'mengelola pembayaran',
            'mengelola tipe dan fasilitas kamar',
            'mengelola ketersediaan kamar',
            'mengelola galeri kamar',
            'mengelola harga sewa',
            'mencetak laporan',
        ]);

        // Create role for Pengunjung
        $pengunjung = Role::firstOrCreate(['name' => 'Pengunjung']);
        $pengunjung->syncPermissions([
            'melihat tipe dan fasilitas kamar',
            'melihat ketersediaan kamar',
            'melihat galeri kamar',
            'melihat harga sewa',
            'melakukan reservasi',
        ]);
    }
}
