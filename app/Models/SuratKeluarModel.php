<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluarModel extends Model
{
     public $timestamps = false;
     
    use HasFactory;
    protected $table        = "surat_keluar";
    protected $primaryKey   = "id_surat_keluar";
    protected $fillable     = ['id_surat_keluar','no_agenda','no_surat','tujuan_surat','isi_ringkas','tgl_surat','file_surat','file_balasan','keterangan','user_id','status_proses','diproses_oleh'];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
