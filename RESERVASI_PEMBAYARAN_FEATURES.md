# Fitur Reservasi dan Pembayaran - Pondok Putri Apartment

## Overview

Sistem reservasi dan pembayaran yang terintegrasi untuk Pondok Putri Apartment menggunakan Laravel Livewire Volt dengan functional API style.

## Fitur yang Telah Dibuat

### 1. Halaman Reservasi Saya (`/landingpage/reservasi-saya`)

-   **Lokasi**: `resources/views/livewire/pages/landingpage/reservasi-saya.blade.php`
-   **Fitur**:
    -   Daftar semua reservasi user yang sudah login
    -   Status reservasi (pending, terkonfirmasi, cancelled)
    -   Status pembayaran (lunas, belum lunas)
    -   Riwayat pembayaran dengan status (menunggu, terverifikasi, ditolak)
    -   Upload bukti pembayaran dengan modal
    -   Link ke pembuatan reservasi baru

### 2. Halaman Verifikasi Pembayaran Admin (`/godmode/verifikasi-pembayaran`)

-   **Lokasi**: `resources/views/livewire/pages/admin/verifikasi-pembayaran.blade.php`
-   **Fitur**:
    -   Dashboard statistik pembayaran
    -   Filter berdasarkan status (semua, menunggu, terverifikasi, ditolak)
    -   Tabel daftar pembayaran dengan detail lengkap
    -   Modal verifikasi pembayaran
    -   Preview bukti transfer
    -   Hapus pembayaran
    -   Update status reservasi otomatis jika lunas

### 3. Integrasi dengan Halaman Lain

-   **Navbar**: Link "Reservasi Saya" di dropdown user menu
-   **Halaman Reservasi**: Link ke "Reservasi Saya" di breadcrumb dan hero section
-   **Halaman Beranda**: Link "Lihat Reservasi Saya" untuk user yang sudah login

## Struktur File

### File yang Dihapus

-   `app/Livewire/Actions/UploadBuktiPembayaran.php` (diganti dengan Volt functional API)
-   `app/Livewire/Actions/VerifikasiPembayaran.php` (diganti dengan Volt functional API)

### File yang Dibuat

-   `resources/views/livewire/pages/landingpage/reservasi-saya.blade.php` - Halaman reservasi user
-   `resources/views/livewire/pages/admin/verifikasi-pembayaran.blade.php` - Halaman admin verifikasi

### File yang Dimodifikasi

-   `routes/web.php` - Route baru untuk fitur reservasi dan verifikasi
-   `resources/views/components/layouts/landing/navbar.blade.php` - Menu navigasi
-   `resources/views/components/layouts/admin/sidebar.blade.php` - Sidebar admin
-   `resources/views/livewire/pages/landingpage/home/index.blade.php` - Link di beranda
-   `resources/views/livewire/pages/landingpage/reservasi/index.blade.php` - Link di halaman reservasi

## Routes

### Landing Page Routes

```php
Volt::route('landingpage/reservasi-saya', 'pages.reservasi-saya')->name('landingpage.reservasi-saya');
```

### Admin Routes

```php
Volt::route('/verifikasi-pembayaran', 'pages.admin.verifikasi-pembayaran')->name('admin.verifikasi-pembayaran');
```

## Fitur Utama

### Upload Bukti Pembayaran

-   Validasi file (image, max 2MB)
-   Validasi nominal (min Rp 1.000)
-   Upload ke storage public
-   Status otomatis "menunggu"
-   Refresh data real-time

### Verifikasi Pembayaran Admin

-   Filter berdasarkan status
-   Preview bukti transfer
-   Update status pembayaran
-   Update status reservasi otomatis
-   Statistik real-time

### Status Reservasi

-   **Pending**: Menunggu pembayaran
-   **Terkonfirmasi**: Sudah lunas dan diverifikasi
-   **Cancelled**: Dibatalkan

### Status Pembayaran

-   **Menunggu**: Belum diverifikasi admin
-   **Terverifikasi**: Diverifikasi admin
-   **Ditolak**: Ditolak admin

## Keamanan

-   Middleware auth untuk halaman user
-   Middleware role untuk halaman admin
-   Validasi file upload
-   Sanitasi input
-   CSRF protection

## Responsivitas

-   Mobile-first design
-   Responsive grid layout
-   Touch-friendly interface
-   Optimized untuk berbagai ukuran layar

## Integrasi

-   Menggunakan model `Reservasi`, `Pembayaran`, `Pelanggan`
-   Relasi dengan `Kamar`, `TipeKamar`, `User`
-   Storage untuk file bukti pembayaran
-   Session flash messages
-   Real-time updates dengan Livewire

## Cara Penggunaan

### Untuk User

1. Login ke sistem
2. Akses "Reservasi Saya" dari navbar atau beranda
3. Lihat daftar reservasi
4. Upload bukti pembayaran jika ada sisa bayar
5. Pantau status pembayaran

### Untuk Admin

1. Login sebagai admin
2. Akses "Verifikasi Pembayaran" dari sidebar
3. Lihat daftar pembayaran yang menunggu
4. Verifikasi atau tolak pembayaran
5. Pantau statistik pembayaran

## Dependencies

-   Laravel 11
-   Livewire 3
-   Volt (Livewire functional API)
-   Tailwind CSS
-   Font Awesome Icons
-   Alpine.js (via Flux)

## Catatan Teknis

-   Menggunakan Volt functional API style (bukan class-based)
-   State management dengan `state()` function
-   Event handling dengan `$function` syntax
-   Real-time validation
-   Optimized database queries dengan eager loading
-   File storage menggunakan Laravel Storage facade
