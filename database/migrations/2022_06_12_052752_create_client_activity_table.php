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
        Schema::create('client_activities', function (Blueprint $table) {
            $table->id('stored_event_id');
            $table->uuid('client_id');
            $table->string('entity');
            $table->string('entity_id');
            $table->string('operation');
            $table->integer('user_id')->nullable();
            $table->integer('api_user_id')->nullable();
            $table->string('access_token')->nullable();
            $table->timestamp('created_at');
            //computed autogenerated
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_activities');
    }
};