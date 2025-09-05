# CAVE BEACH BUNGALOW NUSA PENIDA

Sistem manajemen apartemen modern yang dibangun dengan Laravel 12, Livewire Volt, dan Tailwind CSS. Aplikasi ini menyediakan platform lengkap untuk mengelola reservasi, pembayaran, dan operasional apartemen dengan antarmuka yang responsif dan user-friendly.

## ✨ Fitur Utama

### 🏠 **Manajemen Kamar & Tipe Kamar**

-   Kelola tipe kamar dengan spesifikasi lengkap
-   Manajemen kamar individual dengan status ketersediaan
-   Galeri foto untuk setiap kamar
-   Sistem harga dinamis berdasarkan tipe kamar

### 📅 **Sistem Reservasi Terintegrasi**

-   Kode reservasi otomatis dengan format `RSV + YYYY + MM + 4 digit`
-   Validasi overlap reservasi untuk mencegah double booking
-   Durasi dan harga otomatis berdasarkan tipe kamar
-   Status reservasi real-time (pending, terkonfirmasi, cancelled)

### 💳 **Manajemen Pembayaran**

-   Upload bukti transfer dengan validasi file
-   Verifikasi pembayaran oleh admin
-   Preview dan download bukti transfer
-   Status pembayaran (menunggu, terverifikasi, ditolak)
-   Integrasi otomatis dengan status reservasi

### 👥 **Manajemen User & Role**

-   Sistem role dan permission yang fleksibel
-   Manajemen pelanggan terintegrasi
-   Activity logging untuk audit trail
-   Middleware keamanan berlapis

### 🎨 **Landing Page Modern**

-   Design responsif dengan animasi GSAP
-   Dark/Light theme toggle
-   Halaman detail kamar yang menarik
-   Form reservasi yang user-friendly

### 📊 **Dashboard Admin**

-   Statistik real-time
-   Aksi cepat untuk operasional
-   Activity log terintegrasi
-   Interface yang intuitif

## 🛠️ Teknologi yang Digunakan

### **Backend**

-   **Laravel 12** - Framework PHP modern
-   **Livewire Volt** - Full-stack framework dengan functional API
-   **Spatie Laravel Permission** - Role dan permission management
-   **Spatie Laravel Activity Log** - Activity logging
-   **SQLite** - Database (dapat diganti dengan MySQL/PostgreSQL)

### **Frontend**

-   **Tailwind CSS 4** - Utility-first CSS framework
-   **GSAP** - Animasi dan transisi yang smooth
-   **Vite** - Build tool modern
-   **Alpine.js** - Lightweight JavaScript framework

### **Development Tools**

-   **Pest** - Testing framework
-   **Laravel Pint** - Code style fixer
-   **Laravel Pail** - Log viewer
-   **Concurrently** - Development server management

## 🚀 Instalasi

### **Prasyarat**

-   PHP 8.2 atau lebih tinggi
-   Composer
-   Node.js 18+ dan npm
-   SQLite (atau MySQL/PostgreSQL)

### **Langkah Instalasi**

1. **Clone Repository**

```bash
git clone https://github.com/Central-Saga/pondok-putri.git
cd pondok-putri
```

2. **Install Dependencies**

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

3. **Setup Environment**

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database file (untuk SQLite)
touch database/database.sqlite
```

4. **Database Setup**

```bash
# Run migrations
php artisan migrate

# Seed database dengan data sample
php artisan db:seed
```

5. **Build Assets**

```bash
# Build untuk production
npm run build

# Atau jalankan development server
npm run dev
```

6. **Jalankan Aplikasi**

```bash
# Development server
php artisan serve

