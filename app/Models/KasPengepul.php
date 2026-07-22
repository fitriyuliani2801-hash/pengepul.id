<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasPengepul extends Model
{
    use HasFactory;
    protected $table = 'kas_pengepul';
    protected $fillable = ['order_id', 'tipe_transaksi', 'jumlah_uang', 'keterangan'];

    public function order()
    {
        return $this->belongsTo(PenjemputanOrder::class, 'order_id');
    }
}
