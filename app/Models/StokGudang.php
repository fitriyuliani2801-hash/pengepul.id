<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokGudang extends Model
{
    use HasFactory;
    protected $table = 'stok_gudang';
    protected $fillable = ['material_id', 'jumlah_kg', 'tipe_stok', 'keterangan'];

    public function material()
    {
        return $this->belongsTo(SampahKatalog::class, 'material_id');
    }
}
