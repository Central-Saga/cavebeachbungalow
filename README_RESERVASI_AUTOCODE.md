# Fitur Kode Reservasi Otomatis - Cave Beach Bungalow

## Deskripsi

Sistem kode reservasi otomatis yang menghasilkan kode unik untuk setiap reservasi baru dengan format yang konsisten dan mudah dibaca. Dilengkapi dengan fitur durasi otomatis, harga otomatis, dan validasi overlap reservasi.

## Format Kode

Format kode reservasi: `RSV + YYYY + MM + 4 digit nomor urut`

**Contoh:**

-   `RSV2025010001` - Reservasi pertama di Januari 2025
-   `RSV2025010002` - Reservasi kedua di Januari 2025
-   `RSV2025020001` - Reservasi pertama di Februari 2025

## Fitur Utama

### 1. Kode Reservasi Otomatis

-   Generate otomatis dengan format RSV + YYYY + MM + 4 digit nomor urut
-   Reset nomor urut setiap bulan
-   Input field readonly untuk konsistensi

### 2. Durasi dan Tanggal Otomatis

-   Field durasi (1-30 hari) untuk input manual
-   Tanggal checkout dihitung otomatis berdasarkan durasi
-   Input tanggal checkout readonly

### 3. Harga Otomatis

-   Harga per malam diambil dari tipe kamar
-   Total harga = harga per malam × durasi
-   Input total harga readonly
-   Informasi harga per malam dan durasi ditampilkan

### 4. Validasi Overlap Reservasi

-   Mencegah double booking pada kamar yang sama
-   Validasi tanggal check-in dan check-out
-   Exclude reservasi yang dibatalkan (cancelled)
-   Pesan error yang informatif

## Komponen yang Diimplementasikan

### 1. Helper Class (`app/Helpers/ReservasiHelper.php`)

Class utility yang berisi fungsi-fungsi untuk mengelola kode reservasi:

-   `generateKodeReservasi()` - Generate kode otomatis dengan prefix default 'RSV'
-   `generateKodeReservasiWithPrefix($prefix)` - Generate kode dengan prefix custom
-   `validateKodeReservasi($kode)` - Validasi format kode reservasi
-   `parseKodeReservasi($kode)` - Extract informasi dari kode reservasi

### 2. Halaman Create (`resources/views/livewire/pages/reservasi/create.blade.php`)

-   Kode reservasi otomatis di-generate saat halaman dibuka
-   Input field readonly dengan informasi format kode
-   Field durasi dengan validasi 1-30 hari
-   Tanggal checkout otomatis berdasarkan durasi
-   Harga otomatis berdasarkan tipe kamar
-   Validasi overlap reservasi real-time
-   Menggunakan `ReservasiHelper::generateKodeReservasi()`

### 3. Halaman Edit (`resources/views/livewire/pages/reservasi/edit.blade.php`)

-   Kode reservasi readonly untuk menjaga konsistensi data
-   Durasi dihitung otomatis dari tanggal yang ada
-   Tanggal checkout dapat diubah dengan mengubah durasi
-   Harga otomatis berdasarkan tipe kamar
-   Validasi overlap reservasi (exclude current reservasi)
-   Tidak dapat diubah setelah dibuat

## Cara Kerja

### Algoritma Generate Kode

1. **Prefix**: Menggunakan 'RSV' sebagai prefix tetap
2. **Tahun**: Mengambil tahun saat ini (YYYY)
3. **Bulan**: Mengambil bulan saat ini (MM)
4. **Nomor Urut**:
    - Mencari reservasi terakhir di bulan yang sama
    - Extract nomor urut dari kode terakhir
    - Increment nomor urut + 1
    - Format dengan padding 4 digit (0001, 0002, dst)

### Algoritma Durasi dan Tanggal

1. **Input Durasi**: User input jumlah hari (1-30)
2. **Tanggal Check-in**: User pilih tanggal check-in
3. **Tanggal Check-out**: Otomatis = check-in + durasi
4. **Event Handler**: `wire:change="hitungTanggalCheckout"`

### Algoritma Harga Otomatis

1. **Pilih Kamar**: User pilih kamar dari dropdown
2. **Ambil Harga**: Ambil harga dari `tipeKamar.harga`
3. **Hitung Total**: Total = harga per malam × durasi
4. **Event Handler**: `wire:change="hitungTotalHarga"`

### Algoritma Validasi Overlap

1. **Cek Kamar**: Ambil kamar yang dipilih
2. **Cek Tanggal**: Validasi range tanggal check-in/out
3. **Cek Reservasi**: Cari reservasi yang overlap
4. **Exclude Status**: Abaikan reservasi cancelled
5. **Return Error**: Jika overlap, tampilkan pesan error

## Fitur Tambahan

### Input Field

-   **Create**: Input readonly dengan kode otomatis dan informasi format
-   **Edit**: Input readonly untuk menjaga konsistensi data

### Durasi dan Tanggal

-   **Durasi**: Input number 1-30 hari dengan validasi
-   **Check-in**: Date picker dengan event handler
-   **Check-out**: Readonly, dihitung otomatis

### Harga Otomatis

-   **Harga per Malam**: Diambil dari tipe kamar
-   **Total Harga**: Dihitung otomatis (harga × durasi)
-   **Informasi**: Ditampilkan dalam box biru

### Validasi Format

