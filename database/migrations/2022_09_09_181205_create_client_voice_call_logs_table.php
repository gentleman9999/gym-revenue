<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_voice_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_id');
            $table->unsignedBigInteger('user_id');
            $table->string('to');
            $table->string('conversation_id')->comment('The SID of twilio voice');
            $table->string('status');
            $table->json('payload');
            $table->timestamps();

            $table->foreign('gateway_id')->references('id')->on('gateway_providers');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_voice_call_logs');
    }
};
