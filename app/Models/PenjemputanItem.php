<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjemputanItem extends Model
{
    use HasFactory;
    protected $table = 'penjemputan_items';
    protected $fillable = ['order_id', 'material_id', 'estimasi_berat', 'final_berat', 'harga_beli_per_kg'];

    public function order()
    {
        return $this->belongsTo(PenjemputanOrder::class, 'order_id');
    }

    public function material()
    {
        return $this->belongsTo(SampahKatalog::class, 'material_id');
    }
}
