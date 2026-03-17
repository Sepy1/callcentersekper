<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Change `nasabah_id` to a string type so it can store values with leading zeros.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('konfirmasi_waba')) {
            return;
        }

        // Drop foreign key if present (silent catch)
        try {
            DB::statement('ALTER TABLE konfirmasi_waba DROP FOREIGN KEY konfirmasi_waba_nasabah_id_foreign');
        } catch (\Throwable $e) {
            // ignore if fk not present or different name
        }

        // Modify column to VARCHAR(50) so leading zeros can be stored
        try {
            DB::statement('ALTER TABLE konfirmasi_waba MODIFY nasabah_id VARCHAR(50)');
        } catch (\Throwable $e) {
            // Some databases may require different SQL or the column may already be varchar
        }
    }

    /**
     * Reverse the migrations.
     * Try to convert nasabah_id back to unsigned BIGINT (best-effort).
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('konfirmasi_waba')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE konfirmasi_waba MODIFY nasabah_id BIGINT UNSIGNED');
        } catch (\Throwable $e) {
            // ignore if it fails
        }
    }
};
