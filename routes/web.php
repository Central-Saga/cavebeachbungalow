<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Test theme route
Route::get('/test-theme', function () {
    return view('test-theme');
})->name('test-theme');

// Test sidebar theme route
Route::get('/test-sidebar', function () {
    return view('test-sidebar');
})->name('test-sidebar');

Volt::route('/', 'pages.landingpage.home.index')->name('home');
Volt::route('aboutme', 'pages.landingpage.aboutme.index')->name('aboutme');
Volt::route('landingkamar', 'pages.landingpage.kamar.index')->name('kamar');
Volt::route('landingkamar/detail/{id}', 'pages.landingpage.kamar.detail')->name('kamar.detail');

// Landing page routes
Volt::route('landingpage/home', 'pages.landingpage.home.index')->name('landingpage.home');
Volt::route('landingpage/aboutme', 'pages.landingpage.aboutme.index')->name('landingpage.aboutme');
Volt::route('landingpage/gallery', 'pages.landingpage.gallery.index')->name('landingpage.gallery');
Volt::route('landingpage/kamar', 'pages.landingpage.kamar.index')->name('landingpage.kamar');
Volt::route('landingpage/kamar/detail/{id}', 'pages.landingpage.kamar.detail')->name('landingpage.kamar.detail');
Volt::route('landingpage/tipe-kamar', 'pages.landingpage.tipe-kamar.index')->name('landingpage.tipe-kamar');
Volt::route('landingpage/tipe-kamar/{id}', 'pages.landingpage.tipe-kamar.detail')->name('landingpage.tipe-kamar.detail');
Volt::route('landingpage/contact', 'pages.landingpage.contact.index')->name('landingpage.contact');
Volt::route('landingpage/reservasi', 'pages.landingpage.reservasi.index')->name('landingpage.reservasi');
Volt::route('landingpage/reservasi-saya', 'pages.landingpage.reservasi-saya')->name('landingpage.reservasi-saya');
Volt::route('landingpage/reservasi/detail/{id}', 'pages.landingpage.reservasi.detail')->name('landingpage.reservasi.detail');

Route::middleware(['auth', 'verified', 'redirect.role:dashboard_access'])->prefix('godmode')->name('admin.')->group(function () {
    // Dashboard
    Volt::route('/dashboard', 'pages.dashboard.index')->name('dashboard');

    // Settings - Semua role bisa akses
    Volt::route('/settings', 'settings.index')->name('settings.index');
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/password', 'settings.password')->name('settings.password');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Role Management - Admin only
    Route::middleware('role:Admin')->group(function () {
        Volt::route('/roles', 'pages.roles.index')->name('roles.index');
        Volt::route('/roles/create', 'pages.roles.create')->name('roles.create');
        Volt::route('/roles/{role}/edit', 'pages.roles.edit')->name('roles.edit');

        // User Management - Admin only
        Volt::route('/users', 'pages.users.index')->name('users.index');
        Volt::route('/users/create', 'pages.users.create')->name('users.create');
        Volt::route('/users/{user}/edit', 'pages.users.edit')->name('users.edit');

        // Pelanggan Management - Admin only
        Volt::route('/pelanggan', 'pages.pelanggan.index')->name('pelanggan.index');
        Volt::route('/pelanggan/create', 'pages.pelanggan.create')->name('pelanggan.create');
        Volt::route('/pelanggan/{pelanggan}/edit', 'pages.pelanggan.edit')->name('pelanggan.edit');

        // Fasilitas Kamar Management - Admin only
        Volt::route('/fasilitas-kamar', 'pages.fasilitas-kamar.index')->name('fasilitas-kamar.index');
        Volt::route('/fasilitas-kamar/create', 'pages.fasilitas-kamar.create')->name('fasilitas-kamar.create');
        Volt::route('/fasilitas-kamar/{fasilitasKamar}/edit', 'pages.fasilitas-kamar.edit')->name('fasilitas-kamar.edit');

        // Tipe Kamar Management - Admin only
        Volt::route('/tipe-kamar', 'pages.tipe-kamar.index')->name('tipe-kamar.index');
        Volt::route('/tipe-kamar/create', 'pages.tipe-kamar.create')->name('tipe-kamar.create');
        Volt::route('/tipe-kamar/{tipeKamar}/edit', 'pages.tipe-kamar.edit')->name('tipe-kamar.edit');

        // Galeri Kamar Management - Admin only
        Route::delete('/galeri-kamar/{photo}', [App\Http\Controllers\TipeKamarController::class, 'deleteGaleriPhoto'])
            ->name('galeri-kamar.delete');

        // Kamar Management - Admin only
        Volt::route('/kamar', 'pages.kamar.index')->name('kamar.index');
        Volt::route('/kamar/create', 'pages.kamar.create')->name('kamar.create');
        Volt::route('/kamar/{kamar}/edit', 'pages.kamar.edit')->name('kamar.edit');

        // Reservasi Management - Admin only
        Volt::route('/reservasi', 'pages.reservasi.index')->name('reservasi.index');
        Volt::route('/reservasi/create', 'pages.reservasi.create')->name('reservasi.create');
        Volt::route('/reservasi/{reservasi}/edit', 'pages.reservasi.edit')->name('reservasi.edit');

        // Pembayaran Management - Admin only
        Volt::route('/verifikasi-pembayaran', 'pages.admin.verifikasi-pembayaran')->name('verifikasi-pembayaran');
    });
});

// Pelanggan Management - All authenticated users (temporary for testing)

// Route untuk redirect berdasarkan role setelah login
Route::middleware(['auth'])->group(function () {
    Route::get('/redirect-based-role', function () {
        $user = auth()->user();

        if ($user->hasRole('Pengunjung')) {
            return redirect()->route('landingpage.home');
        }

        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Default redirect untuk role lain
        return redirect()->route('landingpage.home');
    })->name('redirect.role');

    // Route untuk redirect langsung berdasarkan role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('Pengunjung')) {
            return redirect()->route('landingpage.home');
        }

        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('landingpage.home');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
