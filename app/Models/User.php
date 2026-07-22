<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Field yang boleh diisi (mass assignable).
     */
    protected $fillable = [
        'name', 'email', 'role', 'status', 'password',
        'no_hp', 'alamat', 'latitude', 'longitude', 
        'bank_nama', 'bank_nomor', 'ewallet_nama', 'ewallet_nomor'
    ];

    /**
     * Field yang disembunyikan saat data user dikonversi ke JSON.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Relasi ke tabel Surat (One-to-Many).
     * Satu user bisa memiliki banyak surat pengaduan.
     */
    public function surat()
    {
        return $this->hasMany(SuratMasuk::class, 'user_id');
    }

    /**
     * Helper untuk mengecek apakah user adalah admin.
     * Digunakan untuk mempermudah logika di Controller/Blade.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasRole(...$roles)
    {
        return in_array($this->role, $roles, true);
    }
}
