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
        Schema::table('surat_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuk', 'file_balasan')) {
                $table->string('file_balasan')->nullable()->after('file_surat');
            }
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_keluar', 'file_balasan')) {
                $table->string('file_balasan')->nullable()->after('file_surat');
            }
        });
    }

    public function down()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn('file_balasan');
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn('file_balasan');
        });
    }
};
