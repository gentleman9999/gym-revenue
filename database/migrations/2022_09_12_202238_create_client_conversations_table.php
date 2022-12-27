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
        Schema::create('client_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id')->nullable();
            $table->uuid('gateway_provider_id');
            $table->uuid('user_conversation_id')->nullable();
            $table->string('conversation_id')->comment('Twilio Conversation SID');
            $table->timestamps();

            $table->unique(['gateway_provider_id', 'conversation_id']);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('gateway_provider_id')->references('id')->on('gateway_providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_conversations');
    }
};
