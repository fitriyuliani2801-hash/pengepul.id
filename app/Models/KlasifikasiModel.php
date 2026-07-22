<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiModel extends Model
{
 public $timestamps = false;

    use HasFactory;
    protected $table        = "klasifikasi";
    protected $primaryKey   = "id_klasifikasi";
    protected $fillable     = ['id_klasifikasi','kode_klasifikasi','nama_klasifikasi'];
}