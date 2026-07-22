<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE surat_masuk CHANGE id id_surat_masuk BIGINT UNSIGNED AUTO_INCREMENT');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE surat_keluar CHANGE id id_surat_keluar BIGINT UNSIGNED AUTO_INCREMENT');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE disposisi CHANGE id id_disposisi BIGINT UNSIGNED AUTO_INCREMENT');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE klasifikasi CHANGE id id_klasifikasi BIGINT UNSIGNED AUTO_INCREMENT');
    }

    public function down()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE klasifikasi CHANGE id_klasifikasi id BIGINT UNSIGNED AUTO_INCREMENT');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE disposisi CHANGE id_disposisi id BIGINT UNSIGNED AUTO_INCREMENT');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE surat_keluar CHANGE id_surat_keluar id BIGINT UNSIGNED AUTO_INCREMENT');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE surat_masuk CHANGE id_surat_masuk id BIGINT UNSIGNED AUTO_INCREMENT');
    }
};
