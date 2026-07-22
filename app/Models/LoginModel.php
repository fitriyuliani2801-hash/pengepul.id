<?php

namespace App\Models;

// PENTING: Gunakan Authenticatable agar bisa login
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Pastikan kolom ini sesuai dengan database Anda
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', // Penting untuk membedakan Admin/User
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];
}