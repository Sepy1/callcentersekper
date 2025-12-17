<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketOfficerTable extends Migration
{
    public function up()
    {
        Schema::create('ticket_officer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('officer_id');
            $table->enum('status', ['assigned', 'proses_qa', 'cancel_qa'])->default('assigned');
            $table->text('tl')->nullable(); // kolom tindak lanjut officer
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('officer_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['ticket_id', 'officer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_officer');
    }
}
