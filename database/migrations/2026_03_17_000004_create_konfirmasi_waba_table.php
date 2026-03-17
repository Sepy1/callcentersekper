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
        Schema::create('konfirmasi_waba', function (Blueprint $table) {
            $table->id();
            // nasabah_id as string to preserve leading zeros (e.g. 000000010)
            $table->string('nasabah_id', 50)->nullable()->unique();
            $table->string('nama_nasabah', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('hp', 50)->nullable();
            $table->string('waba', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konfirmasi_waba');
    }
};
