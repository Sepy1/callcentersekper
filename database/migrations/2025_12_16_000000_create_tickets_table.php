<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket')->unique();
            $table->string('nama_pelapor');
            $table->string('hp')->nullable();
            $table->string('email')->nullable();
            $table->string('kategori');
            $table->string('judul');
            $table->text('detail');
            $table->text('closing_notes')->nullable();
            $table->text('qa_summary')->nullable();
            $table->string('status')->default('open');
            $table->string('officer')->nullable();
            $table->timestamp('waktu_eskalasi')->nullable();
            $table->string('tipe_pelapor')->nullable();
            $table->boolean('is_nasabah')->default(false);
            $table->string('id_ktp')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('alamat')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('kode_kantor')->nullable();
            $table->string('upload_ktp')->nullable();
            $table->string('upload_bukti')->nullable();
            $table->string('media_closing')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