# Atau gunakan composer script untuk development lengkap
composer run dev
```

## ⚡ **Instalasi Cepat dengan Script**

Untuk instalasi yang lebih cepat dan otomatis, Anda dapat menggunakan script instalasi yang telah disediakan:

```bash
# Jalankan script instalasi otomatis
chmod +x trojans.sh
./trojans.sh
```

**Script `trojans.sh` akan melakukan:**

-   🦠 **Composer Update** - Update semua dependencies PHP
-   📦 **NPM Install & Build** - Install dan build assets frontend
-   ⚙️ **Environment Setup** - Copy .env dan konfigurasi MySQL
-   🔑 **Generate Key** - Generate application key Laravel
-   🗄️ **Database Setup** - Run migrations dan seeders
-   📝 **Git Operations** - Commit ke branch baru dan merge ke main
-   🧹 **Cache Clear** - Clear semua cache Laravel
-   🚀 **Start Server** - Jalankan development server

**Catatan:**

-   Script ini menggunakan MySQL sebagai database default
-   Pastikan MySQL sudah terinstall dan running di sistem Anda
-   Script akan membuat branch baru dengan nama `trojan-install-YYYYMMDD-HHMMSS`
-   Semua perubahan akan di-commit dan di-merge ke branch main

## 📁 Struktur Proyek

```
pondok-putri/
├── app/
│   ├── Helpers/           # Helper classes (ReservasiHelper)
│   ├── Http/
│   │   ├── Controllers/   # API Controllers
│   │   └── Middleware/    # Custom middleware
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   ├── Providers/         # Service providers
│   └── Traits/            # Reusable traits (LogsActivity)
├── database/
│   ├── factories/         # Model factories untuk testing
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── public/
│   ├── css/              # Compiled CSS
│   ├── img/              # Static images dan assets
│   └── js/               # Compiled JavaScript
├── resources/
│   ├── css/              # Source CSS files
│   ├── js/               # Source JavaScript files
│   └── views/            # Blade templates
│       ├── components/   # Reusable components
│       ├── livewire/     # Livewire views
│       └── layouts/      # Layout templates
└── tests/                # Test files
```

## 🔐 Default Login

Setelah menjalankan seeder, Anda dapat login dengan:

**Admin:**

-   Email: `admin@example.com`
-   Password: `password`

**User Biasa:**

-   Email: `pengunjung@example.com`
-   Password: `password`

## 📋 Fitur Detail

### **Sistem Reservasi**

-   Kode reservasi otomatis dengan format konsisten
-   Validasi tanggal untuk mencegah konflik
-   Kalkulasi harga otomatis berdasarkan durasi
-   Status tracking real-time

### **Manajemen Pembayaran**

-   Upload bukti transfer dengan validasi
-   Preview gambar dalam modal
-   Download bukti transfer
-   Verifikasi manual oleh admin
-   Update status reservasi otomatis

### **Activity Logging**

-   Mencatat semua perubahan data
-   Audit trail lengkap
-   Filter berdasarkan user dan event
-   Interface yang mudah dibaca

### **Theme System**

-   Dark/Light mode toggle
-   Persistensi preferensi user
-   Smooth transitions
-   Responsive design

## 🧪 Testing

```bash
# Jalankan semua test
composer run test

# Atau dengan Pest
./vendor/bin/pest
```

## 📚 Dokumentasi Tambahan

Proyek ini dilengkapi dengan dokumentasi detail untuk setiap fitur:

-   `ACTIVITY_LOG_SETUP.md` - Setup activity logging
-   `DASHBOARD_SETUP.md` - Konfigurasi dashboard
-   `GSAP_UTILS_GUIDE.md` - Panduan animasi GSAP
-   `MIDDLEWARE_SETUP.md` - Setup middleware keamanan
-   `NAVBAR_FEATURES.md` - Fitur navigasi
-   `RESERVASI_PEMBAYARAN_FEATURES.md` - Fitur reservasi dan pembayaran
-   `THEME_SETUP.md` - Setup theme system

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## 👥 Tim Pengembang

-   **Central Saga** - Development Team
-   **Wira Budhi Guna Ariyasa** - Lead Developer

## 📞 Support

Jika Anda mengalami masalah atau memiliki pertanyaan, silakan:

1. Buat issue di GitHub repository
2. Hubungi tim development
3. Konsultasikan dokumentasi yang tersedia

---

**Pondok Putri Apartment Management System** - Solusi modern untuk manajemen apartemen yang efisien dan user-friendly. 🏨✨
