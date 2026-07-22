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
        Schema::create('sampah_katalog', function (Blueprint $table) {
            $table->id();
            $table->string('nama_material');
            $table->integer('harga_beli_per_kg');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('penjemputan_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->unsignedBigInteger('user_id'); // Warga / customer
            $table->string('status')->default('pending'); // pending, scheduled, processing, completed, cancelled
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('jarak_km', 6, 2)->default(0.00);
            $table->integer('biaya_jemput')->default(0);
            $table->date('tgl_jemput');
            $table->string('jam_jemput');
            $table->integer('total_estimasi_harga')->default(0);
            $table->integer('total_final_harga')->default(0);
            $table->unsignedBigInteger('driver_id')->nullable(); // Driver / kurir
            $table->unsignedBigInteger('id_surat_keluar')->nullable(); // Link ke Surat Tugas / SPK
            $table->timestamps();
        });

        Schema::create('penjemputan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('estimasi_berat', 8, 2);
            $table->decimal('final_berat', 8, 2)->nullable();
            $table->integer('harga_beli_per_kg');
            $table->timestamps();
        });

        Schema::create('kas_pengepul', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('tipe_transaksi'); // pemasukan, pengeluaran
            $table->integer('jumlah_uang');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('stok_gudang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->decimal('jumlah_kg', 8, 2);
            $table->string('tipe_stok'); // masuk, keluar
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_gudang');
        Schema::dropIfExists('kas_pengepul');
        Schema::dropIfExists('penjemputan_items');
        Schema::dropIfExists('penjemputan_orders');
        Schema::dropIfExists('sampah_katalog');
    }
};
