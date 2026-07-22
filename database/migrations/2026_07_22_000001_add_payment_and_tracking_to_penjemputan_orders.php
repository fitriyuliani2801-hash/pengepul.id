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
        Schema::table('penjemputan_orders', function (Blueprint $table) {
            $table->string('metode_pembayaran')->default('cash')->after('total_final_harga'); // cash, transfer
            $table->string('status_pembayaran')->default('pending')->after('metode_pembayaran'); // pending, paid
            $table->string('bukti_transfer')->nullable()->after('status_pembayaran');
            $table->text('catatan_pembayaran')->nullable()->after('bukti_transfer');
            $table->decimal('driver_latitude', 10, 8)->nullable()->after('catatan_pembayaran');
            $table->decimal('driver_longitude', 11, 8)->nullable()->after('driver_latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjemputan_orders', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pembayaran',
                'status_pembayaran',
                'bukti_transfer',
                'catatan_pembayaran',
                'driver_latitude',
                'driver_longitude'
            ]);
        });
    }
};
