<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('klasifikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('klasifikasi', 'kode_klasifikasi')) {
                $table->string('kode_klasifikasi')->nullable()->after('id');
            }
            if (!Schema::hasColumn('klasifikasi', 'nama_klasifikasi')) {
                $table->string('nama_klasifikasi')->nullable()->after('kode_klasifikasi');
            }
        });

        Schema::table('disposisi', function (Blueprint $table) {
            if (!Schema::hasColumn('disposisi', 'id_surat_masuk')) {
                $table->unsignedBigInteger('id_surat_masuk')->nullable()->after('id');
            }
            if (!Schema::hasColumn('disposisi', 'tujuan_disposisi')) {
                $table->string('tujuan_disposisi')->nullable()->after('id_surat_masuk');
            }
            if (!Schema::hasColumn('disposisi', 'isi_disposisi')) {
                $table->text('isi_disposisi')->nullable()->after('tujuan_disposisi');
            }
            if (!Schema::hasColumn('disposisi', 'sifat_disposisi')) {
                $table->string('sifat_disposisi')->nullable()->after('isi_disposisi');
            }
            if (!Schema::hasColumn('disposisi', 'batas_waktu')) {
                $table->date('batas_waktu')->nullable()->after('sifat_disposisi');
            }
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_keluar', 'no_agenda')) {
                $table->string('no_agenda')->nullable()->after('id');
            }
            if (!Schema::hasColumn('surat_keluar', 'no_surat')) {
                $table->string('no_surat')->nullable()->after('no_agenda');
            }
            if (!Schema::hasColumn('surat_keluar', 'tujuan_surat')) {
                $table->string('tujuan_surat')->nullable()->after('no_surat');
            }
            if (!Schema::hasColumn('surat_keluar', 'isi_ringkas')) {
                $table->text('isi_ringkas')->nullable()->after('tujuan_surat');
            }
            if (!Schema::hasColumn('surat_keluar', 'tgl_surat')) {
                $table->date('tgl_surat')->nullable()->after('isi_ringkas');
            }
            if (!Schema::hasColumn('surat_keluar', 'file_surat')) {
                $table->string('file_surat')->nullable()->after('tgl_surat');
            }
            if (!Schema::hasColumn('surat_keluar', 'keterangan')) {
                $table->string('keterangan')->nullable()->after('file_surat');
            }
            if (!Schema::hasColumn('surat_keluar', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('surat_keluar', 'status_proses')) {
                $table->string('status_proses')->default('baru')->after('user_id');
            }
            if (!Schema::hasColumn('surat_keluar', 'diproses_oleh')) {
                $table->unsignedBigInteger('diproses_oleh')->nullable()->after('status_proses');
            }
        });
    }

    public function down(): void
    {
        Schema::table('klasifikasi', function (Blueprint $table) {
            $table->dropColumn(['kode_klasifikasi', 'nama_klasifikasi']);
        });

        Schema::table('disposisi', function (Blueprint $table) {
            $table->dropColumn(['id_surat_masuk', 'tujuan_disposisi', 'isi_disposisi', 'sifat_disposisi', 'batas_waktu']);
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['no_agenda', 'no_surat', 'tujuan_surat', 'isi_ringkas', 'tgl_surat', 'file_surat', 'keterangan', 'user_id']);
        });
    }
};
