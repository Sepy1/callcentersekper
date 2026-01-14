<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('api_token_id')->nullable();
            $table->string('method', 10);
            $table->string('path');
            $table->text('headers')->nullable();
            $table->longText('request_body')->nullable();
            $table->string('ip')->nullable();
            $table->integer('response_status')->nullable();
            $table->longText('response_body')->nullable();
            $table->timestamps();

            $table->index('api_token_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_request_logs');
    }
};
