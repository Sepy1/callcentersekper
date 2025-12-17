<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecycleTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('recycle_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('original_ticket_id')->nullable()->index();
            $table->json('data')->nullable(); // snapshot of ticket as JSON
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
            $table->string('deleted_ip',45)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recycle_tickets');
    }
}
