<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('ticket_id')->nullable()->index();
            $table->string('action', 150); // e.g. "ticket_created", "status_changed", "officer_assigned"
            $table->text('detail')->nullable(); // free-form detail / meta
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            // optional foreign keys (not required, keep nullable for flexibility)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
