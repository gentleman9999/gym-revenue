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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('chat_id');
            $table->uuid('chat_participant_id');
            $table->text('message');
            $table->json('read_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chat_id')
                ->references('id')
                ->on('chats')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('chat_participant_id')
                ->references('id')
                ->on('chat_participants')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_details');
    }
};