-   Regex pattern: `/^RSV\d{4}\d{2}\d{4}$/`
-   Memastikan format kode sesuai standar

### Parsing Kode

-   Extract tahun, bulan, dan nomor urut
-   Format bulan dalam bahasa Indonesia
-   Informasi lengkap tentang kode reservasi

## Keuntungan

1. **Otomatis**: Tidak perlu input manual kode reservasi
2. **Unik**: Setiap kode reservasi unik dan tidak duplikat
3. **Konsisten**: Format yang sama untuk semua reservasi
4. **Mudah Dibaca**: Informasi tahun, bulan, dan urutan jelas
5. **Durasi Fleksibel**: User dapat pilih durasi 1-30 hari
6. **Harga Akurat**: Harga otomatis berdasarkan tipe kamar
7. **Anti Overlap**: Mencegah double booking
8. **User Friendly**: Interface yang intuitif dan informatif

## Penggunaan

### Di Halaman Create

```php
// Kode otomatis di-generate saat mount
mount(function() {
    $this->kode_reservasi = ReservasiHelper::generateKodeReservasi();
});

// Fungsi hitung tanggal checkout
$hitungTanggalCheckout = function() {
    if ($this->tanggal_check_in && $this->durasi > 0) {
        $checkIn = \Carbon\Carbon::parse($this->tanggal_check_in);
        $this->tanggal_check_out = $checkIn->addDays($this->durasi)->format('Y-m-d');
        $this->hitungTotalHarga();
    }
};

// Fungsi hitung total harga
$hitungTotalHarga = function() {
    if ($this->kamar_id && $this->durasi > 0) {
        $kamar = $this->kamars->find($this->kamar_id);
        if ($kamar && $kamar->tipeKamar) {
            $this->harga_per_malam = $kamar->tipeKamar->harga ?? 0;
            $this->total_harga = $this->harga_per_malam * $this->durasi;
        }
    }
};

// Validasi overlap
$validasiOverlap = function() {
    // Logic validasi overlap
};
```

### Di Halaman Edit

```php
// Kode reservasi readonly untuk konsistensi
<input wire:model="kode_reservasi" readonly>

// Durasi dapat diubah untuk update tanggal checkout
<input wire:model="durasi" wire:change="hitungTanggalCheckout">
```

### Di Code Lain

```php
use App\Helpers\ReservasiHelper;

// Generate kode baru
$kode = ReservasiHelper::generateKodeReservasi();

// Validasi kode
$isValid = ReservasiHelper::validateKodeReservasi($kode);

// Parse informasi kode
$info = ReservasiHelper::parseKodeReservasi($kode);
```

## Event Handlers

### Create Page

-   `wire:change="hitungTanggalCheckout"` pada tanggal check-in dan durasi
-   `wire:change="hitungTotalHarga"` pada pemilihan kamar

### Edit Page

-   `wire:change="hitungTanggalCheckout"` pada tanggal check-in dan durasi
-   `wire:change="hitungTotalHarga"` pada pemilihan kamar

## Validasi

### Validasi Input

-   **Durasi**: Required, integer, min:1, max:30
-   **Tanggal Check-in**: Required, date, after_or_equal:today
-   **Tanggal Check-out**: Required, date, after:tanggal_check_in
-   **Kamar**: Required, exists:kamars,id
-   **Pelanggan**: Required, exists:pelanggans,id

### Validasi Overlap

-   Cek overlap dengan reservasi lain pada kamar yang sama
-   Exclude reservasi dengan status 'cancelled'
-   Exclude reservasi yang sedang diedit (untuk edit page)

## Maintenance

### Reset Nomor Urut

Nomor urut otomatis reset setiap bulan, sehingga:

-   Januari 2025: 0001, 0002, 0003, dst
-   Februari 2025: 0001, 0002, 0003, dst
-   Maret 2025: 0001, 0002, 0003, dst

### Backup dan Restore

Jika perlu reset nomor urut di tengah bulan, dapat:

1. Hapus reservasi terakhir
2. Atau gunakan tombol generate untuk generate kode baru

### Update Harga

Harga otomatis diambil dari tabel `tipe_kamars`, sehingga:

1. Update harga di tabel tipe kamar
2. Harga akan otomatis terupdate di form reservasi

## Troubleshooting

### Kode Duplikat

Jika terjadi kode duplikat:

1. Periksa validasi unique di database
2. Pastikan tidak ada race condition
3. Gunakan transaction untuk operasi create

### Format Tidak Sesuai

Jika format kode tidak sesuai:

1. Periksa regex pattern di `validateKodeReservasi()`
2. Pastikan format di `generateKodeReservasi()` konsisten
3. Test dengan berbagai skenario

### Harga Tidak Terhitung

Jika harga tidak terhitung otomatis:

1. Periksa relasi `kamar.tipeKamar.harga`
2. Pastikan data harga tersedia di database
3. Cek event handler `wire:change="hitungTotalHarga"`

### Overlap Tidak Terdeteksi

Jika overlap tidak terdeteksi:

1. Periksa logic validasi overlap
2. Pastikan query database berjalan dengan benar
3. Cek status reservasi yang di-exclude

## Update Terakhir

-   **Tanggal**: 26 Agustus 2025
-   **Versi**: 2.0.0
-   **Status**: Production Ready
-   **Fitur Baru**: Durasi otomatis, harga otomatis, validasi overlap
