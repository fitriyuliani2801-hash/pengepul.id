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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp')->nullable()->after('email');
            $table->text('alamat')->nullable()->after('no_hp');
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('bank_nama')->nullable()->after('longitude');
            $table->string('bank_nomor')->nullable()->after('bank_nama');
            $table->string('ewallet_nama')->nullable()->after('bank_nomor');
            $table->string('ewallet_nomor')->nullable()->after('ewallet_nama');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'no_hp', 'alamat', 'latitude', 'longitude', 
                'bank_nama', 'bank_nomor', 'ewallet_nama', 'ewallet_nomor'
            ]);
        });
    }
};
