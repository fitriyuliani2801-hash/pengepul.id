<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'face_descriptor')) {
            Schema::table('users', function (Blueprint $table) {
                $table->longText('face_descriptor')->nullable()->after('password');
                $table->string('face_photo')->nullable()->after('face_descriptor');
            });
        }

        if (!Schema::hasTable('absensis')) {
            Schema::create('absensis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('tgl_absensi');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_pulang')->nullable();
                $table->string('foto_masuk')->nullable();
                $table->string('foto_pulang')->nullable();
                $table->enum('status_masuk', ['tepat_waktu', 'terlambat'])->default('tepat_waktu');
                $table->enum('status_pulang', ['tepat_waktu', 'pulang_cepat'])->default('tepat_waktu');
                $table->decimal('skor_kemiripan', 5, 2)->default(0);
                $table->decimal('latitude_masuk', 10, 8)->nullable();
                $table->decimal('longitude_masuk', 11, 8)->nullable();
                $table->decimal('latitude_pulang', 10, 8)->nullable();
                $table->decimal('longitude_pulang', 11, 8)->nullable();
                $table->string('catatan')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'tgl_absensi']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('absensis');
        if (Schema::hasColumn('users', 'face_descriptor')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['face_descriptor', 'face_photo']);
            });
        }
    }
};
