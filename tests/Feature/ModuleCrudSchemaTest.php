<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ModuleCrudSchemaTest extends TestCase
{
    public function test_required_columns_exist_for_crud_modules(): void
    {
        $this->assertTrue(Schema::hasColumns('klasifikasi', ['kode_klasifikasi', 'nama_klasifikasi']));
        $this->assertTrue(Schema::hasColumns('disposisi', ['id_surat_masuk', 'tujuan_disposisi', 'isi_disposisi', 'sifat_disposisi', 'batas_waktu']));
        $this->assertTrue(Schema::hasColumns('surat_masuk', ['no_agenda', 'no_surat', 'asal_surat', 'isi_ringkas', 'tgl_surat', 'tgl_diterima', 'file_surat', 'keterangan', 'user_id']));
        $this->assertTrue(Schema::hasColumns('surat_keluar', ['no_agenda', 'no_surat', 'tujuan_surat', 'isi_ringkas', 'tgl_surat', 'file_surat', 'keterangan', 'user_id']));
    }
}
