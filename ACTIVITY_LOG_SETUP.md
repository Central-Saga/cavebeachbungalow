# Setup Activity Log - Cave Beach Bungalow

## 🚀 **Overview**

Sistem Activity Log yang baru menggunakan package `spatie/laravel-activitylog` untuk mencatat semua aktivitas user secara otomatis dan real-time di dashboard.

## 📦 **Package yang Digunakan**

### **Spatie Laravel Activity Log**

-   **Package**: `spatie/laravel-activitylog`
-   **Version**: ^4.10
-   **Fitur**: Automatic logging, customizable, efficient

## 🏗️ **Struktur yang Dibuat**

### 1. **Trait LogsActivity**

-   **Lokasi**: `app/Traits/LogsActivity.php`
-   **Fungsi**: Trait yang bisa digunakan di model untuk logging otomatis
-   **Fitur**:
    -   Log attributes yang berubah
    -   Custom log name
    -   Indonesian descriptions

### 2. **Model ActivityLog**

-   **Lokasi**: `app/Models/ActivityLog.php`
-   **Fungsi**: Model untuk mengakses dan menampilkan activity log
-   **Fitur**:
    -   Scopes untuk filtering
    -   Accessor untuk formatting
    -   Icon dan color berdasarkan event type

### 3. **Database Tables**

-   **activity_log**: Table utama untuk menyimpan log
-   **activity_log_event**: Table untuk event types
-   **activity_log_batch**: Table untuk batch operations

## 🔧 **Setup yang Telah Diterapkan**

### 1. **Install Package**

```bash
composer require spatie/laravel-activitylog
```

### 2. **Publish Files**

```bash
# Publish migrations
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# Publish config
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
```

### 3. **Run Migrations**

```bash
php artisan migrate
```

## 📝 **Model yang Sudah Di-setup**

### 1. **User Model**

```php
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use LogsActivity;

    protected $loggableAttributes = [
        'name',
        'email',
        'email_verified_at',
    ];

    protected $logName = 'User';
}
```

### 2. **Reservasi Model**

```php
use App\Traits\LogsActivity;

class Reservasi extends Model
{
    use LogsActivity;

    protected $loggableAttributes = [
        'kode_reservasi',
        'kamar_id',
        'pelanggan_id',
        'tanggal_check_in',
        'tanggal_check_out',
        'total_harga',
        'status_reservasi',
    ];

    protected $logName = 'Reservasi';
}
```

## 🎯 **Cara Menambahkan ke Model Lain**

### 1. **Import Trait**

```php
use App\Traits\LogsActivity;
```

### 2. **Use Trait**

```php
class Kamar extends Model
{
    use LogsActivity;
}
```

### 3. **Setup Properties**

```php
protected $loggableAttributes = [
    'nomor_kamar',
    'status',
    'tipe_kamar_id',
];

protected $logName = 'Kamar';
```

## 📊 **Dashboard Integration**

### 1. **Data yang Ditampilkan**

-   5 aktivitas terbaru
-   Event type (created, updated, deleted)
-   User yang melakukan aktivitas
-   Waktu aktivitas (diffForHumans)
-   Icon dan color berdasarkan event

### 2. **Real-time Updates**

-   Setiap kali ada perubahan di model
-   Otomatis muncul di dashboard
-   Tidak perlu refresh manual

## 🎨 **UI Features**

### 1. **Color Coding**

-   **Green**: Created events
-   **Blue**: Updated events
-   **Red**: Deleted events
-   **Gray**: Other events

### 2. **Icons**

-   **Plus**: Created
-   **Pencil**: Updated
-   **Trash**: Deleted
-   **Info**: Other

### 3. **Hover Effects**

-   Scale animation
-   Smooth transitions
-   Interactive feedback

## 🔍 **Cara Debug dan Testing**

### 1. **Check Database**

```sql
SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10;
```

### 2. **Check Model Logs**

```php
// Di tinker atau controller
$user = User::first();
$user->activities; // Get all activities for this user
```

### 3. **Check Specific Events**

```php
$activities = ActivityLog::byEvent('created')->recent(5)->get();
```

## 📈 **Performance Considerations**

### 1. **Efficient Logging**

-   Hanya log attributes yang berubah
-   Tidak log empty changes
-   Batch operations support

### 2. **Database Optimization**

-   Indexes pada created_at
-   Efficient queries dengan scopes
-   Pagination support

### 3. **Memory Management**

-   Limit jumlah log yang di-load
-   Lazy loading relationships
-   Cache support (optional)

## 🚀 **Advanced Features**

### 1. **Custom Logging**

```php
// Manual logging
activity()
    ->performedOn($model)
    ->causedBy(auth()->user())
    ->log('Custom activity message');
```

### 2. **Batch Operations**

```php
// Log multiple activities as batch
activity()
    ->inLog('batch-operation')
    ->performedOn($model)
    ->log('Batch update');
```

### 3. **Subject Logging**

```php
// Log changes to specific subject
activity()
    ->performedOn($reservasi)
    ->causedBy(auth()->user())
    ->log('Reservasi diperbarui');
```

## 🔧 **Configuration Options**

### 1. **Config File**

-   **Lokasi**: `config/activitylog.php`
-   **Fitur**:
    -   Enable/disable logging
    -   Custom table names
    -   Batch size limits
    -   Cleanup policies

### 2. **Environment Variables**

```env
ACTIVITY_LOGGER_ENABLED=true
ACTIVITY_LOGGER_DEFAULT_LOG_NAME=default
ACTIVITY_LOGGER_SUBJECT_TYPES=App\Models\User,App\Models\Reservasi
```

## 📋 **Monitoring dan Maintenance**

### 1. **Regular Cleanup**

```bash
# Clean old logs (older than 30 days)
php artisan activitylog:clean

# Clean specific log types
php artisan activitylog:clean --days=7 --log=User
```

### 2. **Performance Monitoring**

-   Monitor table size
-   Check query performance
-   Optimize indexes if needed

### 3. **Log Analysis**

-   Most active users
-   Most changed models
-   Peak activity times

## 🎯 **Next Steps**

### 1. **Add More Models**

-   Kamar model
-   Pelanggan model
-   TipeKamar model
-   FasilitasKamar model

### 2. **Enhanced UI**

-   Real-time updates dengan WebSockets
-   Filtering berdasarkan event type
-   Search functionality
-   Export to CSV/PDF

### 3. **Advanced Logging**

-   Log user sessions
-   Log failed login attempts
-   Log system events
-   Log API calls

## 💡 **Tips dan Best Practices**

### 1. **Choose Attributes Wisely**

-   Hanya log attributes yang penting
-   Hindari logging sensitive data
-   Log business logic changes

### 2. **Performance**

-   Monitor database size
-   Set up cleanup policies
-   Use efficient queries

### 3. **Security**

-   Sanitize log data
-   Control access to logs
-   Audit log access

## 🔗 **Useful Commands**

```bash
# Clean old logs
php artisan activitylog:clean

# List all log names
php artisan activitylog:list

# Check log statistics
php artisan activitylog:stats
```

## 📚 **Documentation Links**

-   [Spatie Laravel Activity Log](https://spatie.be/docs/laravel-activitylog)
-   [Laravel Documentation](https://laravel.com/docs)
-   [Activity Log API Reference](https://spatie.be/docs/laravel-activitylog/api)

---

**Sistem Activity Log sekarang sudah otomatis dan real-time!** 🎉

Setiap perubahan di model yang menggunakan trait `HasActivityLog` akan otomatis muncul di dashboard tanpa perlu refresh manual.
