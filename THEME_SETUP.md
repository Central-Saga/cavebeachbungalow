# Setup Tema - Pondok Putri Hotel

## Masalah yang Diperbaiki

Sebelumnya, tema aplikasi selalu mengikuti preferensi browser (`prefers-color-scheme`) meskipun user sudah memilih tema eksplisit (light/dark). Sekarang sistem tema telah diperbaiki untuk:

1. **Menghormati pilihan user** - Ketika user memilih tema light/dark, aplikasi tidak akan mengikuti preferensi browser
2. **Sistem theme yang konsisten** - Tema diterapkan di semua halaman dengan benar
3. **Debugging yang mudah** - Tools untuk troubleshooting masalah tema

## File yang Ditambahkan/Diperbaiki

### 1. `public/js/theme-initializer.js`

-   Script utama untuk menginisialisasi tema
-   Memastikan tema user tidak di-override oleh browser
-   Berjalan sebelum script lain

### 2. `public/js/theme-manager.js` (diperbaiki)

-   Manager tema yang lebih robust
-   Tidak mengubah tema ketika user sudah memilih eksplisit
-   Hanya mengikuti browser preference ketika user memilih "system"

### 3. `public/css/theme-overrides.css`

-   CSS untuk memaksa tema yang dipilih user
-   Override browser default color-scheme
-   Memastikan konsistensi visual

### 4. `public/js/theme-debug.js`

-   Tools debugging untuk tema
-   Log informasi tema ke console
-   Fungsi untuk force apply tema

### 5. `resources/views/livewire/settings/appearance.blade.php` (diperbaiki)

-   Logika tema yang lebih baik
-   Fungsi `applyThemeDirectly()` yang memaksa tema user

## Cara Kerja

1. **Inisialisasi**: `theme-initializer.js` berjalan pertama dan mengatur tema berdasarkan localStorage
2. **User Choice**: Ketika user memilih tema, preference disimpan di localStorage
3. **Override Browser**: CSS dan JavaScript memastikan tema user tidak di-override oleh browser
4. **Konsistensi**: Tema diterapkan di semua halaman melalui event system

## Cara Debug

### 1. Buka Console Browser

```javascript
// Lihat informasi tema
window.themeDebug.logInfo();

// Force apply tema tertentu
window.themeDebug.forceTheme("light");
window.themeDebug.forceTheme("dark");

// Reset ke system theme
window.themeDebug.resetSystem();
```

### 2. Periksa localStorage

```javascript
// Lihat tema yang tersimpan
localStorage.getItem("theme");

// Hapus tema (akan kembali ke system)
localStorage.removeItem("theme");
```

### 3. Periksa HTML Classes

```javascript
// Lihat class yang diterapkan
document.documentElement.classList.toString();

// Lihat data-theme attribute
document.documentElement.getAttribute("data-theme");
```

## Troubleshooting

### Tema tidak berubah

1. Periksa console untuk error
2. Jalankan `window.themeDebug.logInfo()`
3. Pastikan localStorage tidak kosong
4. Coba force apply tema dengan `window.themeDebug.forceTheme('light')`

### Tema kembali ke browser preference

1. Pastikan localStorage menyimpan tema yang benar
2. Periksa apakah ada script lain yang mengubah tema
3. Gunakan CSS overrides untuk memaksa tema

### Tema tidak konsisten antar halaman

1. Pastikan semua halaman memuat script tema
2. Periksa event listener untuk storage changes
3. Gunakan `window.themeDebug.logInfo()` di setiap halaman

## Testing

1. **Pilih tema light** - Pastikan tetap light meskipun browser dark
2. **Pilih tema dark** - Pastikan tetap dark meskipun browser light
3. **Pilih system** - Pastikan mengikuti browser preference
4. **Refresh halaman** - Pastikan tema tetap sama
5. **Buka tab baru** - Pastikan tema konsisten

## Catatan Penting

-   Script tema harus dimuat di **SEMUA** halaman yang menggunakan tema
-   Urutan script penting: `theme-initializer.js` → `theme-manager.js` → `theme-debug.js`
-   CSS overrides menggunakan `!important` untuk memastikan tema user tidak di-override
-   Event system memastikan tema konsisten antar komponen

## Struktur File

```
public/
├── js/
│   ├── theme-initializer.js    # Script utama tema
│   ├── theme-manager.js        # Manager tema
│   └── theme-debug.js          # Tools debugging
└── css/
    └── theme-overrides.css     # CSS overrides

resources/views/
├── components/layouts/admin/sidebar.blade.php  # Layout utama
├── partials/head.blade.php                     # Head section
└── livewire/settings/appearance.blade.php      # Settings tema
```
