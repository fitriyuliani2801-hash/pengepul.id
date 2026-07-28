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
        if (!Schema::hasTable('distribusi_suppliers')) {
            Schema::create('distribusi_suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('no_surat_jalan')->unique();
                $table->string('supplier_name');
                $table->foreignId('material_id')->constrained('sampah_katalog')->onDelete('cascade');
                $table->decimal('jumlah_kg', 12, 2);
                $table->decimal('harga_jual_per_kg', 15, 2);
                $table->decimal('total_pendapatan', 15, 2);
                $table->unsignedBigInteger('id_surat_keluar')->nullable();
                $table->string('keterangan')->nullable();
                $table->foreignId('diproses_oleh')->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribusi_suppliers');
    }
};
