<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjemputanOrder extends Model
{
    use HasFactory;
    protected $table = 'penjemputan_orders';
    protected $fillable = [
        'order_no', 'user_id', 'status', 'latitude', 'longitude', 
        'jarak_km', 'biaya_jemput', 'tgl_jemput', 'jam_jemput', 
        'total_estimasi_harga', 'total_final_harga', 'driver_id', 'id_surat_keluar',
        'metode_pembayaran', 'status_pembayaran', 'bukti_transfer', 'catatan_pembayaran',
        'driver_latitude', 'driver_longitude'
    ];

    public function warga()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function suratTugas()
    {
        return $this->belongsTo(SuratKeluarModel::class, 'id_surat_keluar');
    }

    public function items()
    {
        return $this->hasMany(PenjemputanItem::class, 'order_id');
    }

    public function chats()
    {
        return $this->hasMany(OrderChat::class, 'order_id');
    }
}
