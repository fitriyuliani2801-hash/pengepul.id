<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiSupplier extends Model
{
    use HasFactory;

    protected $table = 'distribusi_suppliers';

    protected $fillable = [
        'no_surat_jalan',
        'supplier_name',
        'material_id',
        'jumlah_kg',
        'harga_jual_per_kg',
        'total_pendapatan',
        'id_surat_keluar',
        'keterangan',
        'diproses_oleh'
    ];

    public function material()
    {
        return $this->belongsTo(SampahKatalog::class, 'material_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluarModel::class, 'id_surat_keluar');
    }
}
