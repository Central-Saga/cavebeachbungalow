# UserFactory Usage Examples

## Cara Penggunaan UserFactory

### 1. Membuat User dengan Role Spesifik

```php
// Membuat user admin
$admin = User::factory()->admin()->create();

// Membuat user owner
$owner = User::factory()->owner()->create();

// Membuat user pengunjung (otomatis buat data Pelanggan)
$pengunjung = User::factory()->pengunjung()->create();

// Membuat user dengan role custom
$user = User::factory()->withRole('Pengunjung')->create();
```

### 2. Membuat Multiple Users

```php
// Membuat 5 user admin
$admins = User::factory()->admin()->count(5)->create();

// Membuat 10 user pengunjung (otomatis buat data Pelanggan)
$pengunjungs = User::factory()->pengunjung()->count(10)->create();

// Membuat 3 user dengan role custom
$users = User::factory()->withRole('Pengunjung')->count(3)->create();
```

### 3. Membuat User dengan Data Custom

```php
// Membuat admin dengan email dan nama spesifik
$admin = User::factory()->admin()->create([
    'email' => 'admin@pondokputri.com',
    'name' => 'Administrator Pondok Putri'
]);

// Membuat pengunjung dengan data custom
$pengunjung = User::factory()->pengunjung()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);
```

### 4. Membuat User Tanpa Role (Default)

```php
// Membuat user tanpa role assignment
$user = User::factory()->create();

// Kemudian assign role secara manual jika diperlukan
$user->assignRole('Pengunjung');
```

### 5. Membuat User dengan State Combinations

```php
// Membuat user admin yang unverified
$admin = User::factory()->admin()->unverified()->create();

// Membuat user pengunjung dengan data custom
$pengunjung = User::factory()->pengunjung()->create([
    'name' => 'Jane Doe'
]);
```

## Fitur Otomatis

### Pelanggan Auto-Creation

-   Setiap kali menggunakan `->pengunjung()` atau `->withRole('Pengunjung')`, data Pelanggan akan otomatis dibuat
-   Data Pelanggan akan terhubung dengan User yang baru dibuat
-   Menggunakan PelangganFactory untuk data yang konsisten

### Role Assignment

-   Role akan otomatis di-assign saat user dibuat
-   Menggunakan Spatie Permission package
-   Role yang tersedia: Admin, Owner, Pengunjung

## Contoh dalam Seeder

```php
// Di UserSeeder
public function run(): void
{
    // Membuat user default
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'name' => 'Admin'
    ]);

    User::factory()->owner()->create([
        'email' => 'owner@example.com',
        'name' => 'Owner'
    ]);

    User::factory()->pengunjung()->create([
        'email' => 'pengunjung@example.com',
        'name' => 'Pengunjung'
    ]);

    // Membuat multiple pengunjung
    User::factory()->pengunjung()->count(20)->create();
}
```

## Testing

```php
// Di test
public function test_can_create_admin_user()
{
    $admin = User::factory()->admin()->create();

    $this->assertTrue($admin->hasRole('Admin'));
    $this->assertFalse($admin->hasRole('Pengunjung'));
}

public function test_can_create_pengunjung_with_pelanggan()
{
    $pengunjung = User::factory()->pengunjung()->create();

    $this->assertTrue($pengunjung->hasRole('Pengunjung'));
    $this->assertNotNull($pengunjung->pelanggan);
    $this->assertEquals($pengunjung->id, $pengunjung->pelanggan->user_id);
}
```
