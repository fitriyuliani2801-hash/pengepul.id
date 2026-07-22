<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuk', 'status_proses')) {
                $table->string('status_proses')->default('baru')->after('user_id');
            }
            if (!Schema::hasColumn('surat_masuk', 'diproses_oleh')) {
                $table->unsignedBigInteger('diproses_oleh')->nullable()->after('status_proses');
            }
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
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
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn(['status_proses', 'diproses_oleh']);
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['status_proses', 'diproses_oleh']);
        });
    }
};
