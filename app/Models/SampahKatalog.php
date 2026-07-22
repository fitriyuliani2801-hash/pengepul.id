<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SampahKatalog extends Model
{
    use HasFactory;
    protected $table = 'sampah_katalog';
    protected $fillable = ['nama_material', 'harga_beli_per_kg', 'icon'];
}
