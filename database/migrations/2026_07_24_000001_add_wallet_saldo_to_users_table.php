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
        if (!Schema::hasColumn('users', 'saldo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('saldo', 15, 2)->default(0)->after('longitude');
            });
        }

        if (!Schema::hasTable('penarikan_saldo')) {
            Schema::create('penarikan_saldo', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('jumlah', 15, 2);
                $table->string('tujuan_pembayaran'); // Bank / E-Wallet
                $table->string('no_rekening');
                $table->string('status')->default('pending'); // pending, completed, rejected
                $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('penarikan_saldo');
        if (Schema::hasColumn('users', 'saldo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('saldo');
            });
        }
    }
};
