# Troubleshooting Tema Dark/Light

## Masalah yang Sering Terjadi

### 1. Tema Memaksa Kembali ke Dark Mode

**Gejala:**

-   User memilih tema light di appearance settings
-   Tema berhasil berubah ke light
-   Setelah beberapa saat atau refresh, tema kembali ke dark mode
-   Browser dalam mode dark tapi user ingin light mode

**Penyebab:**

-   Konflik antara Alpine.js, Theme Manager, dan Theme Initializer
-   System preference (browser dark mode) menimpa pilihan user
-   Timing issue saat inisialisasi tema
-   CSS Tailwind dark mode classes yang memaksa tema

**Solusi yang Diterapkan:**

#### A. Perbaikan JavaScript Files

1. **Theme Manager (`public/js/theme-manager.js`)**

    - Delay inisialisasi lebih lama (200ms) untuk menunggu Alpine.js
    - Logging yang lebih jelas untuk debugging
    - Interval check yang lebih jarang (10 detik)

2. **Theme Initializer (`public/js/theme-initializer.js`)**

    - Delay inisialisasi lebih lama (300ms)
    - Logging yang lebih jelas
    - Penanganan event yang lebih baik

3. **Appearance Component (`resources/views/livewire/settings/appearance.blade.php`)**
    - Logging yang lebih jelas
    - Penanganan system preference yang lebih baik

#### B. CSS Overrides (`public/css/theme-overrides.css`)

```css
/* Force light theme when user explicitly chooses it */
html[data-theme="light"] {
    color-scheme: light !important;
}

/* Override any forced dark mode from browser/system */
html[data-theme="light"] * {
    color-scheme: light !important;
}

/* Force remove any system dark mode classes when user chooses light */
html[data-theme="light"].dark {
    background-color: #ffffff !important;
    color: #111827 !important;
}
```

#### C. Theme Enforcer (`public/js/theme-enforcer.js`)

File JavaScript khusus yang:

-   Memantau perubahan tema secara real-time
-   Mencegah pemaksaan tema yang tidak sesuai
-   Menggunakan MutationObserver untuk mendeteksi perubahan
-   Memperbaiki tema secara otomatis setiap 2 detik

## Cara Kerja Solusi

### 1. Hierarki Prioritas Tema

1. **User Choice** (light/dark) - Prioritas tertinggi
2. **System Preference** (hanya jika user memilih 'system')
3. **Default** (light)

### 2. Flow Inisialisasi

```
DOM Ready → Alpine.js (100ms) → Theme Initializer (300ms) → Theme Manager (200ms) → Theme Enforcer
```

### 3. Monitoring dan Enforcement

-   **MutationObserver**: Memantau perubahan class pada documentElement
-   **Storage Event**: Memantau perubahan localStorage
-   **System Preference**: Memantau perubahan preferensi browser
-   **Periodic Check**: Memeriksa tema setiap 2 detik

## Testing

### 1. Test Case: User Memilih Light Mode

1. Buka appearance settings
2. Pilih tema light
3. Verifikasi tema berubah ke light
4. Refresh halaman
5. Verifikasi tema tetap light
6. Ubah browser ke dark mode
7. Verifikasi tema tetap light

### 2. Test Case: User Memilih System Mode

1. Buka appearance settings
2. Pilih tema system
3. Verifikasi tema mengikuti browser preference
4. Ubah browser preference
5. Verifikasi tema berubah sesuai preference

### 3. Test Case: Cross-tab Synchronization

1. Buka 2 tab dengan aplikasi
2. Ubah tema di tab 1
3. Verifikasi tema berubah di tab 2

## Debugging

### 1. Console Logs

Semua perubahan tema akan di-log dengan prefix yang jelas:

-   `Appearance: ...`
-   `Theme Manager: ...`
-   `Theme Initializer: ...`
-   `Theme Enforcer: ...`

### 2. Manual Testing

```javascript
// Cek tema saat ini
console.log("Current theme:", localStorage.getItem("theme"));

// Paksa tema
window.themeEnforcer.enforceTheme("light");

// Cek tema yang dipaksa
console.log("Enforced theme:", window.themeEnforcer.getEnforcedTheme());
```

### 3. CSS Debugging

```css
/* Tambahkan border untuk debugging */
html[data-theme="light"] {
    border: 3px solid red !important;
}

html[data-theme="dark"] {
    border: 3px solid blue !important;
}
```

## Maintenance

### 1. Regular Checks

-   Monitor console logs untuk error
-   Test tema switching secara berkala
-   Verifikasi cross-browser compatibility

### 2. Updates

-   Update Tailwind CSS jika ada perubahan
-   Review dan update CSS overrides
-   Test dengan browser baru

### 3. Performance

-   Monitor interval timers
-   Optimize MutationObserver jika diperlukan
-   Reduce unnecessary DOM manipulations

## Kesimpulan

Solusi ini mengatasi masalah tema yang memaksa kembali ke dark mode dengan:

1. **Preventing Conflicts**: Menghindari konflik antara multiple JavaScript files
2. **Enforcing User Choice**: Memastikan pilihan user tidak ditimpa sistem
3. **Real-time Monitoring**: Memantau dan memperbaiki tema secara otomatis
4. **CSS Overrides**: Menggunakan CSS untuk memaksa tema yang dipilih
5. **Clear Logging**: Memudahkan debugging dan maintenance

Dengan implementasi ini, tema yang dipilih user akan tetap konsisten dan tidak akan dipaksa kembali oleh sistem atau browser preference.
