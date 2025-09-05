<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'alamat',
        'kota',
        'jenis_kelamin',
        'tanggal_lahir',
        'telepon',
    ];

    /**
     * Get the user that owns the pelanggan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reservasi for the pelanggan.
     */
    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class);
    }

    /**
     * Get the nama lengkap from user relationship
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->user->name ?? 'Nama tidak tersedia';
    }

    /**
     * Scope a query to search pelanggans.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('telepon', 'like', '%' . $term . '%');
        });
    }
}
