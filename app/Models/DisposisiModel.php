<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposisiModel extends Model
{
    public $timestamps = false;
    
    use HasFactory;
    protected $table        = "disposisi";
    protected $primaryKey   = "id_disposisi";
    protected $fillable     = ['id_disposisi','id_surat_masuk','tujuan_disposisi','isi_disposisi','sifat_disposisi','batas_waktu'];
}