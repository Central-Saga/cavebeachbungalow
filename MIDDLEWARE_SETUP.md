# Middleware Setup untuk Redirect Berdasarkan Role

## Overview

Sistem ini menggunakan middleware untuk mengarahkan user berdasarkan role mereka setelah login atau ketika mencoba mengakses halaman tertentu.

## Middleware yang Dibuat

### 1. RedirectBasedOnRole Middleware

**File:** `app/Http/Middleware/RedirectBasedOnRole.php`

Middleware ini memiliki beberapa mode operasi:

#### Mode `after_login`

-   Digunakan setelah user berhasil login
-   Mengarahkan user berdasarkan role:
    -   **Pengunjung** → Landing page (`/landingpage/home`)
    -   **Admin** → Dashboard admin (`/godmode/dashboard`)
    -   **Role lain** → Landing page (default)

#### Mode `dashboard_access`

-   Mencegah user dengan role "Pengunjung" mengakses dashboard
-   Redirect ke landing page dengan pesan error

#### Mode `admin_only`

-   Hanya admin yang bisa akses
-   User lain akan di-redirect dengan pesan error

## Registrasi Middleware

### Bootstrap App

**File:** `bootstrap/app.php`

```php
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \App\Http\Middleware\RoleOrPermissionMiddleware::class,
    'redirect.role' => \App\Http\Middleware\RedirectBasedOnRole::class, // Middleware baru
]);
```

## Penggunaan di Routes

### 1. Route Dashboard Admin

```php
Route::middleware(['auth', 'verified', 'redirect.role:dashboard_access'])
    ->prefix('godmode')
    ->name('admin.')
    ->group(function () {
        // Dashboard dan fitur admin
    });
```

### 2. Route Redirect Setelah Login

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/redirect-based-role', function () {
        // Logic redirect berdasarkan role
    })->name('redirect.role');
});
```

## Flow Kerja

### 1. User Register

1. User mengisi form register
2. Data user dan pelanggan dibuat
3. User otomatis dapat role "Pengunjung"
4. User otomatis login
5. Redirect ke `redirect.role` route

### 2. User Login

1. User mengisi form login
2. Setelah berhasil login
3. Redirect ke `redirect.role` route
4. Middleware akan mengarahkan berdasarkan role

### 3. Redirect Berdasarkan Role

1. Middleware `RedirectBasedOnRole` dijalankan
2. Cek role user:
    - **Pengunjung** → Landing page
    - **Admin** → Dashboard admin
3. User diarahkan ke halaman yang sesuai

### 4. Akses Dashboard

1. User dengan role "Pengunjung" mencoba akses dashboard
2. Middleware `redirect.role:dashboard_access` dijalankan
3. User di-redirect ke landing page dengan pesan error

## Role yang Tersedia

### Admin

-   Akses penuh ke dashboard
-   Mengelola semua fitur sistem
-   Bisa akses semua halaman admin

### Pengunjung

-   Hanya bisa akses landing page
-   Bisa melihat tipe kamar dan fasilitas
-   Bisa melakukan reservasi
-   **TIDAK BISA** akses dashboard admin

## Pesan Error

Sistem menggunakan session flash messages untuk menampilkan pesan:

-   **Success:** `session('success')`
-   **Error:** `session('error')`
-   **Warning:** `session('warning')`
-   **Info:** `session('info')`

## Komponen Alert

**File:** `resources/views/components/alert.blade.php`

Komponen ini otomatis menampilkan pesan session di:

-   Landing page (`resources/views/livewire/pages/landingpage/home/index.blade.php`)
-   Login page (`resources/views/livewire/auth/login.blade.php`)
-   Register page (`resources/views/livewire/auth/register.blade.php`)

## Styling yang Konsisten

### Warna Tema

-   **Primary:** `#133E87` (biru)
-   **Hover:** `#0f326e` (biru gelap)
-   **Focus Ring:** `#133E87` dengan opacity

### Button Styling

-   `rounded-xl` - Border radius
-   `hover:scale-105` - Hover effect
-   `transition-all duration-300` - Smooth transition
-   `focus:ring-4 focus:ring-blue-300/50` - Focus state

### Link Styling

-   `text-[#133E87]` - Warna teks
-   `hover:text-[#0f326e]` - Hover color
-   `underline decoration-[#133E87]/30` - Underline dengan opacity

## Testing

### Test Role Pengunjung

1. Register user baru (otomatis dapat role "Pengunjung")
2. Login dengan user tersebut
3. Pastikan diarahkan ke landing page, bukan dashboard

### Test Role Admin

1. Login dengan user yang memiliki role "Admin"
2. Pastikan bisa akses dashboard admin

### Test Akses Terbatas

1. Login dengan user "Pengunjung"
2. Coba akses `/godmode/dashboard`
3. Pastikan di-redirect ke landing page dengan pesan error

## Troubleshooting

### Middleware Tidak Berfungsi

1. Pastikan sudah di-register di `bootstrap/app.php`
2. Clear cache: `php artisan config:clear`
3. Restart server

### Role Tidak Dikenali

1. Pastikan seeder sudah dijalankan: `php artisan db:seed --class=RoleAndPermissionSeeder`
2. Cek database table `roles` dan `model_has_roles`

### Redirect Loop

1. Pastikan route `redirect.role` tidak masuk ke dalam group middleware yang sama
2. Cek apakah ada redirect yang saling berulang

### Komponen Alert Tidak Muncul

1. Pastikan file `resources/views/components/alert.blade.php` ada
2. Pastikan komponen `<x-alert />` sudah ditambahkan ke halaman
3. Cek apakah ada error di console browser

## File yang Telah Dimodifikasi

### 1. Login (`resources/views/livewire/auth/login.blade.php`)

-   Redirect ke `redirect.role` setelah login
-   Styling button dan link sesuai tema
-   Komponen alert untuk pesan

### 2. Register (`resources/views/livewire/auth/register.blade.php`)

-   Layout 2 kolom untuk form yang compact
-   Otomatis assign role "Pengunjung"
-   Redirect ke `redirect.role` setelah register
-   Styling konsisten dengan tema

### 3. Landing Page (`resources/views/livewire/pages/landingpage/home/index.blade.php`)

-   Komponen alert untuk menampilkan pesan error/success

### 4. Middleware (`app/Http/Middleware/RedirectBasedOnRole.php`)

-   Logic redirect berdasarkan role
-   Multiple mode operasi

### 5. Routes (`routes/web.php`)

-   Route `redirect.role` untuk redirect setelah login
-   Middleware protection untuk dashboard admin

### 6. Bootstrap (`bootstrap/app.php`)

-   Registrasi middleware `redirect.role`

### 7. Components (`resources/views/components/alert.blade.php`)

-   Komponen alert untuk semua jenis pesan

## Keuntungan Sistem Ini

✅ **Keamanan** - User "Pengunjung" tidak bisa akses dashboard admin  
✅ **UX yang Baik** - User langsung diarahkan ke halaman yang sesuai  
✅ **Fleksibel** - Mudah ditambahkan role baru  
✅ **Maintainable** - Kode terorganisir dan terdokumentasi dengan baik  
✅ **Konsisten** - Styling yang seragam di semua halaman  
✅ **User-Friendly** - Pesan error yang jelas dan informatif
