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
        if (Schema::hasTable('penarikan_saldo')) {
            Schema::table('penarikan_saldo', function (Blueprint $table) {
                if (!Schema::hasColumn('penarikan_saldo', 'reference_no')) {
                    $table->string('reference_no')->nullable()->after('no_rekening');
                }
                if (!Schema::hasColumn('penarikan_saldo', 'bank_ref_id')) {
                    $table->string('bank_ref_id')->nullable()->after('reference_no');
                }
                if (!Schema::hasColumn('penarikan_saldo', 'fee')) {
                    $table->decimal('fee', 15, 2)->default(0)->after('bank_ref_id');
                }
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
        if (Schema::hasTable('penarikan_saldo')) {
            Schema::table('penarikan_saldo', function (Blueprint $table) {
                $table->dropColumn(['reference_no', 'bank_ref_id', 'fee']);
            });
        }
    }
};
