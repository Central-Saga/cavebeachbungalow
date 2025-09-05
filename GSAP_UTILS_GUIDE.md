# Panduan Penggunaan GSAP Utilities

## Overview
GSAP utilities telah dipindahkan dari navbar ke layout global untuk memungkinkan penggunaan di seluruh aplikasi. File utama berada di `resources/js/gsap-utils.js`.

## Struktur File

### 1. Layout Landing (`resources/views/components/layouts/landing.blade.php`)
- **GSAP CDN**: Dimuat di `<head>` section
- **Vite Assets**: Include file `gsap-utils.js` melalui Vite

### 2. GSAP Utilities (`resources/js/gsap-utils.js`)
- **Class GSAPUtils**: Berisi semua utility functions
- **Global Instance**: `window.gsapUtils` dan `window.GSAPUtils` (backward compatibility)

### 3. Navbar (`resources/views/components/layouts/landing/navbar.blade.php`)
- **Tidak ada GSAP CDN**: Sudah dipindah ke layout
- **Menggunakan Global Utilities**: `window.GSAPUtils.animateNavbar()`, dll.

## Cara Penggunaan

### 1. Animasi Dasar

#### Fade In
```html
<div class="animate-fade-in">Content yang akan fade in</div>
```
```javascript
// Manual trigger
window.gsapUtils.fadeIn(element, 0.5, 0);
```

#### Slide In
```html
<div class="animate-slide-left">Content yang akan slide dari kiri</div>
<div class="animate-slide-right">Content yang akan slide dari kanan</div>
```
```javascript
// Manual trigger
window.gsapUtils.slideInLeft(element, 0.6, 0);
window.gsapUtils.slideInRight(element, 0.6, 0);
```

#### Scale In
```html
<div class="animate-scale-in">Content yang akan scale in</div>
```
```javascript
// Manual trigger
window.gsapUtils.scaleIn(element, 0.5, 0);
```

### 2. Animasi Stagger (Multiple Elements)

```html
<div class="card animate-fade-in">Card 1</div>
<div class="card animate-fade-in">Card 2</div>
<div class="card animate-fade-in">Card 3</div>
```

```javascript
// Otomatis via CSS class
// Atau manual trigger
const cards = document.querySelectorAll('.card');
window.gsapUtils.staggerAnimation(cards, 'fadeIn', 0.2);
```

### 3. Text Reveal

```html
<h1>Judul yang akan di-reveal per karakter</h1>
```

```javascript
// Otomatis untuk semua heading
// Atau manual trigger
const heading = document.querySelector('h1');
window.gsapUtils.textReveal(heading, 1, 0);
```

### 4. Parallax Effect

```html
<div class="parallax">Background dengan efek parallax</div>
```

```javascript
// Otomatis via CSS class
// Atau manual trigger
const parallaxElement = document.querySelector('.parallax');
window.gsapUtils.parallax(parallaxElement, 0.3);
```

### 5. Smooth Scrolling

```html
<a href="#section" onclick="scrollToSection('#section')">Scroll ke Section</a>
```

```javascript
function scrollToSection(targetId) {
    window.gsapUtils.smoothScrollTo(targetId, 100);
}
```

## CSS Classes yang Tersedia

Tambahkan class berikut ke elemen HTML untuk animasi otomatis:

- `animate-fade-in`: Fade in animation
- `animate-slide-left`: Slide in dari kiri
- `animate-slide-right`: Slide in dari kanan  
- `animate-scale-in`: Scale in animation
- `parallax`: Parallax effect

## Contoh Implementasi di Komponen

### Hero Section
```html
<section id="hero" class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="animate-fade-in">Selamat Datang di Pondok Putri</h1>
        <p class="animate-fade-in">Tempat istirahat terbaik untuk Anda</p>
        <button class="btn animate-scale-in">Mulai Sekarang</button>
    </div>
</section>
```

### Feature Cards
```html
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card animate-fade-in">
        <h3>Fasilitas Lengkap</h3>
        <p>Semua kebutuhan tersedia</p>
    </div>
    <div class="card animate-fade-in">
        <h3>Lokasi Strategis</h3>
        <p>Mudah diakses</p>
    </div>
    <div class="card animate-fade-in">
        <h3>Harga Terjangkau</h3>
        <p>Kualitas terbaik</p>
    </div>
</div>
```

## Troubleshooting

### 1. GSAP Not Loaded
```javascript
if (!window.gsap) {
    console.error('GSAP not loaded');
    return;
}
```

### 2. GSAP Utils Not Available
```javascript
if (!window.gsapUtils) {
    console.error('GSAP Utils not available');
    return;
}
```

### 3. Element Not Found
```javascript
const element = document.querySelector('.target');
if (!element) {
    console.error('Target element not found');
    return;
}
window.gsapUtils.fadeIn(element);
```

## Best Practices

1. **Gunakan CSS Classes**: Untuk animasi sederhana, gunakan CSS classes yang sudah tersedia
2. **Manual Trigger**: Untuk animasi kompleks, gunakan manual trigger dengan JavaScript
3. **Performance**: Gunakan `throttleScroll` untuk scroll events
4. **Error Handling**: Selalu cek ketersediaan GSAP dan target elements
5. **Mobile First**: Pertimbangkan performa di perangkat mobile

## Dependencies

- **GSAP 3.12.2**: CDN dari Cloudflare
- **Vite**: Build tool untuk asset management
- **Laravel**: Framework backend

## File Locations

- `resources/js/gsap-utils.js` - Main utilities file
- `resources/views/components/layouts/landing.blade.php` - Layout dengan GSAP CDN
- `resources/views/components/layouts/landing/navbar.blade.php` - Navbar yang menggunakan utilities
- `vite.config.js` - Konfigurasi Vite untuk include GSAP utilities
