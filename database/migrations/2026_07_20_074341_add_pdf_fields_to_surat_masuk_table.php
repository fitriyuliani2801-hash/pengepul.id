<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuk', 'no_agenda')) {
                $table->string('no_agenda')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'no_surat')) {
                $table->string('no_surat')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'asal_surat')) {
                $table->string('asal_surat')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'isi_ringkas')) {
                $table->text('isi_ringkas')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'tgl_surat')) {
                $table->date('tgl_surat')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'tgl_diterima')) {
                $table->date('tgl_diterima')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'file_surat')) {
                $table->string('file_surat')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'keterangan')) {
                $table->string('keterangan')->nullable();
            }
            if (!Schema::hasColumn('surat_masuk', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn(['no_agenda', 'no_surat', 'asal_surat', 'isi_ringkas', 'tgl_surat', 'tgl_diterima', 'file_surat', 'keterangan', 'user_id']);
        });
    }
};
