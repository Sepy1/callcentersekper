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
        if (! Schema::hasTable('konfirmasi_waba')) {
            return;
        }

        Schema::table('konfirmasi_waba', function (Blueprint $table) {
            $table->date('tgl_register')->nullable()->after('nasabah_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('konfirmasi_waba')) {
            return;
        }

        Schema::table('konfirmasi_waba', function (Blueprint $table) {
            if (Schema::hasColumn('konfirmasi_waba', 'tgl_register')) {
                $table->dropColumn('tgl_register');
            }
        });
    }
};
